<?
// Colorlight project
// Automatically reloading page (using js) to perform cron task; just open in browser

require_once("controller.php");

// call cron task
cron();
?>

<!doctype html>
<html>
<head>
<title>Colorlights auto-cron</title>
<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>
</head>
<body onload="JavaScript:timedRefresh(10000);"></body>
</html>