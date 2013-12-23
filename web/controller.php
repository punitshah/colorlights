<?
// Colorlights project
// Implement changes from view

require_once("model.php");

$debug = 0;
$validModes = array ("picker", "train", "earthquake");


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
		$data = updateTrainData();
	if (fetchMode() == "earthquake")
		$data = updateEarthquakeData();
	
	saveColor($data["color"]);
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

// Get earthquake data for earthquakes close to Market & Church St. in SF
// input: none
// output: array with info on largest close recent earthquake, color
function updateEarthquakeData() {
	global $debug;
	$debugStr = "";
	
	$timeWindow = date(DATE_ISO8601, time()-60*60*24);
	
	$xml = file_get_contents("http://comcat.cr.usgs.gov/fdsnws/event/1/query?starttime=".$timeWindow."&latitude=37.7675&longitude=-122.4289&maxradiuskm=161&orderby=magnitude&limit=1");
	
	// TODO: refine this block to handle no earthquakes more gracefully	
	if ($xml == FALSE){
		// no XML returned - likely bc no earthquakes in area
		if ($debug)
			echo("Error - no XML returned");
		$color = "#ffffff";
		echo $color;
		return;
	}
	
	// get earthquake
	$eqEvent = new SimpleXMLElement($xml);
	$magnitude = floatval($eqEvent -> eventParameters -> event[0] -> magnitude -> mag -> value);

	// scale magnitude btwn green for <= 1 and red for >=4
	if ($magnitude <= 1)
		$colorScale = 0;
	elseif($magnitude >= 4)
		$colorScale = 1;
	else {	
		$colorScale = ($magnitude-1)/3;
	}
	
	// $debugStr .= "colorscale= ". $colorScale." magnitude = ". $magnitude;
	
	// get scaled colors to hex
	if ($colorScale > .5)
		$red = dechex(($colorScale-.5)*256/.5);
	else
		$red = 0;
	
	if ($colorScale < .5)
		$green = dechex(($colorScale)*256/.5);
	else
		$green = dechex((1-$colorScale)*256/.5);
	
	if ($colorScale < .5)
		$blue = dechex((.5-$colorScale)*256/.5);
	else
		$blue = 0;
	
	// convert to hex
	if (strlen($red) == 1)
		$red = "0" . $red;
	if (strlen($green) == 1)
		$green = "0" . $green;
	if (strlen($blue) == 1)
		$blue = "0" . $blue;
	
	$color = "#".$red.$green.$blue;
		
	return array("color" => $color, "magnitude" => $magnitude, "debug" => $debugStr);
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
		
	return array("color" => $color, "mins" => $minstonextbus);
}



?>