<?

	$xml = file_get_contents("http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quote%20where%20symbol%20in%20(%22%5EGSPC%22)&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys");
	
	// todo: fail gracefully if no return
	
	$stockData = new SimpleXMLElement($xml);
	
	$volume = $stockData -> results -> quote[0] -> Volume;
	//print_r($stockData);

	echo $volume;
	
	echo "\n\n\n";
	
	$i = 1e6;
	print($i);
?>