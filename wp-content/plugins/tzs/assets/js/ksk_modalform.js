/* 
 * =======================================================================================
 * kskShowModalForm
 * =======================================================================================
 * Отображение формы в модальном окне с подложкой для затемнения страницы
 * 
 * Для использования функции необходимо выполнить подготовительные действия:
 * 1. создать на странице скрытый DIV (style="display: none") и поместить в него тело формы
 * <div id="xxx" style="display: none">
 *      <form name="???" method="POST">
 *      ......
 *      </form>
 * </div>
 * 2. создать ссылку для вызова окна с формой, href=javascript:kskShowModalForm
 * <a href="javascript:kskShowModalForm('id_ссылки', 'id_div', 'action'[, ширина_формы, высота_формы, флаг_закрытия]);" id="id_ссылки">Текст ссылки</a>
 * 
 * Обязательные параметры:
 * -----------------------
 * link_id - id ссылки (п. 2), при нажатии которой будет открываться модальное окно с формой
 * form_content_div_id - id div (п. 1), содержащего тело формы
 * form_action - ссылка для action формы
 * 
 * Необязательные параметры:
 * -------------------------
 * form_width - ширина формы, при отсутствии устанавливается 300px
 * form_height - высота формы, при отсутствии устанавливается 300px
 * overlay_click_is_close - флаг закрытия окна с формой при клике по подложке, при отсутствии = true
 * 
 * =======================================================================================
 * Автор: Керимов Сергей
 * e-mail: serker@hotmail.ru
 * =======================================================================================
 */

function kskShowModalForm(link_id, form_content_div_id, form_action, form_width, form_height, overlay_click_is_close) {
    if ((typeof(link_id) === 'undefined') || (typeof(link_id) !== 'string') || (link_id.trim() === '')) { 
        alert('Ошибка вызова функции kskShowModalForm: отсутствует обязательный параметр link_id.');
        return;
    }
    
    if ((typeof(form_content_div_id) === 'undefined') || (typeof(form_content_div_id) !== 'string')  || (form_content_div_id.trim() === '')) { 
        alert('Ошибка вызова функции kskShowModalForm: отсутствует обязательный параметр form_content_div_id.');
        return;
    }
    
    if ((typeof(form_action) === 'undefined') || (typeof(form_action) !== 'string')  || (form_action.trim() === '')) { 
        alert('Ошибка вызова функции kskShowModalForm: отсутствует обязательный параметр form_action.');
        return;
    }
    
    ss = '#' + form_content_div_id;
    if (!jQuery('div').is(ss)) {
//    if (!jQuery('div').is('#' + form_content_div_id)) {
        alert('Ошибка вызова функции kskShowModalForm: body не содержит элемент div id="' + form_content_div_id + '"');
        return;
    }
    
    // Ширина и высота формы
    if ((typeof(form_width) === 'undefined') || (typeof(form_width) !== 'number')) { form_width = 300; }
    if ((typeof(form_height) === 'undefined') || (typeof(form_height) !== 'number')) { form_height = 300; }
    
    if ((typeof(overlay_click_is_close) === 'undefined') || (typeof(overlay_click_is_close) !== 'boolean')) { overlay_click_is_close = true; }
    
    // Отступы слева и сверху
    margin_top = form_height/2*(-1);
    margin_left = form_width/2*(-1);
    
    // Добавим 2 новых div к документу
    if (!jQuery('div').is('#ksk_modal_form'))
        jQuery('body').append('<div id="ksk_modal_form" style="display:none;"><span id="ksk_modal_close">X</span></div>');
    //else
        //jQuery('div#ksk_modal_form').empty();

    if (!jQuery('div').is('#ksk_overlay'))
        jQuery('body').append('<div id="ksk_overlay" style="display:none;"></div>');
    //else
        //jQuery('div#ksk_overlay').empty();

    // Добавим стили для новых элементов
    jQuery('#ksk_modal_form').css({
        'background': '#fff',
        'border-radius': '5px',
        'border': '3px #000 solid',
        'width': form_width,
        'height': form_height,
        'margin-top': margin_top,
        'margin-left': margin_left,
        'position': 'fixed',
        'top': '45%',
        'left': '50%',
        'opacity': '0',
        'padding': '25px 10px',
        'z-index': '100000001'
    });

    jQuery('#ksk_modal_form #ksk_modal_close').css({
        'width': '21px',
        'height': '21px',
        'position': 'absolute',
        'top': '10px',
        'right': '10px',
        'cursor': 'pointer',
        'display': 'block'
    });

    jQuery('#ksk_overlay').css({
        'background-color': '#939393',
        'cursor': 'pointer',
        'width': '100%',
        'height': '100%',
        'position': 'fixed',
        'top': '0',
        'left': '0',
        'opacity': '0.8',
        'display': 'none',
        'z-index': '100000000'
    });
    
    // Добавим DIV с формой в новый DIV #ksk_modal_form
    jQuery('#ksk_modal_form').append(jQuery('#'+form_content_div_id));
    
    // Установим видимость DIV с формой, иначе мы его не увидим
    jQuery('#'+form_content_div_id).css('display', 'block');
    
    // Вся магия начинается после загрузки страницы
    jQuery(document).ready(function() {
        // Ловим клик на ссылку с id=link_id
        jQuery('a#'+link_id).click(function(event) {
            // Выключаем стандартную роль элемента
            event.preventDefault();
            jQuery('#ksk_overlay').fadeIn(400, // Вначале плавно показываем темную подложку
                function() { // После выполнения предыдущей анимации
                    jQuery('#ksk_modal_form')
                        .css('display', 'block') // убираем у модального окна display: none;
                        .animate({opacity: 1, top: '50%'}, 200); // плавно прибавляем прозрачность одновременно со съезжанием вниз
                }
            );
        });

        // Закрытие модального окна - все в обратном порядке
        // Если указан флаг overlay_click_is_close, то клик по подложке закрывает окно с формой
        if (overlay_click_is_close) { close_list = '#ksk_overlay, #ksk_modal_close'; }
        else { close_list = '#ksk_modal_close'; }
        
        // Ловим клик по крестику или подложке
        jQuery(close_list).click(function() {
            jQuery('#ksk_modal_form')
                .animate({opacity: 0, top: '45%'}, 200, // плавно меняем прозрачность на 0 и одновременно двигаем окно вверх
                function() { // После выполнения предыдущей анимации
                    jQuery(this).css('display', 'none'); // ставим модальному окну display: none;
                    jQuery('#ksk_overlay').fadeOut(400); // скрываем темную подложку
    
    // Установим видимость DIV с формой, иначе мы его не увидим
    jQuery('#'+form_content_div_id).css('display', 'none');
    
    // Добавим DIV с формой в новый DIV #ksk_modal_form
    jQuery('body').append(jQuery('#'+form_content_div_id));
                }
            );
        });
    });
}
