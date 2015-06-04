<?php
function tzs_front_end_view_shipment_handler($atts) {
	ob_start();
	
	global $wpdb;
	
	$user_id = get_current_user_id();
	
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
	
	if ($sh_id <= 0) {
		print_error('Груз не найден');
	} else {
		$sql = "SELECT * FROM ".TZS_SHIPMENT_TABLE." WHERE id=$sh_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить информацию о грузе. Свяжитесь, пожалуйста, с администрацией сайта');
		} else if ($row == null) {
			print_error('Груз не найден');
		} else {
			$type = isset($GLOBALS['tzs_tr_types'][$row->trans_type]) ? $GLOBALS['tzs_tr_types'][$row->trans_type] : "";
			?>
			<script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
			<?php if(isset($_GET['spis'])) echo "<a id='edit_search' href='/account/my-shipments/'>Назад к списку</a> <div style='clear: both'></div>";
            else echo "<button id='edit_search'  onclick='history.back()'>Назад к списку</button> <div style='clear: both'></div>"; ?>
            <table border="0" id="view_ship">
			<tr>
				<td>Номер груза</td>
				<td><?php echo $row->id; ?></td>
			</tr>
			<tr>
				<td>Активно</td>
				<td><?php echo $row->active == 1 ? 'Да' : 'Нет'; ?></td>
			</tr>
			<tr>
				<td>Дата размещения</td>
				<td><?php echo convert_date_no_year($row->time); ?> <?php echo convert_time_only($row->time); ?></td>
			</tr>
			<?php if ($row->last_edited != null) {?>
			<tr>
				<td>Дата последнего изменения</td>
				<td><?php echo convert_date_no_year($row->last_edited); ?> <?php echo convert_time_only($row->last_edited); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Дата погрузки</td>
				<td><?php echo convert_date_no_year($row->sh_date_from); ?></td>
			</tr>
			<tr>
				<td>Дата выгрузки</td>
				<td><?php echo convert_date_no_year($row->sh_date_to); ?></td>
			</tr>
			<tr>
				<td>Пункт погрузки</td>
				<td><?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->sh_city_from); ?></td>
			</tr>
			<tr>
				<td>Пункт выгрузки</td>
				<td><?php echo tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, $row->sh_city_to); ?></td>
			</tr>
			<tr>
				<td>Описание груза</td>
				<td><?php echo htmlspecialchars($row->sh_descr); ?></td>
			</tr>
			<?php if ($row->sh_weight > 0) {?>
			<tr>
				<td>Вес</td>
				<td><?php echo $row->sh_weight; ?> т</td>
			</tr>
			<?php } ?>
			<?php if ($row->sh_volume > 0) {?>
			<tr>
				<td>Объем</td>
				<td><?php echo $row->sh_volume; ?> м³</td>
			</tr>
			<?php } ?>
			<tr>
				<td>Количество машин</td>
				<td><?php echo $row->trans_count; ?></td>
			</tr>
			<tr>
				<td>Тип транспорта</td>
				<td><?php echo $type; ?></td>
			</tr>
			<?php if ($row->sh_length > 0 || $row->sh_height > 0 || $row->sh_width > 0) {?>
			<tr>
				<td>Габариты</td>
				<td>Длинна=<?php echo $row->sh_length; ?>м Ширина=<?php echo $row->sh_width; ?>м Высота=<?php echo $row->sh_height; ?>м</td>
			</tr>
			<?php } ?>
			<?php $cost=tzs_cost_to_str($row->cost); if (strlen($cost) > 0) {?>
			<tr>
				<td>Цена</td>
				<td><?php echo $cost;?></td>
			</tr>
			<?php } ?>
			
			<?php if ($row->distance > 0) {?>
			<tr>
				<td>Расстояние</td>
				<td><?php echo tzs_make_distance_link($row->distance, false, array($row->sh_city_from, $row->sh_city_to)); ?></td>
			</tr>
			<?php } ?>
			
			<?php if (strlen($row->comment) > 0) {?>
			<tr>
				<td>Комментарии</td>
				<td><?php echo htmlspecialchars($row->comment); ?></td>
			</tr>
			<?php } ?>
			</table>
			
			<?php if ($user_id == 0) {?>
				<div>Для просмотра контактов необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></div>
			<?php } else if ($user_id != $row->user_id) {?>
			
			<br/>
			<h1 class="entry-title">Контактная информация</h1>
			
			<?php
				tzs_print_user_table($row->user_id);
			?>
			
			<script src="/wp-content/plugins/tzs/assets/js/feedback.js"></script>
			
			<button id="view_feedback" onClick="<?php echo tzs_feedback_build_url($row->user_id);?>">Отзывы <span>|</span> Рейтинг пользователя</button>
			<?php } else {?>
				<button id="view_del" onClick="javascript: promptDelete(<?php echo $row->id;?>);">Удалить</button>
				<button id="view_edit" onClick="javascript: window.open('/account/edit-shipment/?id=<?php echo $row->id;?>', '_self');">Изменить</button>
			<?php } ?>
			<script>
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
						'action': 'tzs_delete_shipment',
						'id': id
					};
					
					jQuery.post(ajax_url, data, function(response) {
						if (response == '1') {
							window.open('/account/my-shipments/', '_self');
						} else {
							alert('Не удалось удалить: '+response);
						}
					});
				}
			</script>
			<?php
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}
?>