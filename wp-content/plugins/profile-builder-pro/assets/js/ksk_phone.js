/* 
 * =======================================================================================
 * ksk_phone
 * =======================================================================================
 * Ввод номеров телефонов в отдельном всплывающем блоке
 * Номер разбит на 3 части: код страны, код города/оператора, номер телефона
 * Код страны по умолчанию равен +38 и не изменяется
 * Для активации необходимо вызвать функцию ksk_phone_input, передав ей id поля для ввода,
 * из которого считываются и в который записываются номера через ;
 * =======================================================================================
 * Автор: Керимов Сергей
 * e-mail: serker@hotmail.ru
 * =======================================================================================
 */
       tzsMaxPhoneCount = 5;
        tzsPhoneCount = 1;
        tzsPhoneSaveId = '';
        
        function hide_phone() {
            jQuery('#ksk_phone_input_div').css('display', 'none');
        } // hide_phone
        
        function save_phone() {
            tzsPhoneRezultString = '';
            
            for(i=1;i<=tzsPhoneCount;i++) {
                if ((jQuery("#ksk_phone_2_" + i).val().length > 0) && (jQuery("#ksk_phone_3_" + i).val().length > 0)) {
                    if (i > 1) { tzsPhoneRezultString += ';'; }
                    tzsPhoneRezultString += jQuery("#ksk_phone_1_" + i).val() + jQuery("#ksk_phone_2_" + i).val() + jQuery("#ksk_phone_3_" + i).val();
                }
            }
            
            if (tzsPhoneSaveId !== '') {
                //alert(tzsPhoneSaveId);
                jQuery(tzsPhoneSaveId).val(tzsPhoneRezultString);
            }
            
            jQuery('#ksk_phone_input_div').css('display', 'none');
        } // save_phone
        
        function show_phone_row() {
            if (tzsPhoneCount < tzsMaxPhoneCount) {
                tzsPhoneCount++;
                jQuery('#ksk_phone_input_div table tr[rid="' + tzsPhoneCount + '"]').show();
            }
            
            if (tzsPhoneCount >= tzsMaxPhoneCount) {
                jQuery('#ksk_add_phone_button').attr('disabled', 'disabled');
            }
        } // show_phone_row
        
        function ksk_phone_input(id_elem) {
            tzsPhoneSaveId = '#' + id_elem;
            val = jQuery('#' + id_elem).val();
            if (val == '') { val_split = []; }
            else { val_split = val.split(';'); }
            
            // Создаем наш блок
            if (!jQuery('div').is('#ksk_phone_input_div')) {
                // Добавим div к документу
                jQuery('body').append('<div id="ksk_phone_input_div" style="display:none;"><span id="ksk_modal_close">X</span></div>');
                
                // Добавим стили для новых элементов
                vKskPhoneDivContent = '\
                    <div id="ksk_phone_1">\
                        <table>\
                            <tr>\
                                <th colspan="5">Введите номера телефонов</th>\
                            </tr>\
                            <tr>\
                                <th colspan="5">&nbsp;</th>\
                            </tr>\
                            <tr rid="1">\
                                <td><input class="ksk_phone_1" name="ksk_phone_1_1" type="text" id="ksk_phone_1_1" value="" maxlength="3" disabled="disabled"/></td>\
                                <td><input class="ksk_phone_2" name="ksk_phone_2_1" type="text" id="ksk_phone_2_1" value="" maxlength="3" /></td>\
                                <td><input class="ksk_phone_3" name="ksk_phone_3_1" type="text" id="ksk_phone_3_1" value="" maxlength="7" /></td>\
                                <td>&nbsp;&nbsp;&nbsp;</td>\
                                <td rowspan="' + tzsMaxPhoneCount + '">\
                                    <button type="button" id="ksk_add_phone_button" class="ksk_phone_button" onclick="javascript:show_phone_row()">Добавить один номер</button><br><br>\
                                    <button type="button" onclick="javascript:hide_phone()">Отменить</button>&nbsp;\
                                    <button type="button" id="ksk_save_phone_button" onclick="javascript:save_phone()">Сохранить</button>\
                                </td>\
                            </tr>';
                
                for(i=2;i<=tzsMaxPhoneCount;i++) {
                    vKskPhoneDivContent += '\
                            <tr rid="' + i + '">\
                                <td><input class="ksk_phone_1" name="ksk_phone_1_' + i + '" type="text" id="ksk_phone_1_' + i + '" value="+38" maxlength="3" disabled="disabled"/></td>\
                                <td><input class="ksk_phone_2" name="ksk_phone_2_' + i + '" type="text" id="ksk_phone_2_' + i + '" value="" maxlength="3" /></td>\
                                <td><input class="ksk_phone_3" name="ksk_phone_3_' + i + '" type="text" id="ksk_phone_3_' + i + '" value="" maxlength="7" /></td>\
                                <td>&nbsp;&nbsp;&nbsp;</td>\
                            </tr>';
                }
                
                    vKskPhoneDivContent += '\
                        </table>\
                    </div><!-- #ksk_phone_input_div -->';
                
                jQuery('#ksk_phone_input_div').html(vKskPhoneDivContent);
                
                jQuery('.ksk_phone_1, .ksk_phone_2, .ksk_phone_3').bind("change keyup input click", function() {
                    if (this.value.match(/[^0-9]/g)) {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    }
                });
            } else {
                jQuery(".ksk_phone_1, .ksk_phone_2, .ksk_phone_3").val('');
            }
            
            
            j = 0;
            for(i=0;i<val_split.length;i++) {
                j = i + 1;
                jQuery("#ksk_phone_1_" + j).val(val_split[i].substr(0,3));
                jQuery("#ksk_phone_2_" + j).val(val_split[i].substr(3,3));
                jQuery("#ksk_phone_3_" + j).val(val_split[i].substr(6));
                jQuery('#ksk_phone_input_div table tr[rid="' + j + '"]').show();
            }
            
            for(i=j+1;i<=tzsMaxPhoneCount;i++) {
                jQuery("#ksk_phone_1_" + i).val('+38');
                if (i > 1) {
                    jQuery('#ksk_phone_input_div table tr[rid="' + i + '"]').hide();
                }
            }
            
            jQuery('#ksk_phone_input_div')
                    .css('display', 'block') // убираем у модального окна display: none;
                    .animate({opacity: 1, top: '10%'}, 200); // плавно прибавляем прозрачность одновременно со съезжанием вниз
        } // ksk_phone_input
        
        // Функция, отрабатывающая после готовности HTML-документа
        jQuery(document).ready(function(){
            jQuery('#ksk_phone_input_div').css('display', 'none');

            jQuery('#user_name').bind("change keyup input click", function() {
                    if (this.value.match(/[^0-9a-zA-Z\-\_]/g)) {
                        this.value = this.value.replace(/[^0-9a-zA-Z\-\_]/g, '');
                    }
                });
            
            jQuery('#adduser, #edituser').submit(function(){
                jQuery('#input8').removeAttr('disabled');
                jQuery('#input12').removeAttr('disabled');
                jQuery('#input14').removeAttr('disabled');
           });
        });
