<?php

//include_once(TZS_PLUGIN_DIR.'/front-end/tzs.TblTbodyReload.php');

////////
add_action( 'wp_ajax_tzs_pr_get_regions', 'tzs_pr_get_regions_callback' );
add_action( 'wp_ajax_nopriv_tzs_pr_get_regions', 'tzs_pr_get_regions_callback' );

function tzs_pr_get_regions_callback() {
	$id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval( $_POST['id'] ) : 0;
	$rid = isset($_POST['rid']) && is_numeric($_POST['rid']) ? intval( $_POST['rid'] ) : 0;
	if ($id <= 0) {
		?>
			<option value="0">все области</option>
		<?php
	} else {
		global $wpdb;
		
		$sql = "SELECT * FROM ".TZS_REGIONS_TABLE." WHERE country_id=$id ORDER BY title_ru ASC;";
		$res = $wpdb->get_results($sql);
		if (count($res) == 0 && $wpdb->last_error != null) {
			?>
				<option value="0">все области</option>
			<?php
		} else {
			?>
				<option value="0">все области</option>
			<?php
			$found = false;
			foreach ( $res as $row ) {
				if (!$found) {
					$found = true;
					?>
						<option disabled>- - - - - - - -</option>
					<?php
				}
				$region_id = $row->region_id;
				$title = $row->title_ru;
				?>
					<option value="<?php echo $region_id;?>" <?php
						if ($rid == $region_id) {
							echo 'selected="selected"';
						}
					?> ><?php echo $title;?></option>
				<?php
			}
		}
	}
	wp_die();
}

//////////

