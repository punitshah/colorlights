<?php

error_reporting(E_ALL);

$debug = 1;

$xml = file_get_contents("http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&stopId=17073&routeTag=J");
//$xml = file_get_contents("http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&stopId=14269&routeTag=38AX");
//$xml = file_get_contents("./nextjdummydata.xml");



if ($xml == FALSE){
	if ($debug)
		echo("Error - no XML returned");
	$color = "#ffffff";
	echo $color;
	return;
}

// get prediction
$timings = new SimpleXMLElement($xml);

if($timings -> predictions -> attributes() -> dirTitleBecauseNoPredictions == NULL)
	$minstonextbus  = $timings -> predictions -> direction -> prediction -> attributes() -> minutes; // line still running
else
	$minstonextbus = -1; // line not running at this hour

if ($debug)
	echo("Mins to next bus (-1 if not running): $minstonextbus <br>");

// assign colors
if ($minstonextbus == -1)
	$color = "#ffffff"; //white - bus line is off
elseif ($minstonextbus >= 15)
	$color = "#9d00f5"; //purple
elseif ($minstonextbus >= 10 && $minstonextbus < 15)
	$color = "#0000ff"; //blue
elseif ($minstonextbus >= 7 && $minstonextbus < 10)
	$color = "#00f5ed"; //teal
elseif ($minstonextbus >= 5 && $minstonextbus < 7)
	$color = "#00ff00"; //green
elseif ($minstonextbus >= 3 && $minstonextbus < 5)
	$color = "#ffff00"; //yellow
elseif ($minstonextbus >= 2 && $minstonextbus < 3)
	$color = "#f52600"; //orange
elseif ($minstonextbus >= 0 && $minstonextbus < 2)
	$color = "#ff0000"; //red

echo ("Color to display: $color");

require_once("setColor.php");
saveColor($color);

?>