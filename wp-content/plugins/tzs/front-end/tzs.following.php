<?php
function tzs_front_end_following_handler($atts) {
	ob_start();
	
	$sp = tzs_validate_search_parameters();
	
	$s_sql = tzs_search_parameters_to_sql($sp, 'sh');
	$s_title = tzs_search_parameters_to_str($sp);
	
	$errors = $sp['errors'];
	
	$show_table = true;
	if (strlen($s_title) == 0 && count($errors) == 0) {
		$show_table = false;
		//$errors = array("Укажите параметры поиска");
	}
	
	if (count($errors) > 0)
		print_errors($errors);
	?>
	<a href="javascript:showSearchDialog();" id="edit_search">Изменить параметры поиска</a>
	<?php
	
	if (count($errors) == 0 && $show_table) {
	
	if (strlen($s_title) > 0) {
		?>
			<div id="search_info">Попутные грузы <?php echo $s_title; ?></div>
		<?php
	} else {
		?>
			<div id="search_info">Параметры поиска не заданы</div>
		<?php
	}
	
	$page = current_page_number();
	
	?>
	<a tag="page" id="realod_btn" href="<?php echo build_page_url($page); ?>">Обновить</a>
	<?php
	
	global $wpdb;
	
	$url = current_page_url();
	
	$pp = TZS_RECORDS_PER_PAGE;
	
	$sql = "SELECT COUNT(*) as cnt FROM ".TZS_SHIPMENT_TABLE." WHERE active=1 $s_sql;";
	$res = $wpdb->get_row($sql);
	if (count($res) == 0 && $wpdb->last_error != null) {
		print_error('Не удалось отобразить список грузов. Свяжитесь, пожалуйста, с администрацией сайта');
	} else {
		$records = $res->cnt;
		$pages = ceil($records / $pp);
		if ($pages == 0)
			$pages = 1;
		if ($page > $pages)
			$page = $pages;
		
		$from = ($page-1) * $pp;
		$sql = "SELECT * FROM ".TZS_SHIPMENT_TABLE." WHERE active=1 $s_sql ORDER BY time DESC LIMIT $from,$pp;";
		$res = $wpdb->get_results($sql);
		if (count($res) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить список грузов. Свяжитесь, пожалуйста, с администрацией сайта');
		} else {
			if (count($res) == 0) {
				?>
					<div id="info">По Вашему запросу ничего не найдено.</div>
				<?php
			} else {
			?>
			<script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
			<table id="tbl_shipments">
			<tr>
				<th id="numb">Номер заявки</th>
				<th id="adds">Дата размещения</th>
				<th id="date-load">Дата погрузки<br>Дата выгрузки</th>
				<th id="numb-unload" nonclickable="true">Пункт погрузки<br>Пункт выгрузки</th>
				<th id="desc">Описание груза</th>
				<th id="wight">Вес</th>
				<th id="vol">Объем</th>
				<th id="type">Тип транспорта</th>
				<th id="cost">Цена</th>
				<th id="comm">Комментарии</th>
			</tr>
			<?php
			foreach ( $res as $row ) {
				$type = isset($GLOBALS['tzs_tr_types'][$row->trans_type]) ? $GLOBALS['tzs_tr_types'][$row->trans_type] : "";
				
				?>
				<tr rid="<?php echo $row->id;?>">
				<td><?php echo $row->id;?></td>
				<td><b><?php echo convert_date_no_year($row->time); ?></b><br/><?php echo convert_time_only($row->time);?></td>
				<td><?php echo convert_date_no_year($row->sh_date_from);?><br/><?php echo convert_date_no_year($row->sh_date_to);?></td>
				<td>
					<?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->sh_city_from);?><br/><?php echo tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, $row->sh_city_to);?>
					<?php if ($row->distance > 0) {?>
						<br/>
						<?php echo tzs_make_distance_link($row->distance, false, array($row->sh_city_from, $row->sh_city_to)); ?>
					<?php } ?>
				</td>
				
				<td><?php echo htmlspecialchars($row->sh_descr);?></td>
				
				<?php if ($row->sh_weight > 0) {?>
					<td><?php echo remove_decimal_part($row->sh_weight);?> т</td>
				<?php } else {?>
					<td>&nbsp;</td>
				<?php }?>
				
				<?php if ($row->sh_volume > 0) {?>
					<td><?php echo remove_decimal_part($row->sh_volume);?> м³</td>
				<?php } else {?>
					<td>&nbsp;</td>
				<?php }?>
				
				<td><?php echo $type;?></td>
				<td><?php echo tzs_cost_to_str($row->cost);?></td>
				<td><?php echo htmlspecialchars($row->comment);?></td>
				</tr>
				<?php
			}
			?>
			</table>
			
			<?php
			}
			build_pages_footer($page, $pages);
		}
	}
	}
	
	?>
		<script src="/wp-content/plugins/tzs/assets/js/search.js"></script>
		<script>
			var post = [];
			<?php
				echo "// POST dump here\n";
				foreach ($_POST as $key => $value) {
					echo "post[".tzs_encode2($key)."] = ".tzs_encode2($value).";\n";
				}
			?>
			
			function showSearchDialog() {
				doSearchDialog('cargo', post, null, true);
			}
			
			jQuery(document).ready(function(){
				jQuery('#tbl_shipments').on('click', 'td', function(e) {  
					var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
					var id = this.parentNode.getAttribute("rid");
					if (!nonclickable)
						document.location = "/account/view-shipment/?id="+id;
				});
				hijackLinks(post);
				<?php
				if (strlen($s_title) == 0) {
					?>showSearchDialog();<?php
				}
				?>
			});
		</script>
	<?php
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>