function tzs_front_end_products_handler($atts) {
    // Определяем атрибуты 
    // [tzs-view-products rootcategory="1"] - указываем на странице раздела
    // [tzs-view-products] - указываем на страницах подразделов
    extract( shortcode_atts( array(
            'rootcategory' => '0',
    ), $atts, 'tzs-view-products' ) );
        
    ob_start();

    $p_id = get_the_ID();
    $p_title = the_title('', '', false);

    // Если указан параметр rootcategory, то выводим все товары раздела
    // Иначе - товары категории
    if ($rootcategory === '1') {
        //$sql1 = ' AND type_id IN ('.tzs_build_product_types_id_str($p_id).')';
        $p_name = '';
    } else {
        //$sql1 = ' AND type_id='.$p_id;
        $p_name = get_post_field( 'post_name', $p_id );
    }

    $page = current_page_number();

	?>
    <div>
        <table id="pr_info" width="100%">
            <tr>
                <td width="65px"></td>
                <td>
                    <div id="search_info"><?php 
                    echo $p_title;
                    //echo strlen($s_title) > 0 ?  ' * '. $s_title : '';
                    ?></div>
                </td>
                <td>
                    <div id="errors" class="errors" style="float: left;">
                    </div>
                </td>
                <td>
                    <div>
                        <button tag="page" id="realod_btn" onClick="javascript:TblTbodyReload()">Обновить</button>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<!------------------------------------------------------------------------->                        
    <div>
        <table  id="tbl_products">
            <thead>
    <form class="search_pr_form" name="search_pr_form1" method="POST">
                <tr>
                    <th id="tbl_products_id">Номер<br/>время заявки</th>
                    <th id="tbl_products_sale">
                        <div>
                            Покупка<br/>Продажа
                        </div>
                    </th>
                    <th id="tbl_products_cost">Участник тендера</th>
                    <th id="tbl_products_dtc">Период публикации</th>
                    <th id="tbl_products_title">Описание и фото товара</th>
                    <th id="tbl_products_price">Цена<br/>Форма оплаты<br/>Кол-во</th>
                    <th id="tbl_products_cities">Место нахождения</th>
                    <th id="tbl_products_comm">Контакты</th>
                </tr>
                <tr>
                    <th>
                        <div id="tbl_thead_search_button_1" class="tbl_thead_search_button" title="Фильтр по категории">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_1', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_1" class="tbl_thead_search_div">
                                Категория:<br>
                              <select name="type_id" <?php echo ($p_id == $pa_root_id) ? '' : ' disabled="disabled"'; ?> >
                                <option value="0">все категории</option>
                                <option disabled>- - - - - - - -</option>
                                <?php
                                    tzs_build_product_types('type_id', $pa_root_id);
                                ?>
                            </select>
                            <?php wp_nonce_field( 'type_id', 'type_id_nonce' ); ?>
                            </div>
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
                            <button type="button" id="tbl_thead_button_clear" onclick="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра">Очистить</button>
                            <button type="button" id="tbl_thead_button_search" onclick="javascript:onTblTheadButtonSearchClick();" title="Выполнить поиск по текущим условиям фильтра">Искать</button>
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
    <div id="slick-1">
        <?php tzs_front_end_search_pr_form(); ?>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="preloader">
        <img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/ajax-loader.gif" alt="Loading..."/>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="pages_container">

    </div>
<!------------------------------------------------------------------------->                        
                <?php
//                }

                //build_pages_footer($page, $pages);
//            }
//        }
//    }
////
    //echo '<div>'.tzs_front_end_TblTbodyReload($p_id, $rootcategory).'</div>';
/////
    ?>
    <script>
        var post = [];
        <?php
            echo "// POST dump here\n";
            foreach ($_POST as $key => $value) {
                echo "post[".tzs_encode2($key)."] = ".tzs_encode2($value).";\n";
            }
            if (!isset($_POST['type_id'])) {
                echo "post[".tzs_encode2("type_id")."] = ".tzs_encode2($p_id).";\n";
            }
            if (!isset($_POST['rootcategory'])) {
                echo "post[".tzs_encode2("rootcategory")."] = ".tzs_encode2($rootcategory).";\n";
            }
            if (!isset($_POST['cur_type_id'])) {
                echo "post[".tzs_encode2("cur_type_id")."] = ".tzs_encode2($p_id).";\n";
            }
            if (!isset($_POST['cur_post_name']) && ($p_name !== '')) {
                echo "post[".tzs_encode2("cur_post_name")."] = ".tzs_encode2($p_name).";\n";
            }
        ?>

        function doAjax(id, rid, to_el) {
            jQuery(to_el).attr("disabled", "disabled");
            jQuery(to_el).html('<option value=\"0\">Загрузка</option>');

            var data = {
                    'action': 'tzs_pr_get_regions',
                    'id': id,
                    'rid': rid
            };

            jQuery.post(ajax_url, data, function(response) {
                    jQuery(to_el).html(response);
                    jQuery(to_el).removeAttr("disabled");
                    enableDisable(to_el);
            }).fail(function(response) {
                    jQuery(to_el).html("<option value='0'>все области(!)</option>");
                    jQuery(to_el).removeAttr("disabled");
                    enableDisable(to_el);
            });
        }

        function enableDisable(obj) {
                if (jQuery(obj).children().length <= 1) {
                        jQuery(obj).attr("disabled", "disabled");
                } else {
                        jQuery(obj).removeAttr("disabled");
                }
        }

        function onCountryFromSelected() {
            var rid = <?php echo isset($_POST["region_from"]) ? $_POST["region_from"] : 0; ?>;
            doAjax(jQuery('[name=country_from]').val(), rid, jQuery('[name=region_from]'));

            if ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=cityname_from]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onCityNameFromSelected() {
            if ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=cityname_from]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onTypeIdSelected() {
            if (jQuery('[name=type_id]').val() > 0) {
                jQuery("#tbl_thead_search_button_1 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_1 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onSaleOrPurchaseSelected() {
            if (jQuery('[name=sale_or_purchase]').val() > 0) {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onFixedOrTenderSelected() {
            if (jQuery('[name=fixed_or_tender]').val() > 0) {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }        

        function onDataFromSelected() {
            if ((jQuery('[name=data_from]').val().length > 7) || (jQuery('[name=data_to]').val().length > 7)) {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }                

        function onPrTitleSelected() {
            if (jQuery('[name=pr_title]').val().length > 0) {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }                

        function onPriceFromSelected() {
            if ((jQuery('[name=payment]').val() > 0) || (jQuery('[name=nds]').val() > 0) || (jQuery('[name=price_from]').val().length > 0) || (jQuery('[name=price_to]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }
        
        function showUserContacts(obj, user_id, is_hide) {
            var container = jQuery('div[phone-user="'+user_id+'"]');
            var container1 = jQuery('div[phone-user-not-view="'+user_id+'"]');

            if (is_hide) {
                container.hide();
                container1.show();
            } else {
                container.find('a, b').hide();
                container.find('span').show();
            }
        }
        
        //
        function Form1ToFormCopy() {
            //jQuery('#search_pr_form1').find(':input').each(function(indx){
            jQuery('[name=search_pr_form1]').find(':input').each(function(indx){
            //jQuery('form#search_pr_form1.search_pr_form').find(':input').each(function(indx){
                if ((jQuery(this).val() > 0) || (jQuery(this).val().length > 0)) {
                    field_name  = jQuery(this).attr("name");
                    jQuery('name=search_pr_form').find(field_name).val(jQuery(this).val());
                }
            });
        }
        
        function onTblTheadButtonSearchClick() {
            Form1ToFormCopy();
            TblTbodyReload(false, <?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
        }
        
        function onTblTheadButtonClearClick() {
            //alert('onTblTheadButtonClearClick');
            //jQuery('[name=search_pr_form1]').trigger('reset');
            //jQuery('form[name="search_pr_form"]').find(':input').not(':button, :submit, :reset, :hidden').each(function(indx){
            //jQuery('form[name="search_pr_form"]').find('input[type=text]').each(function(indx){
            //    jQuery(this).attr("value", "");
            //});
            //jQuery('form[name="search_pr_form1"]').find('input[type=text]').val("");
            jQuery(".search_pr_form input[type=text]").val("");
                //.removeAttr('checked')
                //.removeAttr('selected');
                alert('onTblTheadButtonClearClick1');
            //Form1ToFormCopy();
            //TblTbodyReload(false, <?php //echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            //return false;
        }
        
        function TblTbodyReload(is_close_slick, page) {
            if (is_close_slick === true) { jQuery('.tab').click(); }
            
            if (page !== undefined) { addHidden("[name=search_pr_form]", 'page', page); }

            // Очистим
            jQuery("#errors").html('');
            jQuery("#search_info").html('');
            jQuery("#tbl_products tbody").html('');
            jQuery("#pages_container").html('');

            jQuery('#preloader').fadeIn('fast');
            //jQuery('#preloader').show();
            //jQuery('#tbl_products_search_status th').html('Подождите...Выполняется операция поиска записей...');

            fd = jQuery('form[name="search_pr_form"]').serialize();
            jQuery.ajax({
                url: "/wp-admin/admin-ajax.php?action=tzs_products_reload",
                type: "POST",
                data: fd,
                dataType: 'json',
                success: function(data) {
                    if (data.output_tbody !== 'undefined') {
                        jQuery("#tbl_products tbody").html(data.output_tbody);
                    }

                    if (data.output_info !== 'undefined') {
                        jQuery("#search_info").html(data.output_info);
                    }

                    if (data.output_error !== 'undefined') {
                        jQuery("#errors").html(data.output_error);
                        //jQuery("#errors").css('display', 'block');
                    }

                    if (data.output_pnav !== 'undefined') {
                        jQuery("#pages_container").html(data.output_pnav);
                    }

                    jQuery('#preloader').fadeOut('fast');
                },
                error: function(data) {
                    if (data.responsetext !== 'undefined') {
                        jQuery("#errors").html(data.responsetext);
                    }

                    jQuery('#preloader').fadeOut('fast');
                }			
            });		   
        }

        // Create a hidden input element, and append it to the form:
        function addHidden(theForm, key, value) {
            if (jQuery(theForm).find('input[type=hidden]').is('[name='+key+']') == false) {
                var input = jQuery('<input type="hidden"/>');
                jQuery(input).attr('name', key);
                jQuery(input).attr('value', value);
                jQuery(theForm).append(input);                        
            }
            else {
                jQuery(theForm+' [name='+key+']').attr('value', value);
            }
            /*var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;//'name-as-seen-at-the-server';
            input.value = value;

            theForm.appendChild(input);*/
        }

        function tblTHeadShowForm(div_id, div_class) {
                //jQuery(div_id).toggle();
            if (jQuery(div_id).is(':visible')) {
                jQuery(div_class).css('display', 'none');
            } else {
                jQuery(div_class).css('display', 'none');
                jQuery(div_id).css('display', 'block');
            }
        }

        jQuery(document).ready(function(){
                jQuery('#tbl_products').on('click', 'td', function(e) {  
                        var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                        var id = this.parentNode.getAttribute("rid");
                        if (!nonclickable && (this.cellIndex != 7))
                                document.location = "/account/view-product/?id="+id;
                });

                //
                //var theForm = document.forms['search_pr_form'];
                //var theForm = "[name=search_pr_form]";
                var theForm = "#search_pr_form";
                //addHidden(theForm, 'type_id', '<?php echo $p_id; ?>');
                addHidden(theForm, 'rootcategory', '<?php echo $rootcategory; ?>');
                addHidden(theForm, 'cur_type_id', '<?php echo $p_id; ?>');
                addHidden(theForm, 'cur_post_name', '<?php echo $p_name; ?>');
                addHidden(theForm, 'p_title', '<?php echo $p_title; ?>');
                jQuery('<tr><td>&nbsp;</td><td><button id="pr_search_button" onclick="javascript:TblTbodyReload()">Поиск</button></td></tr>').appendTo('.search_pr_form table');

                //
                jQuery('[name=country_from]').change(function() { onCountryFromSelected(); });
                jQuery('[name=cityname_from]').change(function() { onCityNameFromSelected(); });
                jQuery('[name=type_id]').change(function() { onTypeIdSelected(); });
                jQuery('[name=sale_or_purchase]').change(function() { onSaleOrPurchaseSelected(); });
                jQuery('[name=fixed_or_tender]').change(function() { onFixedOrTenderSelected(); });
                jQuery('[name=data_from]').change(function() { onDataFromSelected(); });
                jQuery('[name=data_to]').change(function() { onDataFromSelected(); });
                jQuery('[name=pr_title]').change(function() { onPrTitleSelected(); });
                jQuery('[name=price_from]').change(function() { onPriceFromSelected(); });
                jQuery('[name=price_to]').change(function() { onPriceFromSelected(); });
                jQuery('[name=payment]').change(function() { onPriceFromSelected(); });
                jQuery('[name=nds]').change(function() { onPriceFromSelected(); });
                //
                onCountryFromSelected();
                onTypeIdSelected();
                onSaleOrPurchaseSelected();
                onFixedOrTenderSelected();
                onDataFromSelected();
                onPrTitleSelected();
                onPriceFromSelected();
                //
                jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
                jQuery("[name=data_from]").datepicker({ dateFormat: "dd.mm.yy" });
                jQuery("[name=data_to]").datepicker({ dateFormat: "dd.mm.yy" });


                TblTbodyReload(false, 1);

                //hijackLinks(post);

                ///
                jQuery('#slick-1').dcSlick({
                    location: 'left',
                    align: 'top',
                    offset: '120px',
                    speed: 'slow',
                    tabText: '',
                    autoClose: false
                });

        });
    </script>
    <?php
////
    
    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}
?>