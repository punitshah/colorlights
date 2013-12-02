<?php

error_reporting(E_ALL);

echo("start\n");

//$xml = file_get_contents("http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&stopId=17073&routeTag=J");
$xml = file_get_contents("http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&stopId=14269&routeTag=38AX");
//$xml = file_get_contents("nextjdummydata.xml");



//echo ("\nxml is: " . $xml ."\n");


if ($xml == FALSE){
	echo("Error - no XML returned");
	return;
}


$timings = new SimpleXMLElement($xml);

//var_dump($timings["predictions"]["direction"]["prediction"]);
if($timings -> predictions -> attributes() -> dirTitleBecauseNoPredictions == NULL)
	$minstonextbus = -1;
else
	$minstonextbus  = $timings -> predictions -> direction -> prediction -> attributes() -> minutes;


//$minstonextbus = $timings -> {'body'} -> predictions -> direction -> prediction[0]["minutes"];
//$minstonextbus = $timings -> predictions -> prediction -> {'0'} -> {'@attributes'} -> minutes;

echo($minstonextbus);

// assign colors
if ($minstonextbus >= 5)
	$color = "#00ff00"; //green
elseif ($minstonextbus >= 2 && $minstonextbus < 5)
	$color = "#ffa500"; //orange
else
	$color = "#ff0000"; //red

echo $color;


?>