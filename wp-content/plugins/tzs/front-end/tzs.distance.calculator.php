<?php

function print_distance_calculator_form($errors, $city, $map, $form) {
	print_errors($errors);
	?>
	
	<?php if ($form) {?>
	<script src="/wp-content/plugins/tzs/assets/js/autocomplete.js"></script>
	<form id="cpost" class="post-form" action="">
	<table id="calc_form">
		
	</table>
	<input name="calc" type="submit" id="calc" class="submit_button" value="Рассчитать"/>
	</form>
	<?php }?>
	
	<script>
		var city = [];
		<?php
		if ($city != null) {
			foreach ($city as $c) {
				?>city.push(<?php echo tzs_encode($c) ?>);<?php
			}
		}
		?>
	</script>
	
	<?php if ($map && $city != null) {?>
	<div id="map_canvas"></div>
	<script>
		var directionsService;
		var directionsDisplay;
		var map;
	
		jQuery(document).ready(function(){
			if (typeof google === 'object' && typeof google.maps === 'object') {
				initialize();
			} else {
				loadScript();
			}
		});
	
		function initialize() {
			directionsService = new google.maps.DirectionsService();
			directionsDisplay = new google.maps.DirectionsRenderer();
			var map_canvas = document.getElementById('map_canvas');
			var map_options = {
				zoom: 8,
				center: new google.maps.LatLng(50.4020355, 30.5326905),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			map = new google.maps.Map(map_canvas, map_options);
			displayRoute();
			redraw();
		}
		
		function redraw() {
			if (typeof google === 'object' && typeof google.maps === 'object') {
				google.maps.event.trigger(map, 'resize');
			}
		}
		
		function loadScript() {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=initialize';
			document.body.appendChild(script);
		}
		
		function displayRoute() {
			directionsDisplay.setMap(map);

			var request = {
				origin: city[0],
				destination: city[city.length-1],
				waypoints: [],
				provideRouteAlternatives: false,
				travelMode: google.maps.TravelMode.DRIVING,
				unitSystem: google.maps.UnitSystem.IMPERIAL
			};
			
			if (city.length > 2) {
				for (var i = 1; i < city.length-1; i++) {
					request.waypoints.push({
						location:city[i],
						stopover:true
					});
				}
			}
			
			directionsService.route(request, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(response);
				}
			});
		}
	</script>
	<?php }?>
	
	<?php if ($form) {?>
		<script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
	<?php }?>
	
	<script>
		var global_counter = 0;
		
		function addRow(after) {
			var tag = "row_"+global_counter;
			global_counter++;
			row = jQuery('<tr tag="'+tag+'" name="city_row"> <td id="caption"></td> <td id="field"> <input autocomplete="city" id="city" type="text" size="100" name="city[]" value=""><br/><a id="add_button" href="javascript:addAfter(\''+tag+'\');">Добавить</a> </td> <td id="del"> <a id="del_button" href="javascript:removeRow(\''+tag+'\');">Удалить</a> </td> </tr>');
			if (after == null) {
				tbl = jQuery('#calc_form');
				tbl.append(row);
			} else {
				jQuery(after).after(row);
			}
			autocomplete_initialize();
		}
		
		function update() {
			setCaptions();
			hideDel();
			hideAdd();
		}
		
		function addAfter(tag) {
			var row = jQuery('tr[tag='+tag+']');
			addRow(row);
			update();
		}
		
		function removeRow(tag) {
			var row = jQuery('tr[tag='+tag+']');
			row.remove();
			update();
		}
		
		function hideDel() {
			var arr = jQuery('a[id=del_button]');
			var visible = arr.length > 2;
			arr.each(function() {
				if (visible) {
					jQuery(this).removeAttr('style');
				} else {
					jQuery(this).attr('style', 'display:none;');
				}
			});
		}
		
		function hideAdd() {
			var arr = jQuery('a[id=add_button]');
			var visible = arr.length < 10;
			arr.each(function() {
				if (visible) {
					jQuery(this).removeAttr('style');
				} else {
					jQuery(this).attr('style', 'display:none;');
				}
			});
		}
		
		function setCaptions() {
			var counter = 0;
			var arr = jQuery('td[id=caption]');
			arr.each(function() {
				var caption;
				if (counter == 0) {
					caption = 'Откуда';
				} else if (counter == arr.length-1) {
					caption = 'Куда';
				} else {
					caption = 'Через';
				}
				jQuery(this).html(caption);
				counter++;
			});
		}
		
		function showDist() {
			city_tmp = [];
			jQuery('input[id=city]').each(function() {
				city_tmp.push(jQuery(this).val());
			});
			jQuery('#calc').attr('disabled','disabled');
			displayDistance(city_tmp, function() {
				jQuery('#calc').removeAttr('disabled');
			});
		}
		
		jQuery(document).ready(function(){
			<?php if ($form) {?>
			jQuery('#cpost').submit(function() {
				showDist();
				return false;
			});
			addRow(null);
			addRow(null);
			if (city.length > 2) {
				for (var i = 0; i < city.length-2; i++)
					addRow(null);
			}
			var arr = jQuery('input[id=city]');
			for (var i = 0; i < city.length; i++) {
				jQuery(arr[i]).attr('value', city[i]);
			}
			update();
			<?php }?>
		});
	</script>
	
	<?php
}

function tzs_front_end_distance_calculator_handler($atts) {
	ob_start();
	
	$errors = array();
	
	$city = null;
	$map = false;
	$form = true;
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['calc'])) {
		if (!isset($_GET['city']) || !is_array($_GET['city']) || count($_GET['city']) < 2) {
			array_push($errors, 'Недостаточно данных для расчета расстояния');
		} else {
			$city = array_filter(array_map('trim',$_GET['city']));
			if (count($city) < 2) {
				array_push($errors, 'Недостаточно данных для расчета расстояния');
			} elseif (count($city) > 10) {
				array_push($errors, 'Слишком много точек для расчета расстояния');
			} else {
				$res = tzs_calculate_distance($city);
				
				$errors = array_merge($errors, $res['errors']);
				print_errors($errors);
				$errors = null;
				
				if ($res['results'] > 0) {
				?>
					<div id="calc_result">
						<div id="distance_result">
							Расстояние по маршруту <?php echo tzs_cities_to_str($city);?> <?php echo tzs_convert_distance_to_str($res['distance'], true);?>,
						</div>
						<div id="duration_result">
							примерное время в пути <?php echo tzs_convert_time_to_str($res['time']);?>
						</div>
					</div>
				<?php
				}
				
				$map = true;
				$form = false;
				print_distance_calculator_form($errors, $city, $map, $form);
			}
		}
		print_errors($errors);
	} else {
		print_distance_calculator_form($errors, $city, $map, $form);
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>