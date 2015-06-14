<?php
function tzs_front_end_trucks_handler($atts) {
    ob_start();
    ?>    
<!------------------------------------------------------------------------->                        
    <div>
        <table  id="tbl_products">
            <thead>
    <form class="search_pr_form" id="search_pr_form2" name="search_pr_form1" method="POST">
                <tr id="tbl_thead_records_per_page">
                    <th colspan="6"></th>
                    <th colspan="4">
                        Количество записей на странице:
                        <select name="records_per_page" style="width: 50px;">
                            <option value="5" <?php if ((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 5) || (TZS_RECORDS_PER_PAGE == 5)) echo 'selected="selected"'; ?> >5</option>
                            <option value="10" <?php if ((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 10) || (TZS_RECORDS_PER_PAGE == 10)) echo 'selected="selected"'; ?> >10</option>
                            <option value="15" <?php if ((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 15) || (TZS_RECORDS_PER_PAGE == 15)) echo 'selected="selected"'; ?> >15</option>
                            <option value="20" <?php if ((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 20) || (TZS_RECORDS_PER_PAGE == 20)) echo 'selected="selected"'; ?> >20</option>
                        </select><br>
                    </th>
                </tr>
                <tr>
                    <th id="tbl_trucks_id">Номер,<br/>дата,<br/>время заявки</th>
                    <th id="tbl_trucks_path">Пункт погрузки<br/>выгрузки</th>
                    <th id="tbl_trucks_dtc">Даты погрузки<br>выгрузки</th>
                    <th id="tbl_trucks_wv">Вес, объем груза</th>
                    <th id="tbl_trucks_tc">Тип груза</th>
                    <th id="tbl_trucks_ttr">Тип транспорта</th>
                    <th id="tbl_trucks_cost">Цена<br/>стоимость</th>
                    <th id="tbl_trucks_payment">Форма оплаты</th>
                    <th id="tbl_trucks_comm">Комментарии</th>
                    <th id="tbl_trucks_cont">Контакты перевозчика</th>
                </tr>
                <tr>
                    <th>
                        <!--div id="tbl_thead_search_button_1" class="tbl_thead_search_button" title="Фильтр по категории">
                                <img chk="1" src="<?php //echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_1', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_1" class="tbl_thead_search_div">
                                Категория:<br>
                              <select name="type_id" <?php //echo ($rootcategory === '1') ? '' : ' disabled="disabled"'; ?> >
                                <option value="0">все категории</option>
                                <option disabled>- - - - - - - -</option>
                                <?php
                                    //tzs_build_product_types('type_id', TZS_PR_ROOT_CATEGORY_PAGE_ID);
                                ?>
                            </select>
                            <?php //wp_nonce_field( 'type_id', 'type_id_nonce' ); ?>
                            </div-->
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_2" class="tbl_thead_search_button" title="Фильтр по типу заявок">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_2', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_2" class="tbl_thead_search_div">
                                Тип заявки:<br>
                                <select name="sale_or_purchase">
                                    <option value="0" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 0) echo 'selected="selected"'; ?> >Все</option>
                                    <option value="1" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 1) echo 'selected="selected"'; ?> >Продажа</option>
                                    <option value="2" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 2) echo 'selected="selected"'; ?> >Покупка</option>
                                </select><br>
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_3" class="tbl_thead_search_button" title="Фильтр по участнику тендера">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_3', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_3" class="tbl_thead_search_div">
                                Участник тендера:<br>
                                <select name="fixed_or_tender">
                                    <option value="0" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 0) echo 'selected="selected"'; ?> >Все предложения</option>
                                    <option value="1" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 1) echo 'selected="selected"'; ?> >Цена зафиксирована</option>
                                    <option value="2" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 2) echo 'selected="selected"'; ?> >Тендерное предложение</option>
                                </select>
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_4" class="tbl_thead_search_button" title="Фильтр по периоду публикации">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_4', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_4" class="tbl_thead_search_div">
                                Период публикации: от:<br>
                                <input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="10"><br>
                                Период публикации: до:<br>
                                <input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="10">
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_5" class="tbl_thead_search_button" title="Фильтр по описанию товара">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_5', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_5" class="tbl_thead_search_div">
                                Описание:<br>
                                <input type="text" name="pr_title" value="<?php echo_val('pr_title'); ?>" size="30">
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_6" class="tbl_thead_search_button" title="Фильтр по стоимости товара">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_6', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_6" class="tbl_thead_search_div">
                                Стоимость: от:<br>
                                <input type="text" name="price_from" value="<?php echo_val('price_from'); ?>" size="10"><br>
                                Стоимость: до:<br>
                                <input type="text" name="price_to" value="<?php echo_val('price_to'); ?>" size="10"><br>
                                Форма оплаты:<br>
                                <select name="payment">
                                    <option value="0" <?php if (isset($_POST['payment']) && $_POST['payment'] == 0) echo 'selected="selected"'; ?> >Любая</option>
                                    <option value="1" <?php if (isset($_POST['payment']) && $_POST['payment'] == 1) echo 'selected="selected"'; ?> >Наличная</option>
                                    <option value="2" <?php if (isset($_POST['payment']) && $_POST['payment'] == 2) echo 'selected="selected"'; ?> >Безналичная</option>
                                </select><br>
                                НДС:<br>
                                <select name="nds">
                                    <option value="0" <?php if (isset($_POST['nds']) && $_POST['nds'] == 0) echo 'selected="selected"'; ?> >Все</option>
                                    <option value="1" <?php if (isset($_POST['nds']) && $_POST['nds'] == 1) echo 'selected="selected"'; ?> >Без НДС</option>
                                    <option value="2" <?php if (isset($_POST['nds']) && $_POST['nds'] == 2) echo 'selected="selected"'; ?> >Включая НДС</option>
                                </select>
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_7" class="tbl_thead_search_button" title="Фильтр по местонахождению товара">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_7', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_7" class="tbl_thead_search_div">
                                Местонахождение: страна:<br>
                                <select name="country_from">
                                    <?php
                                        tzs_pr_build_countries('country_from');
                                    ?>
                                </select><br>
                                Местонахождение: регион:<br>
                                <select name="region_from">
                                    <option value="0">все области</option>
                                </select><br>
                                Местонахождение: город:<br>
                                <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>" size="30">
                            </div>
                    </th>
                    <th>
                        <div class="tbl_thead_search_button">
                            <a href="JavaScript:tblTHeadShowSearchForm();" title="Полная форма изменения условий поиска"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/search-1.png" width="24px" height="24px"></a>&nbsp;
                            <a href="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/eraser.png" width="24px" height="24px"></a>&nbsp;
                            <a href="javascript:onTblTheadButtonSearchClick();" title="Выполнить поиск по текущим условиям фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/find-1.png" width="24px" height="24px"></a>
                            <!--button type="button" id="tbl_thead_button_clear" onclick="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра">Очистить</button-->
                            <!--button type="button" id="tbl_thead_button_search" onclick="javascript:onTblTheadButtonSearchClick();" title="Выполнить поиск по текущим условиям фильтра">Искать</button-->
                        </div>
                    </th>
                </tr>
    </form>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="preloader">
        <img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/ajax-loader-3.gif" alt="Loading..."/>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="pages_container">

    </div>
