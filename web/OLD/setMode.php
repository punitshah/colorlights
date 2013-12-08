<?
// Colorlights project
// sets mode file in datastore based on inputted mode in GET parameter "new"

$validModes = array ("picker", "train");

if (array_key_exists("new", $_GET))
	saveMode(urldecode($_GET["new"]));

function saveMode($newMode) {
	// check input validity
	if (!in_array($newMode, $validModes))
		die();

	// save input
	$handle = fopen("datastore/mode.txt", "w");
	fwrite ($handle, $newMode);
	fclose($handle);

}
?>