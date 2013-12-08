<?
// Colorights project
// Model: gets and saves from datastore

function doFetchColor () {
	return file_get_contents ("datastore/color.txt");
}

function doFetchMode () {
	return file_get_contents ("datastore/mode.txt");
}

// sets color file in datastore based on color input in format "#000000"
function doSaveColor ($color) {
	// save input
	$handle = fopen("datastore/color.txt", "w");
	fwrite ($handle, $color);
	fclose($handle);
}

// sets mode file in datastore based on inputted mode from $validModes
function doSaveMode ($mode) {
	// save input
	$handle = fopen("datastore/mode.txt", "w");
	fwrite ($handle, $mode);
	fclose($handle);
}


?>