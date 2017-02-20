<?php

	class CryptoPortfolio 
	{

		public static function GetMarketData()
		{

			// Get Config
			global $config;

			$marketApiUrl = "https://api.coinmarketcap.com/v1/ticker/";

			// Cache market data every 60 seconds
			$cacheFile = $config['cacheFile'];
			if (is_file($cacheFile) && ((time() - filemtime($cacheFile)) > 60) || trim(file_get_contents($cacheFile)) == false)
			{

				$contents = file_get_contents($marketApiUrl);
				file_put_contents($cacheFile, $contents);

			}
			elseif (!is_file($cacheFile)) {

				$contents = '';
				file_put_contents($cacheFile, $contents);

			}

			return file_get_contents($cacheFile);

		}

		public static function BuildPortfolio()
		{

			// Get Config
			global $config;

			$portfolioFile = 'config/portfolio.json';

			// Parse API Data
			$portfolioData = file_get_contents($portfolioFile);
			$marketData = self::GetMarketData();

			$portfolio = json_decode($portfolioData, true);
			$market = json_decode($marketData, true);

			// Loop through portfolio file adding estimated value based on
			// currency conversion provided by Google.
			$valuationAssets = array();
			if (is_array($portfolio) || is_object($portfolio))
			{
				foreach($portfolio as $assetKey => $assetValue)
				{

					$t = array_search($assetValue['id'], array_column($market, 'id'));

					$marketCapUSD = $market[$t]['market_cap_usd'];
					$priceUSD = $assetValue['amount'] * $market[$t]['price_usd'];

					$convertedMarketCapUSD = self::GoogleConvertCurrency('USD', $config['currency'], $marketCapUSD);
					$convertedPrice = self::GoogleConvertCurrency('USD', $config['currency'], $priceUSD);

					$assetValue['marketCap'] = round($convertedMarketCapUSD, 2);
					$assetValue['value'] = round($convertedPrice, 2);
					$assetValue['change'] = round(100 * (($assetValue['value'] - $assetValue['invested']) / $assetValue['invested']), 2);
					
					$valuationAssets[$assetKey] = $assetValue;

				}
			}

			return $valuationAssets;

		}

		public static function GoogleConvertCurrency($fromCurrency, $toCurrency, $amount) 
		{

			$amount = urlencode($amount);
			$fromCurrency = urlencode($fromCurrency);
			$toCurrency = urlencode($toCurrency);
			
			$timeout = 0;
			$url = "http://www.google.com/finance/converter?a=$amount&from=$fromCurrency&to=$toCurrency";
			$userAgent = "Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

			$ch = curl_init();			
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_USERAGENT, $userAgent);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$rawData = curl_exec($ch);
			curl_close($ch);

			$data = explode('bld>', $rawData);
			$data = explode($toCurrency, $data[1]);

			return $data[0];

		}

		public static function ExposePortfolio($expose = 'portfolio')
		{

			// Get Config
			global $config;
			
			// Running Total of Assets
			$totalInvestment = 0.00;
			$totalValue = 0.00;

			$valuationAssets = self::BuildPortfolio();
			foreach($valuationAssets as $assetKey => $assetValue)
			{


				$totalInvestment+= $assetValue['invested'];
				$totalValue+= $assetValue['value'];

				$assetValue['marketCap'] = $config['currencyPrefix'] . " " . number_format($assetValue['marketCap'], 2) . " " . $config['currency'];
				$assetValue['invested'] = $config['currencyPrefix'] . " " . number_format($assetValue['invested'], 2) . " " . $config['currency'];
				$assetValue['amount'] = number_format($assetValue['amount'], 12) . " " . $assetValue['symbol'];
				$assetValue['value'] = $config['currencyPrefix'] . " " . number_format($assetValue['value'], 2) . " " . $config['currency'];
				$assetValue['change'] = number_format($assetValue['change'], 2) . " %";
				
				$valuationAssets[$assetKey] = $assetValue;

			}

			$diff = ($totalValue >= $totalInvestment) ? "MoreOrEqual" : "Less"; 

			$totalInvestment = $config['currencyPrefix'] . " " . number_format($totalInvestment, 2) . " " . $config['currency'];
			$totalValue = $config['currencyPrefix'] . " " . number_format($totalValue, 2) . " " . $config['currency'];


			$output = "{\"Error\": \"Malformed Call!\"}";
			switch ($expose)
			{

			    case 'totals':
				$output = "{\"invested\": \"$totalInvestment\", \"value\": \"$totalValue\", \"diff\": \"$diff\"}";
				break;

			    case 'portfolio':
				$output = json_encode($valuationAssets);
				break;

			}

			return $output;

		}

	}

?>
