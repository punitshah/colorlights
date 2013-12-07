<?
// Colorlights project
// sets color file in datastore based on inputted color in GET parameter "new". Color input in format "#000000"

if (array_key_exists("new", $_GET))
	saveColor(urldecode($_GET["new"]));

function saveColor($newColor) {
	// check input validity
	if (strlen($newColor) != 7) {
		echo "error 1: " . $newColor;
		die();
	}
	if (substr ($newColor, 0, 1) != "#") {
		echo "error 2: " . $newColor;
		die();
	}
	if (!ctype_xdigit (substr ($newColor, 1, 6))) {
		echo "error 3: " . $newColor;
		die();
	}

	// save input
	$handle = fopen("datastore/color.txt", "w");
	fwrite ($handle, $newColor);
	fclose($handle);

}
?>