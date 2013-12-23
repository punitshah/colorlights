<?
// Colorlight project
// Outputs JSON for panels for view

require_once("controller.php");

if (array_key_exists("mode", $_GET) && checkValidMode($_GET["mode"]) )	
	loadPanel($_GET["mode"]);
else
	loadPanel(fetchMode());

function loadPanel ($mode) {
	header('Content-type: application/json');
	
	if ($mode == "picker")
		$html = loadPickerPanel();
	elseif ($mode == "train")
		$html = loadTrainPanel();
	elseif ($mode == "earthquake")
		$html = loadEarthquake();
	elseif ($mode == "sp500vol")
		$html = loadSP500VolPanel();
		
	// todo: optimize functions to return color data directly
	echo json_encode(array("mode" => $mode, "color" => fetchColor(), "html" => $html));
}


function loadEarthquake() {
	saveMode("earthquake");
	
	$eqdata = updateEarthquakeData();
	
	return "
	
	<h1>Colorlights</h1>
	<h2>Earthquakes</h2>
	<div id='colorPreview' style='background-color:{$eqdata['color']}'></div>
	<p>Magnitude for largest earthquake within 100mi of Church and Market in past 24 hours: {$eqdata['magnitude']}</p>
	
	";
}


function loadPickerPanel() {
	saveMode("picker");
	
	return "
	
	<h1>Colorlights</h1>
	<h2>Picker</h2>

	<form id='pickermodule' action=''>
		<div class='form-item'>
			<input type='text' id='color' name='color' value='". fetchColor() ."' />
		</div>
		<div id='picker'></div>
	</form>
	
	";
	
}


function loadSP500VolPanel() {
	saveMode("sp500vol");
	
	$voldata = updateSP500VolData();
	
	return "
	
	<h1>Colorlights</h1>
	<h2>S&amp;P500 Volume</h2>
	<div id='colorPreview' style='background-color:{$voldata['color']}'></div>
	<p>Volume in last trading day (?): ".number_format($voldata['volume'])."</p>
	
	";
}


function loadTrainPanel() {
	saveMode("train");
	
	$traindata = updateTrainData();
	
	if ($traindata['mins'] == -1)
		$traindata['mins'] = "none 'till the morning";
	else
		$traindata['mins'] = $traindata['mins'] . " mins";
	
	return "

	<h1>Colorlights</h1>
	<h2>Next J</h2>
	<div id='colorPreview' style='background-color:{$traindata['color']}'></div>
	<p>Next train: {$traindata['mins']}</p>
	
	";
}


?>