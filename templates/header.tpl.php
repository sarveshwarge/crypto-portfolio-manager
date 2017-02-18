<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $config['portfolioTitle']; ?> | Cryptocurrency Portfolio Manager</title>
		<link href="<?php echo rtrim($config['baseURL'], '/'); ?>/static_assets/img/favicon.png" rel="icon" type="image/png" sizes="16x16">
		<link href="<?php echo rtrim($config['baseURL'], '/'); ?>/bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
		<link href="<?php echo rtrim($config['baseURL'], '/'); ?>/bower_components/font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="<?php echo rtrim($config['baseURL'], '/'); ?>/bower_components/datatables.net-bs/css/dataTables.bootstrap.css" rel="stylesheet">
		<link href="<?php echo rtrim($config['baseURL'], '/'); ?>/bower_components/datatables.net-buttons-bs/css/buttons.bootstrap.css" rel="stylesheet">
		<link href="<?php echo rtrim($config['baseURL'], '/'); ?>/static_assets/css/portfolio.css" rel="stylesheet">
	</head>
	<body style="padding-top: 65px;">
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="<?php echo $config['baseURL']; ?>"><?php echo $config['portfolioTitle']; ?></a>

				</div>
				<span id="navbar-slogan" class="navbar-brand navbar-right hidden-xs">"<?php echo $config['inspirationalQuote']; ?>"</span>
			</div>
		</nav>
		<div class="container">
			<header>
				<p>Currency conversion provided by <a target="_blank" href="https://www.google.co.uk/finance/converter">Google</a> and Market Data provided by <a target="_blank" href="<?php echo $marketUrl; ?>">CoinMarketCap</a>.</p>
			</header>
			<hr>
