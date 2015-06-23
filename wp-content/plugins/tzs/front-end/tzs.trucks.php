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
                    <th colspan="4" id="tbl_thead_records_per_page_th">
                        <!--Количество записей на странице:&nbsp;&nbsp;&nbsp;
                        <?php //echo (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 5)) || (TZS_RECORDS_PER_PAGE == 5)) ? '' : '<a href="javascript:onRecordsPerPageSelected(5)">5</a>&nbsp;&nbsp;'; ?>
                        <?php //echo (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 5)) || (TZS_RECORDS_PER_PAGE == 10)) ? '' : '<a href="javascript:onRecordsPerPageSelected(10)">10</a>&nbsp;&nbsp;'; ?>
                        <?php //echo (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 5)) || (TZS_RECORDS_PER_PAGE == 15)) ? '' : '<a href="javascript:onRecordsPerPageSelected(15)">15</a>&nbsp;&nbsp;'; ?>
                        <?php //echo (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 5)) || (TZS_RECORDS_PER_PAGE == 20)) ? '' : '<a href="javascript:onRecordsPerPageSelected(20)">20</a>&nbsp;&nbsp;'; ?>
                        <select name="records_per_page" style="width: 50px;">
                            <option value="5" <?php //if (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 5)) || (TZS_RECORDS_PER_PAGE == 5)) echo 'selected="selected"'; ?> >5</option>
                            <option value="10" <?php //if (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 10)) || (TZS_RECORDS_PER_PAGE == 10)) echo 'selected="selected"'; ?> >10</option>
                            <option value="15" <?php //if (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 15)) || (TZS_RECORDS_PER_PAGE == 15)) echo 'selected="selected"'; ?> >15</option>
                            <option value="20" <?php //if (((isset($_POST['records_per_page']) && $_POST['records_per_page'] == 20)) || (TZS_RECORDS_PER_PAGE == 20)) echo 'selected="selected"'; ?> >20</option>
                        </select><br-->
                    </th>
                </tr>
                <tr>
                    <th id="tbl_trucks_id">N, дата и время заявки</th>
                    <th id="tbl_trucks_path" nonclickable="true">Пункт погрузки<br/>Пункт выгрузки</th>
                    <th id="tbl_trucks_dtc">Даты погрузки,<br>выгрузки</th>
                    <th id="tbl_trucks_wv">Вес, объем груза</th>
                    <th id="tbl_trucks_tc">Тип груза</th>
                    <th id="tbl_trucks_ttr">Тип транспорта</th>
                    <th id="tbl_trucks_cost">Цена,<br/>стоимость</th>
                    <th id="tbl_trucks_payment">Форма оплаты</th>
                    <th id="tbl_trucks_comm">Комментарии</th>
                    <th id="tbl_trucks_cont" nonclickable="true">Контактные данные</th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_2" class="tbl_thead_search_button" title="Фильтр по пунктам погрузки и выгрузки">
                            <!--img chk="1" src="<?php //echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px"-->
                            <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_2', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png"></a>
                            <label class="switch"><input id="chk_2" type="checkbox" value="1" name="k" disabled="disabled"><span class="switch"></span></label>
                        </div>
                        <div id="tbl_thead_search_div_2" class="tbl_thead_search_div">
                            <span style="float: left;padding: 5px 5px;">
                                Пункт погрузки: страна:<br>
                                <select name="country_from">
                                    <?php
                                        tzs_build_countries('country_from');
                                    ?>
                                </select><br>
                                Пункт погрузки: регион:<br>
                                <select name="region_from">
                                            <option>все области</option>
                                </select><br>
                                Пункт погрузки:&nbsp;<input type="checkbox" name="cargo_city_from" value="" <?php if (isset($_POST['cargo_city_from'])) echo 'checked="checked"'; ?>/>город<br>
                                <input type="text" name="cargo_cityname_from" value="<?php echo_val('cityname_from'); ?>" size="10"><br>
                                Пункт загрузки в радиусе<sup>*</sup>:&nbsp;<input type="checkbox" name="cargo_city_from_radius_check" value="" <?php if (isset($_POST['cargo_city_from_radius_check'])) echo 'checked="checked"'; ?>/><br>
                                <select name="cargo_city_from_radius_value">
                                    <?php
                                        foreach ($GLOBALS['tzs_city_from_radius_value'] as $key => $val) {
                                            echo '<option value="'.$key.'" ';
                                            if ((isset($_POST['cargo_city_from_radius_value']) && $_POST['cargo_city_from_radius_value'] == $key) || (!isset($_POST['cargo_city_from_radius_value']) && $key == 0)) {
                                                echo 'selected="selected"';
                                            }
                                            echo '>'.htmlspecialchars($val).'</option>';
                                        }
                                    ?>
                                </select><br>
                            </span>
                            <span style="float: right;padding: 5px 5px;">
                                Пункт выгрузки: страна:<br>
                                <select name="country_to">
                                    <?php
                                        tzs_build_countries('country_to');
                                    ?>
                                </select><br>
                                Пункт выгрузки: регион:<br>
                                <select name="region_to">
                                            <option>все области</option>
                                </select><br>
                                Пункт выгрузки:&nbsp;<input type="checkbox" name="cargo_city_to" value="" <?php if (isset($_POST['cargo_city_to'])) echo 'checked="checked"'; ?>/>город<br>
                                <input type="text" name="cargo_cityname_to" value="<?php echo_val('cargo_cityname_to'); ?>" size="10"><br><br>
                                <i><sup>*</sup>Для выбора радиуса укажите<br>страну и город пункта загрузки.</i>
                            </span>
                        </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_3" class="tbl_thead_search_button" title="Фильтр по датам погрузки и выгрузки">
                            <!--img chk="1" src="<?php //echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px"-->
                            <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_3', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png"></a>
                            <label class="switch"><input id="chk_3" type="checkbox" value="1" name="k" disabled="disabled"><span class="switch"></span></label>
                        </div>
                        <div id="tbl_thead_search_div_3" class="tbl_thead_search_div">
                            Дата погрузки:<br>
                            <input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="10"><br>
                            Дата выгрузки:<br>
                            <input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="10">
                        </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_4" class="tbl_thead_search_button" title="Фильтр по весу и объему груза">
                            <!--img chk="1" src="<?php //echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px"-->
                            <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_4', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png"></a>
                            <label class="switch"><input id="chk_4" type="checkbox" value="1" name="k" disabled="disabled"><span class="switch"></span></label>
                        </div>
                        <div id="tbl_thead_search_div_4" class="tbl_thead_search_div">
                            Масса: от:<br>
                            <select name="weight_from">
                                    <?php tzs_print_weight('weight_from'); ?>
                            </select><br>
                            Масса: до:<br>
                            <select name="weight_to">
                                    <?php tzs_print_weight('weight_to'); ?>
                            </select><br>
                            Объем: от:<br>
                            <select name="volume_from">
                                    <?php tzs_print_volume('volume_from'); ?>
                            </select><br>
                            Объем: до:<br>
                            <select name="volume_to">
                                    <?php tzs_print_volume('volume_to'); ?>
                            </select>
                        </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_5" class="tbl_thead_search_button" title="Фильтр по типу груза">
                            <!--img chk="1" src="<?php //echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px"-->
                            <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_5', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png"></a>
                            <label class="switch"><input id="chk_5" type="checkbox" value="1" name="k" disabled="disabled"><span class="switch"></span></label>
                        </div>
                        <div id="tbl_thead_search_div_5" class="tbl_thead_search_div">
                            Тип груза:<br>
                            <select name="sh_type">
                                <?php
                                    foreach ($GLOBALS['tzs_sh_types_search'] as $key => $val) {
                                            echo '<option value="'.$key.'" ';
                                            if ((isset($_POST['sh_type']) && $_POST['sh_type'] == $key) || (!isset($_POST['sh_type']) && $key == 0)) {
                                                    echo 'selected="selected"';
                                            }
                                            echo '>'.htmlspecialchars($val).'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_6" class="tbl_thead_search_button" title="Фильтр по типу транспортного средства">
                            <!--img chk="1" src="<?php //echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px"-->
                            <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_6', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png"></a>
                            <label class="switch"><input id="chk_6" type="checkbox" value="1" name="k" disabled="disabled"><span class="switch"></span></label>
                        </div>
                            <div id="tbl_thead_search_div_6" class="tbl_thead_search_div">
                                Тип транспорта:<br>
                                <select name="trans_type">
                                    <?php
                                        foreach ($GLOBALS['tzs_tr_types_search'] as $key => $val) {
                                                echo '<option value="'.$key.'" ';
                                                if ((isset($_POST['trans_type']) && $_POST['trans_type'] == $key) || (!isset($_POST['trans_type']) && $key == 0)) {
                                                        echo 'selected="selected"';
                                                }
                                                echo '>'.htmlspecialchars($val).'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                    </th>
                    <th>
                    </th>
                    <th>
                    </th>
                    <th>
                    </th>
                    <th>
                        <div class="tbl_thead_search_button_1">
                            <a href="JavaScript:tblTHeadShowSearchForm();" title="Полная форма изменения условий поиска"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/search-1.png" width="24px" height="24px"></a>&nbsp;
                            <a href="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/eraser.png" width="24px" height="24px"></a>&nbsp;
                            <a href="javascript:onTblTheadButtonSearchClick();" title="Выполнить поиск по текущим условиям фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/find-1.png" width="24px" height="24px"></a>
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
    <!--div class="slide_panel"-->
    <div id="slideout">
        <img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/search-1.png" width="32px" height="32px" alt="Форма поиска"></a>
        <div id="slideout_inner">
        <?php 
            tzs_front_end_search_tr_form('transport'); 
        ?>
        </div>
    </div>
<!------------------------------------------------------------------------->                        
    <script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
    <script src="/wp-content/plugins/tzs/assets/js/table_reload.js"></script>
    
    <script>
        var SearchFormVisible = false;
        

        function onCountryFromSelected() {
            var rid = <?php echo isset($_POST["region_from"]) ? $_POST["region_from"] : 0; ?>;
            doAjax(jQuery('[name=country_from]').val(), rid, jQuery('[name=region_from]'));
                        
            if (jQuery('[name=cargo_cityname_from]').val().length > 2 && jQuery('[name=country_from]').val() > 0) {
                jQuery('[name=cargo_city_from_radius_check]').removeAttr('disabled');

                if (jQuery('[name=cargo_city_from_radius_check]').is(':checked')) {
                    jQuery('[name=cargo_city_from_radius_value]').removeAttr('disabled');
                } else {
                    jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                }
            } else {
                jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
                jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
                jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
            }
        }
        
        function onCountryToSelected() {
            var rid = <?php echo isset($_POST["region_to"]) ? $_POST["region_to"] : 0; ?>;
            doAjax(jQuery('[name=country_to]').val(), rid, jQuery('[name=region_to]'));
        }
		
        function onCityFromSelected() {
            if (jQuery('[name=cargo_city_from]').is(':checked')) {
                    jQuery('[name=cargo_cityname_from]').removeAttr('disabled');

                if (jQuery('[name=cargo_cityname_from]').val().length > 2 && jQuery('[name=country_from]').val() > 0) {
                    jQuery('[name=cargo_city_from_radius_check]').removeAttr('disabled');

                    if (jQuery('[name=cargo_city_from_radius_check]').is(':checked')) {
                        jQuery('[name=cargo_city_from_radius_value]').removeAttr('disabled');
                    } else {
                        jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                    }
                } else {
                    jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
                    jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
                    jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                }
            } else {
                    jQuery('[name=cargo_cityname_from]').attr('disabled', 'disabled');

                    jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
                    jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
                    jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
            }
        }
        
        function onCityToSelected() {
                if (jQuery('[name=cargo_city_to]').is(':checked')) {
                        jQuery('[name=cargo_cityname_to]').removeAttr('disabled');
                } else {
                        jQuery('[name=cargo_cityname_to]').attr('disabled', 'disabled');
                }
        }

        function onCityNameFromChanged() {
                if (jQuery('[name=cargo_cityname_from]').val().length > 2 && jQuery('[name=country_from]').val() > 0) {
                        jQuery('[name=cargo_city_from_radius_check]').removeAttr('disabled');
                } else {
                        jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
                        jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
                        jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                }
        }

        function onCityFromRadiusSelected() {
                if (jQuery('[name=cargo_city_from_radius_check]').is(':checked')) {
                        jQuery('[name=cargo_city_from_radius_value]').removeAttr('disabled');
                } else {
                        jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                }
        }
        //
        function onForm1Change() {
            // chk_2
            jQuery('#chk_2').prop('checked', ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=country_to]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=region_to]').val() > 0) ||
                    (jQuery('[name=cargo_city_from]').is(':checked') && (jQuery('[name=cargo_cityname_from]').val().length > 0)) ||
                    (jQuery('[name=cargo_city_to]').is(':checked') && (jQuery('[name=cargo_cityname_to]').val().length > 0)) ||
                    (jQuery('[name=cargo_city_from_radius_check]').is(':checked') && (jQuery('[name=cargo_city_from_radius_value]').val().length > 0))));
            if (jQuery('#chk_2').is(':checked')) {
                jQuery('#chk_2').removeAttr('disabled');
            } else {
                jQuery('#chk_2').attr('disabled', 'disabled');
            }
            
            /*if ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=country_to]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=region_to]').val() > 0) ||
                    (jQuery('[name=cargo_city_from]').is(':checked') && (jQuery('[name=cargo_cityname_from]').val().length > 0)) ||
                    (jQuery('[name=cargo_city_to]').is(':checked') && (jQuery('[name=cargo_cityname_to]').val().length > 0)) ||
                    (jQuery('[name=cargo_city_from_radius_check]').is(':checked') && (jQuery('[name=cargo_city_from_radius_value]').val().length > 0))) {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }*/
            
            // chk_3
            jQuery('#chk_3').prop('checked', ((jQuery('[name=data_from]').val().length > 7) || (jQuery('[name=data_to]').val().length > 7)));
            if (jQuery('#chk_3').is(':checked')) {
                jQuery('#chk_3').removeAttr('disabled');
            } else {
                jQuery('#chk_3').attr('disabled', 'disabled');
            }
            
            /*if ((jQuery('[name=data_from]').val().length > 7) || (jQuery('[name=data_to]').val().length > 7)) {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }*/
            
            // chk_4
            jQuery('#chk_4').prop('checked', ((jQuery('[name=weight_from]').val() > 0) || (jQuery('[name=weight_to]').val() > 0) || (jQuery('[name=volume_from]').val() > 0) || (jQuery('[name=volume_to]').val() > 0)));
            if (jQuery('#chk_4').is(':checked')) {
                jQuery('#chk_4').removeAttr('disabled');
            } else {
                jQuery('#chk_4').attr('disabled', 'disabled');
            }
            
            /*if ((jQuery('[name=weight_from]').val() > 0) || (jQuery('[name=weight_to]').val() > 0) || (jQuery('[name=volume_from]').val() > 0) || (jQuery('[name=volume_to]').val() > 0)) {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }*/
            
            // chk_5
            jQuery('#chk_5').prop('checked', (jQuery('[name=sh_type]').val() > 0));
            if (jQuery('#chk_5').is(':checked')) {
                jQuery('#chk_5').removeAttr('disabled');
            } else {
                jQuery('#chk_5').attr('disabled', 'disabled');
            }
            
            /*if (jQuery('[name=sh_type]').val() > 0) {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }*/
            
            // chk_6
            jQuery('#chk_6').prop('checked', (jQuery('[name=trans_type]').val() > 0));
            if (jQuery('#chk_6').is(':checked')) {
                jQuery('#chk_6').removeAttr('disabled');
            } else {
                jQuery('#chk_6').attr('disabled', 'disabled');
            }
            
            /*if (jQuery('[name=trans_type]').val() > 0) {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }*/
        }
        
        function onClearFilterSelected(eventObject) {
            var fid = eventObject.target.id;
            var fname = eventObject.target.name;
            var fchk = eventObject.target.checked;
            
            if (!fchk) {
                switch (fid) {
                    case 'chk_2': {
                        jQuery('[name=country_from]').attr('value', 0);
                        jQuery('[name=country_to]').attr('value', 0);
                        jQuery('[name=region_from]').attr('value', 0);
                        jQuery('[name=region_to]').attr('value', 0);
                        jQuery('[name=cargo_city_from_radius_value]').attr('value', 0);
                        jQuery('[name=cargo_cityname_from]').attr('value', '');
                        jQuery('[name=cargo_cityname_to]').attr('value', '');
                        jQuery('[name=cargo_city_from]').prop('checked', false);
                        jQuery('[name=cargo_city_to]').prop('checked', false);
                        jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
                        jQuery('[name=cargo_cityname_from]').attr('disabled', 'disabled');
                        jQuery('[name=cargo_cityname_to]').attr('disabled', 'disabled');
                        jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                        jQuery('#chk_2').attr('disabled', 'disabled');
                        break;
                    }
                    case 'chk_3': {
                        jQuery('[name=data_from]').attr('value', '');
                        jQuery('[name=data_to]').attr('value', '');
                        jQuery('#chk_3').attr('disabled', 'disabled');
                        break;
                    }
                    case 'chk_4': {
                        jQuery('[name=weight_from]').attr('value', 0);
                        jQuery('[name=weight_to]').attr('value', 0);
                        jQuery('[name=volume_from]').attr('value', 0);
                        jQuery('[name=volume_to]').attr('value', 0);
                        jQuery('#chk_4').attr('disabled', 'disabled');
                        break;
                    }
                    case 'chk_5': {
                        jQuery('[name=sh_type]').attr('value', 0);
                        jQuery('#chk_5').attr('disabled', 'disabled');
                        break;
                    }
                    case 'chk_6': {
                        jQuery('[name=trans_type]').attr('value', 0);
                        jQuery('#chk_6').attr('disabled', 'disabled');
                        break;
                    }
                }
            }
        }
        
        function onTblTheadButtonSearchClick() {
            tblTHeadShowForm('', '.tbl_thead_search_div');
            if (SearchFormVisible) { tblTHeadShowSearchForm(); }
            //FormToFormCopy("search_pr_form1", "search_pr_form");
            TblTbodyReload(<?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            // Для исключения повторного обновления страницы - return false
            //return false;
        }
        
        function onTblTheadButtonClearClick() {
            FormClear("search_pr_form");
            FormClear("search_pr_form1");
            onForm1Change();
            tblTHeadShowForm('', '.tbl_thead_search_div');
            if (SearchFormVisible) { tblTHeadShowSearchForm(); }
            TblTbodyReload(<?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            // Для исключения повторного обновления страницы - return false
            //return false;
        }
        
        function onTblSearchButtonClick() {
            tblTHeadShowForm('', '.tbl_thead_search_div');
            //FormToFormCopy("search_pr_form", "search_pr_form1");
            TblTbodyReload(<?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            // Скроем форму
            tblTHeadShowSearchForm();
            // Для исключения повторного обновления страницы - return false
            //return false;
        }
        
        function tblTHeadShowSearchForm() {
            if (!SearchFormVisible) { 
                //jQuery('.slide_panel').animate({'left':'0'},600); 
                jQuery('#slideout').stop().animate({left: 385}, 1000);
                jQuery('#slideout_inner').stop().animate({left: 0}, 1000);
            }
            else { 
                //jQuery('.slide_panel').animate({'left':'-420'},500); 
                jQuery('#slideout').stop().animate({left: 0}, 'slow');
                jQuery('#slideout_inner').stop().animate({left: -385}, 'slow');
            }
            SearchFormVisible = ~ SearchFormVisible;
        }
  
        function thRecordsPerPagePrint(records_per_page) {
            var vTZS_RECORDS_PER_PAGE = <?php echo TZS_RECORDS_PER_PAGE; ?>;
            var vRecordsArray = [5,10,15,20];
            var vRecordsStr = 'Количество записей на странице:&nbsp;&nbsp;&nbsp;';
            
            if (!records_per_page || (records_per_page < 1)) { records_per_page = vTZS_RECORDS_PER_PAGE; }
            
            for(i=0;i<vRecordsArray.length;i++) {
                if (vRecordsArray[i] != records_per_page) {
                    vRecordsStr += '<a href="javascript:onRecordsPerPageSelected(' + vRecordsArray[i] + ')">' + vRecordsArray[i] + '</a>&nbsp;&nbsp;';
                }
            }
            
            jQuery("#tbl_thead_records_per_page_th").html(vRecordsStr);
        }
  
        function onRecordsPerPageSelected(records_per_page) {
            addHidden("#search_pr_form1", 'records_per_page', records_per_page);
            TblTbodyReload(1);
            thRecordsPerPagePrint(records_per_page);
        }

        
        // Функция, отрабатывающая после готовности HTML-документа
        jQuery(document).ready(function(){
                // Установим обработчик "клика" в строках таблицы
                jQuery('#tbl_products').on('click', 'td', function(e) {  
                        var nonclickable = 'true' == e.delegateTarget.rows[1].cells[this.cellIndex].getAttribute('nonclickable');
                        var id = this.parentNode.getAttribute("rid");
                        if (!nonclickable) {
                                document.location = "/account/view-truck/?id="+id;
                        }
                });

                // Создадми скрытые поля для формы
                var theForm = "#search_pr_form1";
                addHidden(theForm, 'form_type', 'trucks');
                addHidden(theForm, 'records_per_page', '<?php echo isset($_POST['records_per_page']) ? $_POST['records_per_page'] : TZS_RECORDS_PER_PAGE; ?>');
                
                // Установим размеры для выезжающей панели с формой
                /*jQuery(".slide_panel").css({
                    'width': '360px',
                    'left': '-420px'
                });*/
                
                jQuery("#slideout, #slideout_inner").css({
                    'top': '110px'
                });

                // Устанавливаем обработчики событий 
                setFormFielsdChangeHandler('search_pr_form');
                jQuery('#chk_2, #chk_3, #chk_4, #chk_5, #chk_6').change(function(eventObject) { onClearFilterSelected(eventObject); });
                
                jQuery('#slideout').hover(
                    function() {
                        //jQuery('#slideout').stop().animate({left: 385}, 1000);
                        //jQuery('#slideout_inner').stop().animate({left: 0}, 1000);
                        if (!SearchFormVisible) { tblTHeadShowSearchForm(); }
                    }, 
                    function() {
                        //jQuery('#slideout').stop().animate({left: 0}, 'slow');
                        //jQuery('#slideout_inner').stop().animate({left: -385}, 'slow');
                    }
                );
                
                /*jQuery('[name=records_per_page]').change(function(eventObject) {
                    addHidden(theForm, 'records_per_page', eventObject.target.value);
                    TblTbodyReload(1); 
                });*/
                onForm1Change();
                onCountryFromSelected();
		onCountryToSelected();
			//onCityFromSelected();
                        //onCityNameFromChanged();
			//onCityToSelected();
                        //onCityFromRadiusSelected();
                //
                jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
                jQuery("[name=data_from]").datepicker({ dateFormat: "dd.mm.yy" });
                jQuery("[name=data_to]").datepicker({ dateFormat: "dd.mm.yy" });


                // Скроем форму
                if (SearchFormVisible) { tblTHeadShowSearchForm(); }
                
                // Обновим тело таблицы
                TblTbodyReload(1);
                thRecordsPerPagePrint(<?php echo isset($_POST['records_per_page']) ? $_POST['records_per_page'] : TZS_RECORDS_PER_PAGE; ?>);
        });
    </script>
    
    <?php
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}
?>