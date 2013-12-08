<?
// Colorlights project
// Send contents of color datastore file; in special folder because .htaccess allows this to be directly accessed apart from 301 rewries

require_once("../controller.php");
chdir ( "../" );

echo fetchColor();

?>
