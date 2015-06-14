<?php


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

    ?>
<!------------------------------------------------------------------------->                        
    <div>
        <table  id="tbl_products">
            <thead>
    <form class="search_pr_form" id="search_pr_form2" name="search_pr_form1" method="POST">
                <tr id="tbl_thead_records_per_page">
                    <th colspan="4">
                        <?php if ($rootcategory === '1') { ?>
                            Категория товаров:
                            <select name="type_id" <?php echo ($rootcategory === '1') ? '' : ' disabled="disabled"'; ?> >
                                <option value="0">все категории</option>
                                <option disabled>- - - - - - - -</option>
                            <?php
                                tzs_build_product_types('type_id', TZS_PR_ROOT_CATEGORY_PAGE_ID);
                            ?>
                            </select>
                        <?php } ?>
                    </th>
                    <th></th>
                    <th colspan="3">
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
                    <th id="tbl_products_id">Номер<br/>время заявки</th>
                    <th id="tbl_products_sale">Покупка<br/>Продажа</th>
                    <th id="tbl_products_cost">Участник тендера</th>
                    <th id="tbl_products_dtc">Период публикации</th>
                    <th id="tbl_products_title">Название, описание и фото товара</th>
                    <th id="tbl_products_price">Цена<br/>Форма оплаты<br/>Кол-во</th>
                    <th id="tbl_products_cities">Место нахождения</th>
                    <th id="tbl_products_comm">Контакты</th>
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
    
    <script>
        var SearchFormVisible = false;
        

        function onCountryFromSelected() {
            var rid = <?php echo isset($_POST["region_from"]) ? $_POST["region_from"] : 0; ?>;
            doAjax(jQuery('[name=country_from]').val(), rid, jQuery('[name=region_from]'));
        }
        
        function onCountryToSelected() {
            var rid = <?php echo isset($_POST["region_to"]) ? $_POST["region_to"] : 0; ?>;
            doAjax(jQuery('[name=country_to]').val(), rid, jQuery('[name=region_to]'));
        }
        //
        function onForm1Change() {
            if (jQuery('[name=type_id]').val() > 0) {
                jQuery("#tbl_thead_search_button_1 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_1 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
            
            if (jQuery('[name=sale_or_purchase]').val() > 0) {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
            if (jQuery('[name=fixed_or_tender]').val() > 0) {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
            
            if ((jQuery('[name=data_from]').val().length > 7) || (jQuery('[name=data_to]').val().length > 7)) {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
            
            if (jQuery('[name=pr_title]').val().length > 0) {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
            
            if ((jQuery('[name=payment]').val() > 0) || (jQuery('[name=nds]').val() > 0) || (jQuery('[name=price_from]').val().length > 0) || (jQuery('[name=price_to]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
            
            if ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=cityname_from]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
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
            if (!SearchFormVisible) { jQuery('.slide_panel').animate({'left':'0'},600); }
            else { jQuery('.slide_panel').animate({'left':'-420'},500); }
            SearchFormVisible = ~ SearchFormVisible;
        }

        
        // Функция, отрабатывающая после готовности HTML-документа
        jQuery(document).ready(function(){
                // Установим обработчик "клика" в строках таблицы
                jQuery('#tbl_products').on('click', 'td', function(e) {  
                        var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                        var id = this.parentNode.getAttribute("rid");
                        if (!nonclickable && (this.cellIndex != 7))
                                document.location = "/account/view-product/?id="+id;
                });

                // Создадми скрытые поля для формы
                var theForm = "#search_pr_form1";
                //addHidden(theForm, 'type_id', '<?php echo $p_id; ?>');
                addHidden(theForm, 'rootcategory', '<?php echo $rootcategory; ?>');
                addHidden(theForm, 'cur_type_id', '<?php echo $p_id; ?>');
                addHidden(theForm, 'cur_post_name', '<?php echo $p_name; ?>');
                addHidden(theForm, 'p_title', '<?php echo $p_title; ?>');
                addHidden(theForm, 'records_per_page', '<?php echo isset($_POST['records_per_page']) ? $_POST['records_per_page'] : TZS_RECORDS_PER_PAGE; ?>');
                
                // Установим размеры для выезжающей панели с формой
                jQuery(".slide_panel").css({
                    'width': '360px',
                    'left': '-420px'
                });

                // Устанавливаем обработчики событий 
                setFormFielsdChangeHandler('search_pr_form');
                jQuery('[name=records_per_page]').change(function(eventObject) {
                    addHidden(theForm, 'records_per_page', eventObject.target.value);
                    TblTbodyReload(1); 
                });
                onForm1Change();
                //
                jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
                jQuery("[name=data_from]").datepicker({ dateFormat: "dd.mm.yy" });
                jQuery("[name=data_to]").datepicker({ dateFormat: "dd.mm.yy" });


                // Скроем форму
                if (SearchFormVisible) { tblTHeadShowSearchForm(); }
                // Обновим тело таблицы
                TblTbodyReload(1);

                //hijackLinks(post);

                ///
        });
    </script>
    <?php
////
    
    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}
?>