<?php
require_once dirname( __FILE__ ) . '/everyblock/EveryblockRequest.php';

class EveryblockLocalInfoWidget {
	protected $schemaObj;
	protected $everyblockRequest;
	protected $neighborhoodDropdownEntries;
	protected $metro;
	protected $schema;
	protected $width = 300;
	protected $height = 500;
	
	public function EveryblockLocalInfoWidget($metro, $schema, $width = 300, $height = 500) {
		if(!isset($metro) || !isset($schema)) { 
			die("No metro and / or schema!  Widget will not work without supplying those!"); 
		}
		
		$this->metro = preg_replace("/[^a-zA-Z0-9-_]/", "", strip_tags($metro));
		$this->schema = preg_replace("/[^a-zA-Z0-9-_]/", "", strip_tags($schema));
		
		if(isset($width)) {
			$width = preg_replace("/[^a-zA-Z0-9]/", "", strip_tags($width));
			if(intval($width) > 0) $this->width = $width;
			else $this->width = 300;
		}
		
		if(isset($height)) {
			$height = preg_replace("/[^a-zA-Z0-9]/", "", strip_tags($height));
			if(intval($height) > 0) $this->height = $height;
			else $this->height = 500;
		}
		
		$this->everyblockRequest = new EveryblockLocalInfoRequest();
		
		$this->schemaObj = $this->generate_schema_object();
		$this->metroObj = $this->generate_metro_object();
	}
		
	protected function generate_schema_object() {
		$xml = $this->everyblockRequest->getSchema($this->metro);
		
		if($xml === false) {
			return false;
		}
		
		foreach($xml as $element) {  
			if($element->slug == $this->schema) {
				return $element;
			}
		}
		return null;
	}
	
	protected function generate_metro_object() {
		return $this->everyblockRequest->getMetro($this->metro);
	}
	
