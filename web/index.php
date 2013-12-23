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
	
	<script src="lib/ga.js"></script>
	
	<script type="text/javascript" charset="utf-8">
		var mode = "<? echo $modeOnLoad; ?>";
		
		$(document).ready(function() {
			// load the appropriate panel
			changePanel("<? echo $modeOnLoad; ?>");
			
			// bind clicks to nav bar to re-loading panel
			$("#pickerNav").bind("click", function(){
				changePanel("picker");
			});
			$("#trainNav").bind("click", function(){
				changePanel("train");
			});
			
			$("#earthquakeNav").bind("click", function(){
				changePanel("earthquake");
			});

			$("#sp500volNav").bind("click", function(){
				changePanel("sp500vol");
			});			
			
		});		
		
		function changePanel (newMode) {
			mode = newMode;
			showLoadingOverlay();
			setPanel(newMode);
		}
		
		function setPanel(newMode) {			
			$.ajax({
				url: "view-mobile.php?mode=" + mode,
				success: function(data){
					// check we don't have overlapping AJAX requests out due to a change in mode btwn requests
					if (data.mode != mode)
						return;
						
					$("#content").html(data.html);
					hideLoadingOverlay();
					
					// instantiate panel-specific components
					if (mode == "picker") {
						// instantiate color picker
						$("#picker").farbtastic(function(color) {
							$("#color").val(color);
							// TODO: need to make text box change color too, or have some larger preview box, and then load the item with an initial
							var jqxhr = $.ajax( "controller.php?c=pickerColor&new="+ encodeURIComponent(color));
						});
						
						$.farbtastic("#picker").setColor(data.color);
					}					
				},
				dataType: "json",
				
				// set complete and timeout if set to train, where refreshing is needed
				complete: function(){
					if(mode == "train")
						setPanel("train");
					if(mode == "earthquake")
						setPanel("earthquake");
					if(mode== "sp500vol")
						setPanel("sp500vol");
				},
				timeout: 50000
			});
		}	
		
		function showLoadingOverlay() {
			$(".loadingoverlay").show();
		}
		
		function hideLoadingOverlay() {
			$(".loadingoverlay").hide();
		}
		
	</script>
	
	<meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=0">
</head>

<body>
<div class="loadingoverlay"><div id="loadingicon"></div></div>
<div data-role="header" data-position="fixed">
	<h1>Colorlights</h1>
	<a href="#nav-panel" data-icon="bars" data-iconpos="notext">Menu</a>
	<a href="#add-form" data-icon="gear" data-iconpos="notext" data-position="right">Add</a>
</div><!-- /header -->
<div data-role="panel" data-position-fixed="true" data-display="push" id="nav-panel">
    <ul data-role="listview">
            <li data-icon="delete"><a href="#" data-rel="close">Close menu</a></li>
			<li><a href="#panel-fixed-page2">Accordion</a></li>
			<li><a href="#panel-fixed-page2">AJAX Navigation</a></li>
			<li><a href="#panel-fixed-page2">Autocomplete</a></li>
			<li><a href="#panel-fixed-page2">Buttons</a></li>
        </ul>
</div><!-- /panel -->
<div id="content"></div>

<footer data-position="fixed">
	<div class="loadingoverlay"></div>
	<div data-role="navbar">
    	<ul>
			<li><a id="pickerNav" 		href="#" <? if($modeOnLoad == "picker") 	{ ?> class="ui-btn-active" <? } ?> >Picker<br>&nbsp;</a></li>
			<li><a id="trainNav"  		href="#" <? if($modeOnLoad == "train") 		{ ?> class="ui-btn-active" <? } ?> >Next J<br>&nbsp;</a></li>
			<li><a id="earthquakeNav"  	href="#" <? if($modeOnLoad == "earthquake") { ?> class="ui-btn-active" <? } ?> >Earth-<br>quake</a></li>
			<li><a id="sp500volNav"  	href="#" <? if($modeOnLoad == "sp500vol") 	{ ?> class="ui-btn-active" <? } ?> >S&amp;P 500<br>Volume</a></li>

		</ul>
	</div><!-- /navbar -->
<footer><!-- /footer -->

</body>
</html>
