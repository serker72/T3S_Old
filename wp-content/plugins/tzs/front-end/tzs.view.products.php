<?php
function tzs_front_end_view_productsd_handler($atts) {
	ob_start();
	
	global $wpdb;
	
	$user_id = get_current_user_id();
	
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
	
	if ($sh_id <= 0) {
		print_error('Тендер не найден');
	} else {
		$sql = "SELECT * FROM ".TZS_PRODUCTS_TABLE." WHERE id=$sh_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить информацию о тендер. Свяжитесь, пожалуйста, с администрацией сайта.');
		} else if ($row == null) {
			print_error('Тендер не найден');
		} else {
                    
                    ?>
<script src="/wp-content/plugins/tzs/assets/js/table_reload.js"></script>
<!--            <div style='clear: both'>
            <br /> -->
<?php
    $active=" AND active=1";
    $userid=" AND user_id=".$user_id;
    $sql = "SELECT COUNT(*) FROM ".TZS_PRODUCT_RATES_TABLE." WHERE product_id=$sh_id $userid $active";
    /*$max = $wpdb->get_results($sql); */
    $cnt_rate=$wpdb->get_var($sql);
     
    // Отбор ставок по тендеру - active=1 AND 
    $sql = "SELECT MAX(rate) FROM ".TZS_PRODUCT_RATES_TABLE." WHERE product_id=$sh_id $userid $active";
    /*$max = $wpdb->get_results($sql); */
     $max_rate=$wpdb->get_var($sql);
     if ($max_rate > 0) echo "<input id='act_rate_user' style='display: none;' value=".round($max_rate,2)." />";
     else echo "<input id='act_rate_user' style='display: none;' value=".$row->price." />";
     
    
?>                                   
            <div class="row-fluid" id="contact-block-right" >
                <div class="span2 offset10">
                    <?php
                        echo "<img src='".get_user_meta($row->user_id, 'company_logo',true)."'/>";
                        $form_type = get_param_def('form_type', '');
                        echo tzs_print_user_contacts($row, $form_type);
                    ?>
                    <?php 
                if(isset($_GET['spis'])) echo "<a id='edit_search' href='/account/my-products/'>Назад к списку</a> <div style='clear: both'></div>";
                    else echo "<button id='edit_search'  onclick='history.back()'>Назад к списку</button> <div style='clear: both'></div>";
            ?>
            <?php if (($user_id != $row->user_id) && ($user_id != 0) && ($row->fixed_or_tender == 2) && ($cnt_rate <= 0)) {?>
                <button   id="bet_button" type="button" onclick="bet_click();" <?php echo ($user_id === 0) ? 'disabled="disabled"' : ''; ?>>Сделать ставку</button>
            <?php } ?>
            <?php if (($user_id != $row->user_id) && ($user_id != 0) && ($row->fixed_or_tender == 2) && ($cnt_rate > 0)) {?>
                <button   id="bet_button" type="button" onclick="bet_click();" <?php echo ($user_id === 0) ? 'disabled="disabled"' : ''; ?>>Изменить цену</button>
            <?php } ?>
            <?php if (($user_id != $row->user_id) && ($row->fixed_or_tender == 1)) {?>
                <button   id="buy_button" type="button" onClick="javascript: window.location.href = '/company/?id=<?php echo $row->user_id;?>';" >Купить сейчас</button>
            <?php } ?>
            <?php if (($user_id == $row->user_id)) {?>
                <div style="margin-top: 15px;">
                    <a id="view_edit"  onClick="javascript: window.location.href = '/account/edit-auction/?id=<?php echo $row->id;?>';">Изменить</a>
                </div>
            <?php } ?>
                </div>
            </div>
            <div class="container" id="product-container">
            
    <div class="row-fluid" >
        <div class="span4" id="img_kart">
        <?php 
             if (strlen($row->image_id_lists) > 0) {
            //$img_names = explode(';', $row->pictures);
            $img_names = explode(';', $row->image_id_lists);
            $main_image_id = $row->main_image_id;
            if (count($img_names) > 0) {
        ?>
        <?php
            // Вначале выведем главное изображение
            $attachment_info = wp_get_attachment_image_src($main_image_id, 'full');
            if ($attachment_info !== false) { ?>
            <ul class="thumbnails"  style="max-height: 470px;">
                <li class="span12">
                    <a href="#" class="thumbnail" id="general-img">
                        <img id="general" src=<?php echo $attachment_info[0]; ?> alt="">
                    </a>
                </li>
            </ul>
            
                    
            <?php } ?>
            <ul class="thumbnails">
                <li class="span3">
                    <a href="#" class="thumbnail" >
                        <img src=<?php echo $attachment_info[0];?> alt="" onclick="clickSmallImage(this);">
                    </a>
                </li>
            <?php
            // Затем выведем все остальные изображения
            for ($i=0;$i<count($img_names);$i++) {
                if ($img_names[$i] !== $main_image_id) {
                    $attachment_info = wp_get_attachment_image_src($img_names[$i], 'full');
                    //if (file_exists(ABSPATH . $img_names[$i])) {
                    if ($attachment_info !== false) { ?>
                        <li class="span3">
                            <a href="#" class="thumbnail" >
                                <img src=<?php echo $attachment_info[0];?> alt="" onclick="clickSmallImage(this);">
                            </a>
                        </li>
                        
                    <?php }
                }
            }
            
        ?>
            </ul>
            <?php }} else { ?>
                    <ul class="thumbnails"  style="max-height: 470px;">
                        <li class="span12">
                            <a href="#" class="thumbnail" id="general-img">
                                <img id="general" src="/wp-content/themes/twentytwelve/image/360x270.png" alt="">
                            </a>
                        </li>
                    </ul>
                <?php }?>
            
        </div>
        <div class="span6" id="descript">
            <div class="well well-large">
                <div id="bet">
                    <form name="bet_form" id="bet_form" >
                        <fieldset>
                            
                            <div id="bet_error" style="color: red;"><?php if (($row->fixed_or_tender != 1)) echo ($user_id === 0) ? '<p>Для участия в тендере необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></p>' : '';?></div>
                            
                            <?php
                            if (($user_id == 0)) { ?>
                                  <!--  <div class="pull-left label-txt">
                                        <label><strong>Актуальная цена:</strong></label>
                                    </div>
                                    <div class="pull-left label-txt">
                                        <strong><div id="tek_price"></div></strong>
                                    </div> -->
                             <?php }  
                            
                            if ($user_id == $row->user_id) {?>
                                <!-- <div class="pull-left label-txt">
                                    <label><strong>Актуальная цена:</strong></label>
                                      <center><strong><div id="tek_price"></div></strong></center>
                                </div> -->
                                <div class="pull-left label-txt" style="min-width: 60px;">
                                    <label><strong>Предложений:</strong></label> 
                                    <strong><div id="tek_stavki"></div></strong> 
                                </div>
                            <?php }?>
                            
                            <?php
                            if (($user_id != $row->user_id) && ($user_id != 0)) {
                                if (($row->fixed_or_tender == 1)) { ?>
                                    <!-- <div class="pull-left label-txt">
                                        <label><strong>Актуальная цена:</strong></label>
                                    </div>
                                    <div class="pull-left label-txt">
                                        <strong><div id="tek_price"></div></strong>
                                    </div> -->
                            <?php }
                                if ($row->fixed_or_tender == 2) { ?>
                                <!-- <div class="pull-left label-txt">
                                    <label><strong>Актуальная цена:</strong></label>
                                      <center><strong><div id="tek_price"></div></strong></center>
                                </div> -->
                                <?php  if ($cnt_rate <= 0) { ?>
                                    <div class="pull-left" style="padding-left: 10px;">
                                        <strong>Ваша цена:</strong>
                                        <div class="clearfix"></div>
                                        <input id="bet_user" type="number" style="width: 45%;" <?php echo ($user_id === 0) ? 'disabled="disabled"' : ''; ?>/>
                                    </div>
                                <?php }?>
                                <?php  if ($cnt_rate > 0) { ?>
                                    <div class="pull-left" style="padding-left: 10px;">
                                        <strong>Изменить цену:</strong>
                                        <div class="clearfix"></div>
                                        <input id="bet_user" type="number" value="<?php echo $max_rate; ?>" style="width: 45%;" <?php echo ($user_id === 0) ? 'disabled="disabled"' : ''; ?>/>
                                    </div>
                                    
                                <?php }?>
                                    <div class="pull-left" style="padding-left: 10px; width: 50%">
                                        <strong>Комментарий к ставке</strong>
                                        <div class="clearfix"></div>
                                        <textarea id="text_bet" class="bet-area" rows="1" cols="75" name="text-bet" placeholder="Текст ставки"></textarea>
                                    </div>          
                                <?php }?>
                            <?php } ?>
                            <input id="user_id" style="display: none;" value=<?php echo $user_id; ?> />
                            <input id="user_name" style="display: none;" value=<?php $meta = get_user_meta($row->user_id, 'fio'); echo $meta[0]; ?> />
                            <input id="auction_id" style="display: none;" value=<?php echo $sh_id; ?> />
                            <input id="created" style="display: none;" value=<?php echo date("Y-m-d H:i:s"); ?> />
                            <input id="currency" style="display: none;" value="<?php echo $GLOBALS['tzs_pr_curr'][$row->currency];?>" />
                            <input id="title-company" style="display: none;" value="<?php echo $row->title; ?>" />
                            <div class="clearfix"></div>
                        </fieldset>
                    </form>
                </div>
                <div id="labeltxt">
                    <div class="pull-left label-txt">
                        <label><strong>Активно:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo $row->active == 1 ? 'Да' : 'Нет'; ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Категория:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo get_the_title($row->type_id); ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Дата размещения:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo convert_date($row->created); ?> <?php echo convert_time_only($row->time); ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Дата окончания:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo convert_date($row->expiration); ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Краткое описание:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo htmlspecialchars($row->title); ?> 
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Количество:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo $row->copies; ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Начальная стоимость:</strong></label>
                    </div>
                    <div class="pull-left" id="bet_label">
                        <?php echo $row->price." ".$GLOBALS['tzs_pr_curr'][$row->currency]; ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Форма оплаты:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo $GLOBALS['tzs_pr_payment'][$row->payment]; ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left label-txt">
                        <label><strong>Место нахождение:</strong></label>
                    </div>
                    <div class="pull-left">
                        <?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from); ?>
                    </div>
                    <div class="clearfix"></div>
                    
                </div>
            </div>
        </div>
        <div class="span2" id="left-control">
            
                            
        </div>
    </div>
   
    <div class="row-fluid">
        <div class="span11">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#description" data-toggle="tab">Описание</a></li>
                <?php
                    if ($user_id == $row->user_id) {?>
                        <li><a href="#dopolnenie" data-toggle="tab">История ставок</a></li>
                <?php } ?>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="description">
                    <?php echo $row->description; ?>
                </div>
                <div class="tab-pane fade" id="dopolnenie">
                    <div class="well well-large">
                        <div id="wrapper_bet">
                        <?php
                        $active=" AND active=1";
                        // Отбор ставок по тендеру - active=1 AND 
                        $sql = "SELECT MAX(rate) FROM ".TZS_PRODUCT_RATES_TABLE." WHERE product_id=$sh_id $active";
                        /*$max = $wpdb->get_results($sql); */
                        $max=$wpdb->get_var($sql);
                        if ($max > 0) echo "<input id='act_rate' style='display: none;' value=".round($max,2)." />";
                        else echo "<input id='act_rate' style='display: none;' value=".$row->price." ".$GLOBALS['tzs_pr_curr'][$row->currency]." />";
                        $sql = "SELECT COUNT(*) FROM ".TZS_PRODUCT_RATES_TABLE." WHERE product_id=$sh_id $active";
                        /*$max = $wpdb->get_results($sql); */
                        $cnt_rate=$wpdb->get_var($sql);
                        if ($cnt_rate > 0) echo "<input id='cnt_rate' style='display: none;' value=".$cnt_rate." />";
                        else echo "<input id='cnt_rate' style='display: none;' value='0' />";
                        $sql = "SELECT * FROM ".TZS_PRODUCT_RATES_TABLE." WHERE product_id=$sh_id ORDER BY active DESC,created DESC;";
                        $res = $wpdb->get_results($sql);
                        if (count($res) == 0 && $wpdb->last_error != null) {
                            print_r($wpdb->last_error );
                            print_error('Не удалось отобразить информацию о ставках. Свяжитесь, пожалуйста, с администрацией сайта.');
                        } else if (count($res) == 0) {
                            print_error('Ставки не найдены');
                        } else {
                            ?>
                            <table border="0" id="tbl_products" style="float: none !important;">
                                <tr>
                                        <th>Статус</th>
                                        <th id="tbl_products_dtc">Размещена <br /> Отозвана</th>
                                        <!-- <th id="tbl_products_dtc">Дата и время отзыва</th> -->
                                        <th id="price">Предложенная стоимость</th>
                                        <th id="price">Комментарий</th>
                                        <!-- <th id="price">Автор</th> -->
                                        <?php if (($user_id !== 0) || ($GLOBALS['tzs_au_contact_view_all'] !== false)) {?>
                                        <th id="price">Автор <br /> Контактные данные</th>
                                        <?php } ?>
                                </tr>
                                <?php
                                $i=0;
                                foreach ( $res as $row ) {
                                    $user_info = get_userdata($row->user_id);
                                    if ($row->reviewed == null) $reviewed="&nbsp"; else{$reviewed=convert_time($row->reviewed);}
                                    ?>
                                    <tr id="<?php echo $row->active == 1 ? 'tbl_auction_rate_active' : 'tbl_auction_rate_reviewed'; ?>">
                                        <td><?php echo $row->active == 1 ? 'Активна' : 'Отозвана'; ?></td>
                                        <td><?php echo ' '.convert_time($row->created)."<br />".$reviewed; ?></td>
                                        <!-- <td><?php /*echo $row->reviewed == null ? '&nbsp;' : convert_time($row->reviewed); */?></td> -->
                                        <td><?php echo $row->rate." ".$GLOBALS['tzs_pr_curr'][$row->currency]; ?></td>
                                        <td><?php echo $row->description ?></td>
                                        <!-- <td><?php /* $meta = get_user_meta($row->user_id, 'fio'); echo $meta[0]; */?></td> -->
                                        <?php if (($user_id !== 0) || ($GLOBALS['tzs_au_contact_view_all'] !== false)) {?>
                                        <td>
                                            <?php $meta = get_user_meta($row->user_id, 'fio'); echo $meta[0].'<br />'; ?>
                                            <?php $meta = get_user_meta($row->user_id, 'telephone'); echo $meta[0] == null ? '' : 'Номера телефонов: '.htmlspecialchars($meta[0]).'<br/>'; ?>
                                            <?php echo $user_info->user_email == null ? '' : 'E-mail: '.htmlspecialchars($user_info->user_email).'<br/>'; ?>
                                            <?php $meta = get_user_meta($row->user_id, 'skype'); echo $meta[0] == null ? '' : 'Skype: '.htmlspecialchars($meta[0]).'<br/>'; ?>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                
                                <?php } ?>
                            </table>
                        <?php } ?>
                        </div>
                
                    </div>  
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    jQuery('#myTab a').click(function (e) {
    e.preventDefault();
    jQuery(this).tab('show');
})
</script>
<script>
jQuery(document).ready(function() { 
    if (document.getElementById('tek_price')){
        document.getElementById('tek_price').innerHTML=document.getElementById('act_rate_user').value+' '+document.getElementById('currency').value;    
    }
    if(document.getElementById('tek_stavki')){
       document.getElementById('tek_stavki').innerHTML=document.getElementById('cnt_rate').value; 
    }
    
    
    now = new Date();
    month=now.getUTCMonth()+1;
    now_str=now.getFullYear()+'-'+month+'-'+now.getUTCDate()+' '+now.getHours()+':'+now.getMinutes()+':'+now.getSeconds();
    var title=document.getElementsByClassName('entry-title');
    var company=document.getElementById('title-company').value;
    title[0].innerHTML=company;
    
});

function clickSmallImage(element)
{
    document.getElementById("general").src=element.src;
}

function bet_click()
{
flag_subs=0;
document.getElementById('bet_error').innerHTML="";
paramstr=document.getElementById('text_bet').id+"=" + encodeURIComponent(document.getElementById('text_bet').value) + "&"+document.getElementById('user_id').id+"="+document.getElementById('user_id').value+"&"+document.getElementById('user_name').id+"="+document.getElementById('user_name').value+"&"+document.getElementById('auction_id').id+"="+document.getElementById('auction_id').value+"&"+document.getElementById('bet_user').id+"=" + document.getElementById('bet_user').value + "&"+document.getElementById('currency').id+"=" + document.getElementById('currency').value + "&"+document.getElementById('created').id+"=" + now_str + "&";

if  ((document.getElementById('bet_user').value != "") )
{
    flag_subs=flag_subs+1;
}
else
{

  document.getElementById('bet_error').innerHTML="Ставка не может быть пустой!";
  return false;  
}

if(flag_subs>=1)
{
jQuery.ajax({
		url: "/wp-admin/admin-ajax.php?action=add_bet",
       // url: "/wp-content/plugins/tzs/functions/tzs.functions.php?action=add_bet",
		type: "POST",
		data: paramstr,
		success: function(data){
			//document.forms["bet_form"].submit();
            bet_rate=document.getElementById('bet_user').value;
            //document.getElementById('tek_price').innerHTML=bet_rate+' '+document.getElementById('currency').value+'.';
            document.getElementById('bet_error').innerHTML="Ваша ставка принята!";
            document.getElementById('bet_user').value="";
            document.getElementById('text_bet').value="";
            //jQuery("#wrapper_bet").load("/wp-content/plugins/tzs/front-end/tzs.view.auctions#wrapper_bet");
            document.getElementById('wrapper_bet').innerHTML=data;
            //alert(data);

		},
        error: function(data){
			//document.forms["bet_form"].submit();
            
            alert(data);

		}			
	});		   
}
}

</script>


			<br/>
                        
			
                                
			<script>
				function promptDelete(id, active) {
                                    if (active === 1) {
                                        var s_text = '<div><h2>Удалить запись '+id+' или перенести в архив ?</h2><hr/><p>Запись из архива можно в любой момент снова опубликовать.</p><p>При удалении записи будут так же удалены все прикрепленные изображения.</p></div>';
                                        buttons1 = new Object({
                                                                'В архив': function () {
                                                                        jQuery(this).dialog("close");
                                                                        doDelete(id, 0);
                                                                },
                                                                'Удалить': function () {
                                                                        jQuery(this).dialog("close");
                                                                        doDelete(id, 1);
                                                                },
                                                                'Отменить': function () {
                                                                        jQuery(this).dialog("close");
                                                                }
                                                            });
                                    } else {
                                        var s_text = '<div><h2>Удалить запись '+id+' из архива ?</h2><hr/><p>Запись из архива можно в любой момент снова опубликовать.</p><p>При удалении записи будут так же удалены все прикрепленные изображения.</p></div>';
                                        buttons1 = new Object({
                                                                'Удалить': function () {
                                                                        jQuery(this).dialog("close");
                                                                        doDelete(id, 1);
                                                                },
                                                                'Отменить': function () {
                                                                        jQuery(this).dialog("close");
                                                                }
                                                            });
                                    }

					jQuery('<div></div>').appendTo('body')
						.html(s_text)
						.dialog({
							modal: true,
							title: 'Удаление',
							zIndex: 10000,
							autoOpen: true,
							width: 'auto',
							resizable: false,
							buttons: buttons1,
							close: function (event, ui) {
								jQuery(this).remove();
							}
						});
				}
				function doDelete(id, is_delete) {
                                    p_data = "id=" + id + "&is_delete=" + is_delete;
                                    jQuery.ajax({
                                        url: "/wp-admin/admin-ajax.php?action=tzs_delete_auction",
                                        type: "POST",
                                        data: p_data,
                                        success: function(response){
                                            //window.open('/account/my-auctions/', '_self');
                                            window.open('/account/my-auctions/', '');
                                        },
                                        error: function(response){
                                            alert('Не удалось удалить: '+response);
                                        }
                                    });		   
					/*var data = {
						'action': 'tzs_delete_auction',
						'id': id,
                                                'is_delete': is_delete
					};
					
					jQuery.post(ajax_url, data, function(response) {
						if (response == '1') {
							window.open('/account/my-auctions/', '_self');
						} else {
							alert('Не удалось удалить: '+response);
						}
					});*/
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