	public function getEmbedCode() {
	?>
		<html>
			<head>
				<title>
					<?php if(isset($this->metroObj) && isset($this->metroObj->city_name)) { echo ($this->metroObj->city_name . " "); } ?><?php if(isset($this->schemaObj) && isset($this->schemaObj->plural_name)) { echo(ucwords($this->schemaObj->plural_name)); } else { echo('Widget'); } ?>
				</title>
				<link rel="stylesheet" href="./style.css" type="text/css" />
			</head>
			<body>
				<div class="everyblock_widget_container" style="width:<?php echo(strip_tags($this->width));?>px;height:<?php echo(strip_tags($this->height)); ?>px;overflow:hidden;position:absolute;">
					<div id="header" style="" class="everyblock_widget_header">
						<div id="flag" class="everyblock_widget_flag">
							<a href="<?php if(isset($this->schemaObj) && isset($this->schemaObj->about)) { echo(strip_tags($this->schemaObj->about)); } else { echo(strip_tags("http://" . $this->metro . ".everyblock.com/" . $schema . "/")); } ?>" target="_blank" style="color:white; font-size: 20px; text-decoration: none;" class="everyblock_clickthrough" id="everyblock_clickthrough"><?php if(isset($this->metroObj) && isset($this->metroObj->city_name)) { echo (strip_tags($this->metroObj->city_name . " ")); } ?><?php if(isset($this->schemaObj->plural_name)) { echo(ucwords(strip_tags($this->schemaObj->plural_name))); } else { echo('Widget'); } ?></a>
							<em>Powered by <img src="https://static.everyblock.com/images/widget/logo.14px.png" height="14" alt="EveryBlock"></em>
						</div>
						<div id="selection" class="everyblock_widget_selection">
							<form>
								<select id="neighborhood" name="neighborhood" onchange="lookupNeighborhood()" style="pointer-events: none; cursor: default;">
									<option value="loading">Loading...</option>
								</select>
							</form>
						</div>
					</div>
					<div id="content" class="everyblock_widget_content" style="overflow-y:auto;height:<?php echo($this->height - 108 - 45); ?>px;">
						<div class="everyblock_widget_container" id="entries_content">
						</div>
					</div>
						<div class="everyblock_widget_teaser" id="teaser" style="position:absolute;bottom:0px;width:100%;height:20px;">
							<a href="http://<?php echo(strip_tags($this->metro)); ?>.everyblock.net/top/?utm_source=embed&utm_medium=embed&utm_campaign=wordpress" target="_blank">Read more news on EveryBlock &rarr;</a>
						</div>
					<script type="text/javascript">
					
						if(typeof console === "undefined"){
							console = { log: function() { } };
						}
					  
						function doAjaxRequest(call, params, callback) {
							var xmlhttp;
							
							params.call = call;
							params.type = "json";
							params.process = "false";
							
							var stringParams = "";
							for(var param in params) {
								var obj = params[param];
								stringParams += param + "=" + obj + "&";
							}
							
							if (window.XMLHttpRequest) {
								xmlhttp=new XMLHttpRequest();
							} else {
								xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
							}
							
							xmlhttp.onreadystatechange=function() {
								if (xmlhttp.readyState==4) {
									if(xmlhttp.status==200 && xmlhttp.responseText != '') {
										try {
											callback(JSON.parse(xmlhttp.responseText));
										} catch(err) {
											console.log('a horrible error ' + err);
										}
										
									}
								}
							}
							
							xmlhttp.open("POST","./everyblock/EveryblockRequest.php",true);
							xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							xmlhttp.setRequestHeader("Content-length", stringParams.length);
							xmlhttp.setRequestHeader("Connection", "close");
							xmlhttp.send(stringParams);
						}
						
						function dropdownCallback(data) {
							document.getElementById('neighborhood').options.length = 0;
							document.getElementById('neighborhood').options[0] = new Option('-- Neighborhood --', 'all');
							data.forEach(function(element, index, array) {
								document.getElementById('neighborhood').options[index+1] = new Option(element.name, element.slug);
							});
							document.getElementById('neighborhood').style.pointerEvents = "auto";
							document.getElementById('neighborhood').style.cursor = "auto";
							
							
						}
						
						function lookupNeighborhood() {
							var dropdown = document.getElementById('neighborhood');
							var neighborhood = dropdown.options[dropdown.selectedIndex].value;
							
							var clickthrough = document.getElementById("everyblock_clickthrough");
							
							if(neighborhood != "loading" && neighborhood != "all") {
								clickthrough.setAttribute("href", "<?php echo('http://' . strip_tags($this->metro) . '.everyblock.com/locations/neighborhoods/" + neighborhood + "/?only=' . strip_tags($this->schema)); ?>");
								doAjaxRequest('getLocationTimeline', { metro: "<?php echo strip_tags($this->metro); ?>", location: neighborhood, schemas: [ "<?php echo strip_tags($this->schema); ?>" ] } , entriesCallback);
							} else if (neighborhood != "loading") {
								clickthrough.setAttribute("href", "<?php if(isset($this->schemaObj) && isset($this->schemaObj->about)) { echo(strip_tags($this->schemaObj->about)); } else { echo("http://" . strip_tags($this->metro) . ".everyblock.com/" . strip_tags($schema) . "/"); } ?>");
								doAjaxRequest('getTopNews', { metro: "<?php echo strip_tags($this->metro); ?>)", schemas: [ "<?php echo strip_tags($this->schema); ?>" ] } , entriesCallback);
							}
							return false;
						}
						
						function entriesCallback(data) {
							try {
								var html = "";
								if(data.count == 0) {
									html += "<div class=\"everyblock_no_data\">There are no <?php if(isset($this->schemaObj->plural_name)) { echo(ucwords(strip_tags($this->schemaObj->plural_name))); } else { echo("Entries"); } ?> currently for this neighborhood.</div>";
									document.getElementById('entries_content').innerHTML = html;
									return;
								}
								data.results.forEach(function(element, index, array) {
									html += "<div class=\"everyblock_widget_item" + (index == array.length - 1 ? " everyblock_widget_item_last" : "") + "\">";
									html += "<h3><a href=" + element.url + " target=\"_blank\">" + element.title + "</a></h3>";
									if (typeof(element.pub_date) !== 'undefined') html += "<span class=\"everyblock_widget_time\">" + formatDate(element.pub_date) + "</span>";
									if (typeof(element.location_name) !== 'undefined') html += "<span class=\"everyblock_widget_loc\">" + element.location_name + "</span>";
									html += "</div>";
								});
								document.getElementById('entries_content').innerHTML = html;
							} catch (err) {
								console.log('error generating entries: ' + err);
							}
						}
						
						var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
						var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
						
						function formatDate(unformattedDate) {
							unformattedDate = unformattedDate.substring(0, unformattedDate.indexOf("T"));
							var datesArray = unformattedDate.split("-");
							var date = new Date(datesArray[0], parseInt(datesArray[1], 10) - 1, datesArray[2]);
							return days[date.getDay()] + ", " + months[date.getMonth()] + " " + date.getDate();
						}
						
						doAjaxRequest('getNeighborhoods', { metro: "<?php echo strip_tags($this->metro); ?>" } , dropdownCallback);
						doAjaxRequest('getTopNews', { metro: "<?php echo strip_tags($this->metro); ?>", schemas: [ "<?php echo strip_tags($this->schema); ?>" ] } , entriesCallback);
					</script>
				</div>
			</body>
		</html>
<?php
	}
}

$widget = new EveryblockLocalInfoWidget(strip_tags($_GET['metro']), strip_tags($_GET['schema']), strip_tags($_GET['width']), strip_tags($_GET['height']));
	
echo($widget->getEmbedCode());
?>