<?
// Colorlight project
// Main view

require_once("controller.php");

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
			// load the appropriate panel
			setPanel("<? echo $modeOnLoad; ?>");
			
			// bind clicks to nav bar to re-loading panel
			$("#pickerNav").bind("click", function(){
				setPanel("picker");
			});
			$("#trainNav").bind("click", function(){
				setPanel("train");
			});
			
		});
		
		
		function setPanel(mode) {
			//alert ("setPanel called with mode of: " + mode);
			$.ajax({
				url: "view-mobile.php?mode=" + mode
			}).done(function(data){
				$("#content").html(data);
				
				// instantiate the color picker, if applicable
				if (mode == "picker") {
					$('#picker').farbtastic(function(color) {
						$('#color').val(color);
						// TODO: need to make text box change color too, or have some larger preview box, and then load the item with an initial
						var jqxhr = $.ajax( "controller.php?c=pickerColor&new="+ encodeURIComponent(color) );
					});
				}
			});
		}
		
		
	</script>
	
	<meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=0">
</head>

<body>
<div id="content"></div>

<footer data-position="fixed">
	<div data-role="navbar">
    	<ul>
			<li><a id="pickerNav" href="#" <? if($modeOnLoad == "picker") { ?> class="ui-btn-active" <? } ?> >Picker</a></li>
			<li><a id="trainNav"  href="#" <? if($modeOnLoad == "train") { ?> class="ui-btn-active" <? } ?> >Next J</a></li>
		</ul>
	</div><!-- /navbar -->
<footer><!-- /footer -->
</body>
</html>
