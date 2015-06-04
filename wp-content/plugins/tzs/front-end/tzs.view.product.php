<?php
function tzs_front_end_view_product_handler($atts) {
	ob_start();
	
	global $wpdb;
	
	$user_id = get_current_user_id();
	
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
	
	if ($sh_id <= 0) {
		print_error('Товар/услуга не найден');
	} else {
		$sql = "SELECT * FROM ".TZS_PRODUCTS_TABLE." WHERE id=$sh_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить информацию о товаре/услуге. Свяжитесь, пожалуйста, с администрацией сайта.');
		} else if ($row == null) {
			print_error('Товар/услуга не найден');
		} else {
                    if(isset($_GET['spis'])) echo "<a id='edit_search' href='/account/my-products/'>Назад к списку</a> <div style='clear: both'></div>";
                    else echo "<button id='edit_search'  onclick='history.back()'>Назад к списку</button> <div style='clear: both'></div>";
                    ?>
                <div id="">
                    <table border="0" id="view_ship">
			<tr>
				<td>Номер товара/услуги</td>
				<td><?php echo $row->id; ?></td>
			</tr>
			<tr>
				<td>Активно</td>
				<td><?php echo $row->active == 1 ? 'Да' : 'Нет'; ?></td>
			</tr>
			<tr>
				<td>Дата размещения</td>
				<td><?php echo convert_date_no_year($row->created); ?> <?php echo convert_time_only($row->time); ?></td>
			</tr>
			<?php if ($row->last_edited != null) {?>
			<tr>
				<td>Дата последнего изменения</td>
				<td><?php echo convert_date_no_year($row->last_edited); ?> <?php echo convert_time_only($row->last_edited); ?></td>
			</tr>
			<?php } ?>
			<?php if ($row->expiration != null) {?>
			<tr>
				<td>Дата окончания публикации</td>
				<td><?php echo convert_date_no_year($row->expiration); ?></td>
			</tr>
			<?php } ?>
			<?php if ($row->type_id > 0) {?>
			<tr>
				<td>Категория</td>
				<td><?php
                                    echo $row->type_id;
                                    $res = tzs_get_children_pages(TZS_PR_ROOT_CATEGORY_PAGE_ID);
                                    $key = array_search(intval($row->type_id), $res);
                                    if ($key) {
                                        echo $res[$key]['title'];
                                    }
                                ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Краткое описание товара/услуги</td>
				<td><?php echo htmlspecialchars($row->title); ?></td>
			</tr>
			<tr>
				<td>Полное описание товара/услуги</td>
				<td><?php echo htmlspecialchars($row->description); ?></td>
			</tr>
			<?php if ($row->copies > 0) {?>
			<tr>
				<td>Количество</td>
				<td><?php echo $row->copies; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row->price > 0) {?>
			<tr>
				<td>Стоимость товара</td>
				<td><?php echo $row->price." ".$GLOBALS['tzs_pr_curr'][$row->currency]; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row->payment > 0) {?>
			<tr>
				<td>Форма оплаты</td>
				<td><?php echo $GLOBALS['tzs_pr_payment'][$row->payment]; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Местонахождение</td>
				<td><?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from); ?></td>
			</tr>
			<?php if (strlen($row->comment) > 0) {?>
			<tr>
				<td>Комментарии</td>
				<td><?php echo htmlspecialchars($row->comment); ?></td>
			</tr>
			<?php } ?>
			<?php if (strlen($row->image_id_lists) > 0) {?>
			<tr>
				<td>Изображения</td>
				<td><?php 
                                    //$img_names = explode(';', $row->pictures);
                                    $img_names = explode(';', $row->image_id_lists);
                                    $main_image_id = $row->main_image_id;
                                    if (count($img_names) > 0) {
                                        ?>
                                    <table>
                                        <?php
                                        // Вначале выведем главное изображение
                                        $attachment_info = wp_get_attachment_image_src($main_image_id, 'full');
                                        if ($attachment_info !== false) {
                                            echo '<tr><td><img src="'.$attachment_info[0].'" alt=""></td></tr>';
                                        }
                                        
                                        // Затем выведем все остальные изображения
                                        for ($i=0;$i<count($img_names);$i++) {
                                            if ($img_names[$i] !== $main_image_id) {
                                                $attachment_info = wp_get_attachment_image_src($img_names[$i], 'full');
                                                //if (file_exists(ABSPATH . $img_names[$i])) {
                                                if ($attachment_info !== false) {
                                                    echo '<tr><td><img src="'.$attachment_info[0].'" alt=""></td></tr>';
                                                }
                                            }
                                        }
                                        ?>
                                    </table>
                                        <?php
                                    }
                                ?></td>
			</tr>
			<?php } else {
                          $img_names = array();
                        } ?>
			</table>
                    </div>

			<?php if (($user_id == 0) && ($GLOBALS['tzs_au_contact_view_all'] == false)) {?>
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
				<button id="view_edit" onClick="javascript: window.open('/account/edit-product/?id=<?php echo $row->id;?>', '_self');">Изменить</button>
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
						'action': 'tzs_delete_product',
						'id': id
					};
					
					jQuery.post(ajax_url, data, function(response) {
						if (response == '1') {
							window.open('/account/my-products/', '_self');
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