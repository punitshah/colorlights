<?
// Colorlight project
// Main view

require_once("controller.php");
//require_onve("view-mobile.php");

$modeOnLoad = fetchMode();
?>

<!doctype html>
<html>
<head>
	<title>
		Colorlight selector
	</title>
		
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
	
	<script type="text/javascript" src="lib/farbtastic/farbtastic.js"></script>
	<link rel="stylesheet" href="lib/farbtastic/farbtastic.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="lib/style.css" />
	
	<link href='http://fonts.googleapis.com/css?family=Signika+Negative' rel='stylesheet' type='text/css'>
	
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			//alert ("test");
			// load the appropriate panel
			setPanel(<? echo $modeOnLoad; ?>);
			//alert ("after setPanel called with mode of: " + mode);
		});
		
		
		function setPanel(mode) {
			alert ("setPanel called ");//with mode of: " + mode);
			/*$.ajax({
				url: "view-mobile.php?mode=" + mode
			}).done(function(data){
				$("#content").html(data);
				
				// instantiate the color picker, if applicable
				if (mode == "picker") {
					$('#picker').farbtastic(function(color) {
						$('#color').val(color);
						// TODO: need to make text box change color too, or have some larger preview box, and then load the item with an initial
						var jqxhr = $.ajax( "setColor.php?new="+ encodeURIComponent(color) );
					});
				}
			}).fail(
				alert("fail");
			);
			
			*/
		}
		
	</script>
	
	<meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=0">
</head>

<body>
<div id="content">
	
	
	
	
	
	<!--<div id="pickerPanel"<? if ($modeOnLoad != "picker") {?> style="visibility:hidden;" <? } ?> >
		<h1>Colorlight</h1>
		<h2>Picker</h2>
	
		<form id="pickermodule" action="">
			<div class="form-item">
				<input type="text" id="color" name="color" value="#123456" />
			</div>
			<div id="picker"></div>
		</form>
	</div>
	<div id="trainPanel" <? if ($modeOnLoad != "train") echo 'style="visibility:hidden;"';?>>
		<?php $traindata = updateTrainData(); ?>
		<h1>Colorlight</h1>
		<h2>Next J</h2>
		
		Next train: <? $traindata["mins"] ?> <br><br>
		<div id="trainColor" style="color:<? $traindata["color"] ?>"></div>
	</div>-->
</div> <!-- content -->



<footer data-position="fixed">
	<div data-role="navbar">
    	<ul>
			<li><a onClick="setMode.php?new=picker" href="#" <? if($modeOnLoad == "picker") { ?> class="ui-btn-active" <? } ?> >Picker</a></li>
			<li><a href="#" <? if($modeOnLoad == "train") { ?> class="ui-btn-active" <? } ?> >J Train</a></li>
		</ul>
	</div><!-- /navbar -->
<footer><!-- /footer -->
</body>
</html>
