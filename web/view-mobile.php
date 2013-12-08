<?
// Colorlight project
// Panels for view

require_once("controller.php");

if (array_key_exists("mode", $_GET) && checkValidMode($_GET["mode"]) )	
	loadPanel($_GET["mode"]);
else
	loadPanel(fetchMode());

function loadPanel ($mode) {
	//echo "{ 'mode': '". $mode ."',";
	//echo "'html': '";
	
	if ($mode == "picker")
		loadPickerPanel();
	elseif ($mode == "train")
		loadTrainPanel();
		
	//echo "'}";
}


function loadPickerPanel() {
	saveMode("picker");
	
	?>
	
	<h1>Colorlights</h1>
	<h2>Picker</h2>
	
	<form id="pickermodule" action="">
		<div class="form-item">
			<input type="text" id="color" name="color" value="#123456" />
		</div>
		<div id="picker"></div>
	</form>
	
	<?
	
}


function loadTrainPanel() {
	saveMode("train");
	
	$traindata = updateTrainData();
	?>

	<h1>Colorlights</h1>
	<h2>Next J</h2>
	<div id="trainColor" style="background-color:<? echo $traindata["color"]; ?>"></div>
	<p>Next train: <? echo $traindata["mins"]; ?> mins</p>
	
	<?
}

?>