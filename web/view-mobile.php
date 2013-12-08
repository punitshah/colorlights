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
		
	echo json_encode(array("mode" => $mode, "color" => fetchColor(), "html" => $html));
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


function loadTrainPanel() {
	saveMode("train");
	
	$traindata = updateTrainData();
	
	return "

	<h1>Colorlights</h1>
	<h2>Next J</h2>
	<div id='trainColor' style='background-color:{$traindata['color']}'></div>
	<p>Next train: {$traindata['mins']} mins</p>
	
	";
}

?>