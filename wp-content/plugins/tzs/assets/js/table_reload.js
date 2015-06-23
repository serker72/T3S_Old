/* 
 * Функции для таблицы
 */
/*        function FormToFormCopy(form_from, form_to) {
            var curdate = new Date();
            //console.log("FormToFormCopy: run on " + curdate.toString() + ', form_from=' + form_from + ', form_to=' + form_to);
            if ((form_from !== undefined) && (form_from !== '') && (form_to !== undefined) && (form_to !== '')) {
                var elem = document.getElementsByName(form_from)[0].elements;
                for (i=0;i < elem.length;i++) {
                    if ((elem[i].type == 'text') || (elem[i].type == 'select-one')) {
                        //console.log(form_from + " before: " + elem[i].tagName + " " + elem[i].type + " " + elem[i].name + " " + elem[i].value);
                        var elem1 = document.getElementsByName(form_to)[0].elements.namedItem(elem[i].name);
                        //console.log(form_to + " before: " + elem1.tagName + " " + elem1.type + " " + elem1.name + " " + elem1.value);
                        
                        switch (elem[i].type) {
                            case 'select-one': {
                                if (elem[i].value > 0) { 
                                    elem1.value = elem[i].value; 
                                    //console.log("Update " + form_to + '.' + elem1.name + " new value: " + elem[i].value);
                                }
                                else if (elem1.value > 0) { 
                                    elem[i].value = elem1.value; 
                                    //console.log("Update " + form_from + '.' + elem[i].name + " new value: " + elem1.value);
                                }
                                break;
                            }
                            
                            case 'text': {
                                if (elem[i].value.length > 0) { 
                                    elem1.value = elem[i].value; 
                                    //console.log("Update " + form_to + '.' + elem1.name + " new value: " + elem[i].value);
                                }
                                else if (elem1.value.length > 0) { 
                                    elem[i].value = elem1.value;
                                    //console.log("Update " + form_from + '.' + elem[i].name + " new value: " + elem1.value);
                                }
                                break;
                            }
                        }
                        
                        //console.log(form_from + " after: " + elem[i].tagName + " " + elem[i].type + " " + elem[i].name + " " + elem[i].value);
                        //console.log(form_to + " after: " + elem1.tagName + " " + elem1.type + " " + elem1.name + " " + elem1.value);
                    }
                }
                
                onForm1Change();
            }
        }
*/        
        function doAjax(id, rid, to_el) {
            jQuery(to_el).attr("disabled", "disabled");
            jQuery(to_el).html('<option value=\"0\">Загрузка</option>');

            var data = {
                    'action': 'tzs_regions_reload',
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

        function FormClear(form_name) {
            if ((form_name !== undefined) && (form_name !== '')) {
                var elem = document.getElementsByName(form_name)[0].elements;
                for (i=0;i < elem.length;i++) {
                    if ((elem[i].type == 'text') || (elem[i].type == 'select-one') || (elem[i].type === 'checkbox') || (elem[i].type === 'radio')) {
                        //console.log(form_name + " before: " + elem[i].tagName + " " + elem[i].type + " " + elem[i].name + " " + elem[i].value);
                        if (elem[i].type == 'text') { elem[i].value = ''; }
                        //else if ((elem[i].type == 'checkbox') || (elem[i].type === 'radio')) { elem[i].checked = false; }
                        else { elem[i].value = 0; }
                        
                        //elem[i].change();
                        //console.log(form_name + " after: " + elem[i].tagName + " " + elem[i].type + " " + elem[i].name + " " + elem[i].value);
                    }
                }
                //console.log(form_name + " reset");
                //return false;
            }
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
        }

        function tblTHeadShowForm(div_id, div_class) {
            if ((div_id !== undefined) && (div_id !== '') && (jQuery(div_id).is(':visible'))) {
                jQuery(div_class).css('display', 'none');
            } else {
                jQuery(div_class).css('display', 'none');
                if ((div_id !== undefined) && (div_id !== '')) { jQuery(div_id).css('display', 'block'); }
            }
        }

        function onFormFieldChange(eventObject) {
            var curdate = new Date();
            //console.log("onFormFieldChange: run on " + curdate.toString() + ', this.name=' + this.name);
            var fid = eventObject.target.id;
            var fname = eventObject.target.name;
            var fval = eventObject.target.value;
            var ftype = eventObject.target.type;
            //console.log('id='+fid+', name='+fname+', val='+fval);
            //console.log(eventObject);
            if ((ftype === 'checkbox') || (ftype === 'radio')) {
                //jQuery('[name=' + fname + ']').prop('checked', jQuery('[name=' + fname + ']').is(':checked'));
            } else {
                jQuery('[name=' + fname + ']').attr('value', fval);
            }
            onForm1Change();
        }
        
        function setFormFielsdChangeHandler(form_name) {
            var curdate = new Date();
            //console.log("setFormFielsdChangeHandler: run on " + curdate.toString() + ', form_name=' + form_name);
            if ((form_name !== undefined) && (form_name !== '')) {
                var elem = document.getElementsByName(form_name)[0].elements;
                for (i=0;i < elem.length;i++) {
                    if ((elem[i].type == 'text') || (elem[i].type == 'select-one') || (elem[i].type == 'checkbox')) {
                        if (elem[i].name === 'country_from') {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); onCountryFromSelected(); });
                        } else if (elem[i].name === 'country_to') {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); onCountryToSelected(); });
                        } else if (elem[i].name === 'cargo_city_from') {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); onCityFromSelected(); });
                        } else if (elem[i].name === 'cargo_cityname_from') {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); onCityNameFromChanged(); });
                        } else if (elem[i].name === 'cargo_city_to') {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); onCityToSelected(); });
                        } else if (elem[i].name === 'cargo_city_from_radius_check') {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); onCityFromRadiusSelected(); });
                        } else {
                            jQuery('[name='+elem[i].name+']').change(function(eventObject) { onFormFieldChange(eventObject); });
                        }
                        //console.log("Set onChange " + form_name + "." + elem[i].name);
                    }
                }
            }
        }

        function TblTbodyReload(page) {
            if (page !== undefined) { addHidden("[name=search_pr_form]", 'page', page); }

            // Очистим
            //jQuery("#errors").html('');
            //jQuery("#search_info").html('');
            jQuery("#tbl_products tbody").html('');
            jQuery("#pages_container").html('');

            jQuery('#preloader').fadeIn('fast');
            //jQuery('#preloader').show();
            //jQuery('#tbl_products_search_status th').html('Подождите...Выполняется операция поиска записей...');

            fd = jQuery('form[name="search_pr_form"]').serialize();
            jQuery.ajax({
                url: "/wp-admin/admin-ajax.php?action=tzs_tables_reload",
                type: "POST",
                data: fd,
                dataType: 'json',
                success: function(data) {
                    if ((data.output_tbody !== 'undefined') && (data.output_tbody !== '')) {
                        jQuery("#tbl_products tbody").html(data.output_tbody);
                    }

                    if ((data.output_info !== 'undefined') && (data.output_info !== '')) {
                        //jQuery("#search_info").html(data.output_info);
                    }

                    if ((data.output_error !== 'undefined') && (data.output_error !== '')) {
                        //jQuery("#errors").html(data.output_error);
                        //jQuery("#errors").css('display', 'block');
                        var td_count = tbl_products.rows[1].cells.length;
                        var o_err = '<tr><td colspan="' + td_count + '"><div class="tbl_tbody_errors">' + data.output_error + '</div></td><\tr>';
                        jQuery("#tbl_products tbody").html(o_err);
                    }

                    if ((data.output_pnav !== 'undefined') && (data.output_pnav !== '')) {
                        jQuery("#pages_container").html(data.output_pnav);
                    }

                    jQuery('#preloader').fadeOut('fast');
                },
                error: function(data) {
                    if (data.responseText !== 'undefined') {
                        //jQuery("#errors").html(data.responsetext);
                        var td_count = tbl_products.rows[1].cells.length;
                        var o_err = '<tr><td colspan="' + td_count + '"><div class="tbl_tbody_errors">' + data.responseText + '</div></td><\tr>';
                        jQuery("#tbl_products tbody").html(o_err);
                    }

                    jQuery('#preloader').fadeOut('fast');
                }			
            });
        }

