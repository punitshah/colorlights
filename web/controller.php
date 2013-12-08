<?
// Colorlights project
// Implement changes from view

require_once("model.php");

$debug = 0;
$validModes = array ("picker", "train");


// interpret direct calls to the controller via GET
if (array_key_exists("c", $_GET)) {
	$command = $_GET["c"];
	if ($command == "pickerColor" && array_key_exists("new", $_GET))
		saveColor(urldecode($_GET["new"]));
	elseif ($command == "cron")
		cron();
}

// returns true if valid mode
function checkValidMode($mode) {
	global $validModes;
	return in_array($mode, $validModes);
}

// Cron task to enable repeated train information updates if needed
function cron () {
	if (fetchMode() == "train")
		updateTrainData();
}

function fetchColor () {
	return doFetchColor();
}

function fetchMode () {
	return doFetchMode();
}

// sets color file in datastore based on color input in format "#000000"
function saveColor ($color) {
	global $debug;
	
	// check input validity
	if (strlen($color) != 7) {
		if ($debug)
			echo "error 1: " . $color;
		return;
	}
	if (substr ($color, 0, 1) != "#") {
		if ($debug)
			echo "error 2: " . $color;
		return;
	}
	if (!ctype_xdigit (substr ($color, 1, 6))) {
		if ($debug)
			echo "error 3: " . $color;
		return;
	}
	
	return doSaveColor($color);
}

function saveMode ($mode) {	
	// check input validity
	if (!checkValidMode($mode)) {
		if ($debug)
			echo ("Error: Invalid mode");
		return;
	}
	
	return doSaveMode($mode);
}


// Get next J train time, convert to color, and then call saver
// input: none
// output: array with time to next bus, color
function updateTrainData () {
	global $debug;
	
	$xml = file_get_contents("http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&stopId=17073&routeTag=J");

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
	
	if ($debug)
		echo ("Color to display: $color");
	
	saveColor($color);
	
	return array("color" => $color, "mins" => $minstonextbus);
}



?>