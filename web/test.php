	<?
	$timeWindow = date(DATE_ISO8601, time()-60*60*24*7);
	
	$xml = file_get_contents("http://comcat.cr.usgs.gov/fdsnws/event/1/query?starttime=".$timeWindow."&latitude=37.7675&longitude=-122.4289&maxradiuskm=161&orderby=magnitude&limit=1");
		

	//echo $xml;
	
	// get earthquake
	$eqEvent = new SimpleXMLElement($xml);
	
	
	//print_r($eqEvent);
	
	
	$magnitude = $eqEvent -> eventParameters -> event[0] -> magnitude -> mag -> value;
	
	echo( $magnitude) ;
	
	echo "\n\n";
	
	echo date(DATE_ISO8601, time()-60*60);
	
	?>