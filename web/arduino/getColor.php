<?
// Colorlights project
// Send contents of color datastore file; in special folder because .htaccess allows this to be directly accessed apart from 301 rewries

chdir("../");
require_once("controller.php");

// update latest data
cron();

// get data
echo fetchColor();

?>
