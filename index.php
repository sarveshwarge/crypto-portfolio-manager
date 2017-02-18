<?php

	$config = require_once(__DIR__.'/config/config.inc.php');
	require_once(__DIR__.'/classes/CryptoPortfolio.class.php');	
	
	if (isset($_GET['api']))
	{

		header('Content-Type: application/json');

		$output = CryptoPortfolio::ExposePortfolio($_GET['api']);
		if ($output == "{\"Error\": \"Malformed Call!\"}")
		{
			header("HTTP/1.0 404 Not Found");
		}

		echo $output;

	}
	else
	{

		include(__DIR__.'/templates/header.tpl.php');

		if (isset($_GET['page']))
		{
			$content = __DIR__.'/pages/'.$_GET['page'].'.pg.php';
			if (is_file($content))
			{

				include($content);

			}
			else
			{

				header("HTTP/1.0 404 Not Found");
				echo "<h1>Page Not Found!</h1>";

			}

		}
		else
		{

			include(__DIR__.'/pages/dashboard.pg.php');

		}

		include(__DIR__.'/templates/footer.tpl.php');

	}

?>
