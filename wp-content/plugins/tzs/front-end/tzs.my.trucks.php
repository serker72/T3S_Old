<?php

add_action( 'wp_ajax_tzs_delete_truck', 'tzs_delete_truck_callback' );

function tzs_delete_truck_callback() {
	$id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval( $_POST['id'] ) : 0;
	$user_id = get_current_user_id();
	if ($id <= 0) {
		echo "Номер не найден";
	} else if ($user_id == 0) {
		echo "Необходимо ввойти";
	} else {
		global $wpdb;
		
		//$sql = "UPDATE ".TZS_TRUCK_TABLE." SET active=0 WHERE id=$id AND user_id=$user_id;";
		$sql = "DELETE FROM ".TZS_TRUCK_TABLE." WHERE id=$id AND user_id=$user_id;";
		
		if (false === $wpdb->query($sql)) {
			echo "Не удалось удалить Ваш транспорт. Свяжитесь, пожалуйста, с администрацией сайта ";
		} else {
			echo "1";
		}
	}
	die();
}

function tzs_front_end_my_trucks_handler($atts) {
	ob_start();
	
	global $wpdb;
	
	$user_id = get_current_user_id();
	$url = current_page_url();
	$page = current_page_number();
	$pp = TZS_RECORDS_PER_PAGE;
	
	if ($user_id == 0) {
		?>
		<div>Для просмотра необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></div>
		<?php
	} else {
		$sql = "SELECT COUNT(*) as cnt FROM ".TZS_TRUCK_TABLE." WHERE user_id=$user_id AND active=1;";
		$res = $wpdb->get_row($sql);
		if (count($res) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить список транспорта. Свяжитесь, пожалуйста, с администрацией сайта');
		} else {
			$records = $res->cnt;
			$pages = ceil($records / $pp);
			if ($pages == 0)
				$pages = 1;
			if ($page > $pages)
				$page = $pages;
		
			$from = ($page-1) * $pp;
			$sql = "SELECT * FROM ".TZS_TRUCK_TABLE."  WHERE user_id=$user_id AND active=1 ORDER BY time DESC LIMIT $from,$pp;";
			$res = $wpdb->get_results($sql);
			if (count($res) == 0 && $wpdb->last_error != null) {
				print_error('Не удалось отобразить список транспорта. Свяжитесь, пожалуйста, с администрацией сайта');
			} else {
				?>
				<script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
				<table id="tbl_shipments">
				<tr>
					<th id="numb">Номер заявки</th>
					<th id="adds">Дата размещения</th>
					<th id="date-load">Дата погрузки<br>Дата выгрузки</th>
					<th id="numb-unload" nonclickable="true">Пункт погрузки<br>Пункт выгрузки</th>
					<th id="wight">Вес</th>
					<th id="vol">Объем</th>
					<th id="type">Тип транспорта</th>
					<th id="cost">Цена</th>
					<th id="comm">Комментарии</th>
					<th id="actions" nonclickable="true">Действия</th>
				</tr>
				<?php
				foreach ( $res as $row ) {
					$type = trans_types_to_str($row->trans_type, $row->tr_type);
					?>
					<tr rid="<?php echo $row->id;?>">
					<td><?php echo $row->id;?></td>
					<td><b><?php echo convert_date_no_year($row->time); ?></b><br/><?php echo convert_time_only($row->time);?></td>
					<td><?php echo convert_date_no_year($row->tr_date_from);?><br/><?php echo convert_date_no_year($row->tr_date_to);?></td>
					<td>
						<?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->tr_city_from);?><br/><?php echo tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, $row->tr_city_to);?>
						<?php if ($row->distance > 0) {?>
							<br/>
							<?php echo tzs_make_distance_link($row->distance, false, array($row->tr_city_from, $row->tr_city_to)); ?>
						<?php } ?>
					</td>
					
					<?php if ($row->tr_weight > 0) {?>
						<td><?php echo remove_decimal_part($row->tr_weight);?> т</td>
					<?php } else {?>
						<td>&nbsp;</td>
					<?php }?>
					
					<?php if ($row->tr_volume > 0) {?>
						<td><?php echo remove_decimal_part($row->tr_volume);?> м³</td>
					<?php } else {?>
						<td>&nbsp;</td>
					<?php }?>
					
					<td><?php echo $type;?></td>
					<td><?php echo tzs_cost_to_str($row->cost);?></td>
					<td><?php echo htmlspecialchars($row->comment);?></td>
					<!--<td>
						<a href="javascript: promptDelete(<?php echo $row->id;?>);">Удалить</a>
						<br/>
						<a href="/account/edit-truck/?id=<?php echo $row->id;?>">Изменить</a>
					</td>-->
					<td>
						<a href="javascript:doDisplay(<?php echo $row->id;?>);" at="<?php echo $row->id;?>" id="icon_set">Действия</a>
						<div id="menu_set" id2="menu" for="<?php echo $row->id;?>" style="display:none;">
							<ul>
								<a href="/account/view-truck/?id=<?php echo $row->id;?>">Смотреть</a>
								<a href="/account/edit-truck/?id=<?php echo $row->id;?>">Изменить</a>
								<a href="javascript: promptDelete(<?php echo $row->id;?>);" id="red">Удалить</a>
							</ul>
						</div>
					</td>
					</tr>
					<?php
				}
				?>
				</table>
				<button onClick="javascript: window.open('/account/add-truck/', '_self');">Добавить транспорт</button>
			
				<script>
				jQuery(document).ready(function(){
					jQuery('table').on('click', 'td', function(e) {  
						var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
						var id = this.parentNode.getAttribute("rid");
						if (!nonclickable)
							document.location = "/account/view-truck/?id="+id;
					});
				});
				
				function doDisplay(id) {
					var el = jQuery('div[for='+id+']');
					if (el.attr('style') == null) {
						el.attr('style', 'display:none;');
						jQuery('a[at='+id+']').attr('id', 'icon_set');
					} else {
						el.removeAttr('style');
						jQuery('a[at='+id+']').attr('id', 'icon_set_cur');
					}
					jQuery("div[id2=menu]").each(function(i) {
						var id2 = this.getAttribute('for');
						if (id2 != ''+id) {
							this.setAttribute('style', 'display:none;');
							jQuery('a[at='+id2+']').attr('id', 'icon_set');
						}
					});
				}
				
				function promptDelete(id) {
					jQuery('<div></div>').appendTo('body')
						.html('<div><h6>Удалить запись '+id+'?</h6></div>')
						.dialog({
							modal: true,
							title: 'Удаление',
							zIndex: 10000,
							autoOpen: true,
							width: 'auto',
							resizable: false,
							buttons: {
								'Да': function () {
									jQuery(this).dialog("close");
									doDelete(id);
								},
								'Нет': function () {
									jQuery(this).dialog("close");
								}
							},
							close: function (event, ui) {
								jQuery(this).remove();
							}
						});
				}
				
				function doDelete(id) {
					var data = {
						'action': 'tzs_delete_truck',
						'id': id
					};
					
					jQuery.post(ajax_url, data, function(response) {
						if (response == '1') {
							location.reload();
						} else {
							alert('Не удалось удалить: '+response);
						}
					});
				}
				</script>
				<?php
				build_pages_footer($page, $pages);
			}
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>