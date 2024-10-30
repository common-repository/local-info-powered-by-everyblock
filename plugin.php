<?php
/*
Plugin Name: Local Info Powered By EveryBlock
Plugin URI: http://tirespider.com/
Description: Adds a Local Info Widget to a post, page, or sidebar.
Version: 1.0
Author: Jeff S
Author URI: http://tirespider.com/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once dirname( __FILE__ ) . "/everyblock/settings.php";
require_once dirname( __FILE__ ) . '/everyblock/EveryblockRequest.php';

class EveryblockLocalInfoWidget extends WP_Widget {

	protected $settings;
	
	public function EveryblockLocalInfoWidget()
	{
		$widget_ops = array('classname' => 'EveryblockLocalInfoWidget', 'description' => 'Adds a Local Info Widget to a post, page, or sidebar.' );
		$this->WP_Widget('EveryblockLocalInfoWidget', 'Local Info Powered by Everyblock', $widget_ops);
		
		$this->everyblockRequest = new EveryblockLocalInfoRequest();
		$this->settings = new EveryblockLocalInfoSettings();
	}
  
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'widgetWidth' => '', 'widgetHeight' => ''));
		if(strlen($this->settings->getAPIKey()) == 0 || $this->settings->getAPIKey() == 'API_KEY_HERE') {
			?><p><div class="alert alert-red">Please input your API key into the settings file located inside this component in "./everyblock/settings.php".  The widget will not appear until then.</div></p><?php
		} else {
			if(!isset($instance['widgetWidth']) || strlen($instance['widgetWidth']) == 0) { 
				$widgetWidth = "300"; 
			} else {
				$widgetWidth = $instance['widgetWidth'];
			}
			
			if(!isset($instance['widgetHeight']) || strlen($instance['widgetHeight']) == 0) { 
				$widgetHeight = "500"; 
			} else {
				$widgetHeight = $instance['widgetHeight'];
			}
					
			if(isset($instance['metro'])) { 
				$currentMetro = $instance['metro'];
			} else {
				$currentMetro = "chicago";
			}
			
			if(isset($instance['schema'])) { 
				$currentSchema = $instance['schema'];
			} else {
				$currentSchema = "food-inspections";
			}
			
			$metros = $this->everyblockRequest->getAllMetros();
			
			if(isset($currentMetro) || ($metros && count($metros) > 0)) {
				$schemas = $this->everyblockRequest->getSchema(isset($currentMetro) ? $currentMetro : $metros[0]->short_name); 
			}
			$metroDropdownId = $this->get_field_id('metro');
			$schemaDropdownId = $this->get_field_id('schema');
		?>
			
		<p><label for="<?php echo $this->get_field_id('metro'); ?>">
				Metro: <select class="widefat" id="<?php echo $metroDropdownId; ?>" name="<?php echo $this->get_field_name('metro'); ?>" <?php if(!isset($metros)) { ?>disabled <?php } ?>>
					<?php 
					if(isset($metros)) {
						foreach($metros as $element) { ?>
							<option <?php if(isset($currentMetro) && $currentMetro == $element->short_name) { ?>selected <?php } ?> value="<?php echo sanitize_text_field($element->short_name); ?>"><?php echo sanitize_text_field($element->metro_name); ?></option>
							<?php 
						}
					} else { ?>
						<option value="undefined">Metro Lookup failed.</option>
						<?php 
					}
					?>
				</select>
			</label>
		</p>
		
		<p><label for="<?php echo $this->get_field_id('schema'); ?>">
				Schema: <select class="widefat" id="<?php echo $schemaDropdownId; ?>" name="<?php echo $this->get_field_name('schema'); ?>" <?php if(!isset($schemas)) { ?>disabled <?php } ?>>
					<?php 
					if(isset($schemas)) {
						foreach($schemas as $element) { ?>
							<option <?php if(isset($currentSchema) && $currentSchema == $element->slug) { ?>selected <?php } ?> value="<?php echo sanitize_text_field($element->slug); ?>"><?php echo ucwords(sanitize_text_field($element->plural_name)); ?></option>
							<?php 
						}
					} else { ?>
						<option value="undefined">Schema Lookup failed.</option>
						<?php 
					}
					?>
				</select>
			</label>
		</p>
		  <p><label for="<?php echo $this->get_field_id('widgetWidth'); ?>">Width: <input class="widefat" id="<?php echo $this->get_field_id('widgetWidth'); ?>" name="<?php echo $this->get_field_name('widgetWidth'); ?>" type="text" value="<?php echo attribute_escape($widgetWidth); ?>" /></label></p>
		  <p><label for="<?php echo $this->get_field_id('widgetHeight'); ?>">Height: <input class="widefat" id="<?php echo $this->get_field_id('widgetHeight'); ?>" name="<?php echo $this->get_field_name('widgetHeight'); ?>" type="text" value="<?php echo attribute_escape($widgetHeight); ?>" /></label></p>
		  
		  <script type="text/javascript">						
			jQuery(document.getElementById("<?php echo $metroDropdownId; ?>")).change(function() { 
				console.log('wat');
				var dropdown = document.getElementById("<?php echo $this->get_field_id('metro'); ?>");
				var metro = dropdown.options[dropdown.selectedIndex].value;
				
				jQuery.ajax({url: '<?php echo(plugins_url( 'everyblock/EveryblockRequest.php', __FILE__ )); ?>', type:'POST', data: { process: "false", call: "getSchema", type: "json", metro: metro }}).done(function(results) {
					var json = JSON.parse(results);
					var dropdown = document.getElementById("<?php echo $schemaDropdownId; ?>");
					var html = "";
					json.forEach(function(element, index, array) {
						html += "<option value=" + element.slug + ">" + element.plural_name.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) + "</option>";
					});
					dropdown.innerHTML = html;
				});
			});
		  </script>
		<?php
	}
  }
  
  function update($new_instance, $old_instance) 
  {
		$instance = $old_instance;
		
		if(!isset($new_instance['widgetWidth']) || strlen($new_instance['widgetWidth']) == 0 || intval($new_instance['widgetWidth']) <= 0) { 
			$instance['widgetWidth'] = 300; 
		} else {
			$instance['widgetWidth'] = 300; 
		}
		
		if(!isset($new_instance['widgetHeight']) || strlen($new_instance['widgetHeight']) == 0 || intval($new_instance['widgetHeight']) <= 0) { 
			$instance['widgetHeight'] = 500; 
		} else {
			$instance['widgetHeight'] = intval($new_instance['widgetHeight']);
		}
		
		if(isset($new_instance['metro'])) {
			$instance['metro'] = sanitize_text_field($new_instance['metro']);
		}
		
		if(isset($new_instance['schema'])) {
			$instance['schema'] = sanitize_text_field($new_instance['schema']);
		}
		
		return $instance;
  }

  function widget($args, $instance) {
	extract($args, EXTR_SKIP);
	if(strlen($this->settings->getAPIKey()) >= 1 && $this->settings->getAPIKey() != 'API_KEY_HERE') {
		$metro = sanitize_text_field($instance['metro'] != "" ? $instance['metro'] : "chicago");
		$schema = sanitize_text_field($instance['schema'] != "" ? $instance['schema'] : "food-inspections");
		$width = sanitize_text_field(intval($instance['widgetWidth']) <= 0 ? 300 : intval($instance['widgetWidth']));
		$height = sanitize_text_field(intval($instance['widgetHeight']) <= 0 ? 300 : intval($instance['widgetHeight']));
		echo "<iframe src=\"" . plugins_url( 'widget.php', __FILE__ ) . "?metro=" .  $metro . "&schema=" . $schema . "&width=" . $width . "&height=" . $height . "\" width=\"". $width . "\" //height=\"" . $height . "\" frameborder='0' scrolling='no'></iframe>";
	}
 }
}
 
add_action('widgets_init', create_function('', 'return register_widget("EveryblockLocalInfoWidget");') );

function display_local_info_widget($atts) {
	$atts = extract(shortcode_atts( array('width' => 300, 'height' => 500, 'metro' => 'chicago', 'schema' => 'food-inspections'), $atts, 'display_custom_widget'));
	$width = sanitize_text_field(intval($width) <= 0 ? 300 : intval($width));
	$height = sanitize_text_field(intval($height) <= 0 ? 300 : intval($height));
	
	$code = "<iframe src=\"" . plugins_url( 'widget.php', __FILE__ ) . "?metro=" . sanitize_text_field($metro) . "&schema=" . sanitize_text_field($schema) . "&width=" . $width . "&height=" . $height . "\" width=\"". $width . "\" //height=\"" . $height . "\" frameborder='0' scrolling='no'></iframe>";
	 
	return get_html_for_embed_local(stripslashes($code));
}

function get_html_for_embed_local($embedCode) {
	$settings = new EveryblockLocalInfoSettings();
	if(strlen($settings->getAPIKey()) >= 1 && $settings->getAPIKey() != 'API_KEY_HERE') {
		return '<div class="widget_block">' . stripslashes($embedCode) .'</div>'; 
	}
	return "";
}

add_shortcode('display_local_info_widget', display_local_info_widget);
?>