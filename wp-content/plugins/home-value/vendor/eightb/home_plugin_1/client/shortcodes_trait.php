<?php

namespace eightb\home_plugin_1\client;

/**
	@brief		Handles shortcode functions.
	@since		2017-03-05 11:32:31
**/
trait shortcodes_trait
{
	/**
		@brief		Return the prefix used for shortcodes.
		@details	For example: 8b_home_value_
		@since		2017-03-05 11:33:05
	**/
	public function get_plugin_prefix()
	{
		$this->wp_die( 'Please override this function: %s\%s', __CLASS__, __FUNCTION__ );
	}

	/**
		@brief		Return the name of the shortcode.
		@details	The default is the plugin prefix.
		@since		2017-03-07 22:27:42
	**/
	public function get_shortcode_name()
	{
		return $this->get_plugin_prefix();
	}

	/**
		@brief		Return the shortcode function name.
		@details	The default is shortcode_PLUGINPREFIX
		@since		2017-03-07 22:27:51
	**/
	public function get_shortcode_function()
	{
		$name = $this->get_plugin_prefix();
		$name = 'shortcode_' . $name;
		return $name;
	}

	/**
		@brief		Replace the shortcodes in this text.
		@since		2017-02-24 23:54:27
	**/
	public function replace_shortcodes( $text, $replacements )
	{
		$prefix = $this->get_plugin_prefix();
		foreach( $replacements as $find => $replacement )
			$text = str_replace( '[' . $prefix . '_' . $find . ']', $replacement, $text );
		
		
		/*Custom Code Start 2019/03/09 Add comparables data template file.*/
		$comparables_text = '<h5 class="hv-recent-sales">Recent Local Sales</h5>';
		if( isset( $replacements["comparables"] ) && count( $replacements["comparables"] ) > 0)
		{
			$temp_comp_array = $replacements["comparables"];
			usort($temp_comp_array, function($a, $b) {
				return $a->attributes->saleDate < $b->attributes->saleDate;
			});
			
			
			$comparables_looop_count=0;
			
			
			$found_check=0;
			$map_location_data = array();
			$map_location_data_str = "";
			$map_location_center_data="";
			//print_r( $temp_comp_array);
			for($count = 0; $count < count($temp_comp_array ); $count++ )
			{
				if( strtotime("-6 months") < $temp_comp_array[$count]->attributes->saleDate  && isset( $temp_comp_array[$count]->attributes->salePrice ) && $temp_comp_array[$count]->attributes->salePrice > 0 )
				{
					if( $map_location_center_data == "" )
					{
						$map_location_center_data = "lat: ".$temp_comp_array[$count]->coordinates->latitude.", lng: ".$temp_comp_array[$count]->coordinates->longitude;
					}
					setlocale(LC_MONETARY, 'en_US');
					$map_location_data[] = array('<div class="show_value"><p class="hv_address">'.$temp_comp_array[$count]->address->deliveryLine.'</p><p class="hv_address">Sale Price: $'.$this->number_format( $temp_comp_array[$count]->attributes->salePrice ).'</p><p class="hv_address">Sale Date: '.date("m/d/Y",$temp_comp_array[$count]->attributes->saleDate).'</p></div>',$temp_comp_array[$count]->coordinates->latitude,$temp_comp_array[$count]->coordinates->longitude,($found_check+1));
					$comparables_text .= '';
					$found_check++;
					$comparables_looop_count++;
					if( $comparables_looop_count >= 10 )
					{
						break;
					}
				}
			}
			$comparables_text .= '<div id="home_value_map" class="hv-map"></div><script>
				initMap();
				function initMap()
				{
					var map = new google.maps.Map(document.getElementById("home_value_map"), {
						zoom: 15,
						center: {'.$map_location_center_data.'},
						mapTypeId: google.maps.MapTypeId.ROADMAP
					});
					setMarkers(map);
				}
				function setMarkers(map) {
					var locations = '.json_encode($map_location_data).';
					var image = {
						url: "https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png",
						size: new google.maps.Size(20, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(0, 32)
					
					};
					var shape = {
						coords: [1, 1, 1, 20, 18, 20, 18, 1],
						type: "poly"
					};
					var infowindow = new google.maps.InfoWindow();
					var marker, i;
					for (i = 0; i < locations.length; i++) {  
						marker = new google.maps.Marker({
							position: new google.maps.LatLng(locations[i][1], locations[i][2]),
							map: map
						});
						google.maps.event.addListener(marker, "click", (function(marker, i) {
							return function() {
							  infowindow.setContent(locations[i][0]);
							  infowindow.open(map, marker);
							}
						})(marker, i));
					}
				}
				</script>';
			if( $found_check > 0)
			{
				$text = str_replace( '[' . $prefix . '_data_comparables]', $comparables_text, $text );
			}
			else
			{
				$text = str_replace( '[' . $prefix . '_data_comparables]', "", $text );
			}
		}
		else
		{
			$text = str_replace( '[' . $prefix . '_data_comparables]', "", $text );
		}
		/*Custom Code End 2019/03/09*/
		
		return $text;
	}
}