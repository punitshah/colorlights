<?
// Colorlight project
// Panels for view

require_once("controller.php");

if (array_key_exists("mode", $_GET) && checkValidMode($_GET["mode"]) )	
	loadPanel($_GET["mode"]);
else
	loadPanel(fetchMode());


function loadPanel ($mode) {
	if ($mode == "picker")
		loadPickerPanel();
	elseif ($mode == "train")
		loadTrainPanel();
}


function loadPickerPanel() {
	?>
	
	<h1>Colorlight</h1>
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
	$traindata = updateTrainData();
	?>

	<h1>Colorlight</h1>
	<h2>Next J</h2>
	
	Next train: <? echo $traindata["mins"]; ?> <br><br>
	<div id="trainColor" style="color:<? echo $traindata["color"]; ?>"></div>
	
	<?
}




?>