<!------------------------------------------------------------------------->                        
    <div class="slide_panel">
        <?php 
            tzs_front_end_search_pr_form(); 
        ?>
    </div>
<!------------------------------------------------------------------------->                        
    <script src="/wp-content/plugins/tzs/assets/js/table_reload.js"></script>
    <?php
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

function aa () {
	$sp = tzs_validate_search_parameters();
	$errors = $sp['errors'];
	if (count($errors) > 0)
		print_errors($errors);
	?>
	<a href="javascript:showSearchDialog();" id="edit_search">Изменить параметры поиска</a>
    
	<?php
	
	if (count($errors) == 0) {
	
	$s_sql = tzs_search_parameters_to_sql($sp, 'tr');
	$s_title = tzs_search_parameters_to_str($sp);
	
	if (strlen($s_title) > 0) {
		?>
			<div id="search_info">Транспорт <?php echo $s_title; ?></div>
		<?php
	} else {
		?>
			<div id="search_info">Весь транспорт</div>
		<?php
	}
	
	$page = current_page_number();
	
	?>
	<a tag="page" id="realod_btn" href="<?php echo build_page_url($page); ?>">Обновить</a>
	<?php
	
	global $wpdb;
	
	$url = current_page_url();
	
	$pp = TZS_RECORDS_PER_PAGE;
	
	$sql = "SELECT COUNT(*) as cnt FROM ".TZS_TRUCK_TABLE." WHERE active=1 $s_sql;";
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
		$sql = "SELECT * FROM ".TZS_TRUCK_TABLE." WHERE active=1 $s_sql ORDER BY time DESC LIMIT $from,$pp;";
		$res = $wpdb->get_results($sql);
		if (count($res) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить список транспорта. Свяжитесь, пожалуйста, с администрацией сайта');
		} else {
			if (count($res) == 0) {
				?>
					<div style="clear: both;"></div>
                    <div class="errors">
                        <div id="info error">По Вашему запросу ничего не найдено.</div>
                    </div>
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
				<th id="wight">Вес</th>
				<th id="vol">Объем</th>
				<th id="type">Тип транспорта</th>
				<th id="cost">Цена</th>
				<th id="comm">Комментарии</th>
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
				doSearchDialog('transport', post, null);
			}
			
			jQuery(document).ready(function(){
				jQuery('#tbl_shipments').on('click', 'td', function(e) {  
					var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
					var id = this.parentNode.getAttribute("rid");
					if (!nonclickable)
						document.location = "/account/view-truck/?id="+id;
				});
				hijackLinks(post);
			});
		</script>
	<?php
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>