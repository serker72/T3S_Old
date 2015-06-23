<?php
function tzs_front_end_view_productsd_handler($atts) {
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
                    if(isset($_GET['spis'])) echo "<a id='edit_search' href='/account/my-products/'>Назад к списку</a>";
                    else echo "<button id='edit_search'  onclick='history.back()'>Назад к списку</button>";  ?>                  
                    
                     <div style='clear: both'>
            <br />
            <div class="container-fluid">
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
        <div class="span7">
            <div class="well well-large">
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
                        <label><strong>Cтоимость:</strong></label>
                    </div>
                    <div class="pull-left">
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
                    <?php if ($user_id == $row->user_id) /*if ($user_id != $row->user_id)*/{ ?>
                        <center>
                            <button id="view_del" onClick="javascript: promptDelete(<?php echo $row->id.', '.$row->active;?>);">Удалить</button>
			         	    <button id="view_edit" onClick="javascript: window.open('/account/edit-product/?id=<?php echo $row->id;?>', '_self');">Изменить</button>
                        </center>
                    <?php } ?>    
                </div>
            </div>
        </div>
    </div>
   
    <div class="row-fluid">
        <div class="span11">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#description" data-toggle="tab">Описание</a></li>
                <li><a href="#contact" data-toggle="tab">Контактная информация</a></li>
                <li><a href="#allgoods" data-toggle="tab">Все товары пользователя</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="description">
                    <?php echo $row->description; ?>
                </div>
                <div class="tab-pane fade" id="contact">
                    <?php  if (($user_id == 0) && ($GLOBALS['tzs_au_contact_view_all'] == false)) {?>
				        <div>Для просмотра контактов необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></div>
                    <?php } else if ($user_id != $row->user_id) {?>
			         <br/>
			         <div class="span6"> 
                        <div id="labeltxt">
                            <?php echo tzs_print_user_table_ed($row->user_id); ?>   
                        </div>
                    </div> 
			         <script src="/wp-content/plugins/tzs/assets/js/feedback.js"></script>
        			<button id="view_feedback" onClick="<?php echo tzs_feedback_build_url($row->user_id);?>">Отзывы <span>|</span> Рейтинг пользователя</button>
			         <?php } ?>  
                </div>
                <div class="tab-pane fade" id="allgoods">
                    <?php /*echo do_shortcode('[tzs-view-user-products user_id=1]');*/echo do_shortcode('[tzs-view-user-products user_id='.$row->user_id.']');?>
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
function clickSmallImage(element)
{
    document.getElementById("general").src=element.src;
}
</script>
            
     
			
			
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
					var data = {
						'action': 'tzs_delete_product',
						'id': id,
                                                'is_delete': is_delete
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