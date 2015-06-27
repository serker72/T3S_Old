<?php
function tzs_front_end_view_firms_handler($atts) {
	ob_start();
	
	global $wpdb;
	
	$user_id = get_current_user_id();
	
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
    //$sh_id=7;
	
	if ($sh_id <= 0) {
		print_error('Информация не найден');
	} else {
		$sql = "SELECT * FROM wp_usermeta WHERE user_id=$sh_id";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить информацию о товаре/услуге. Свяжитесь, пожалуйста, с администрацией сайта.');
            
		} else if ($row == null) {
			print_error('Пользователь не найден');
		} else {
                    if(isset($_GET['spis'])) echo "<a id='edit_search' href='/account/my-products/'>Назад к списку</a>";
                    else echo "<button id='edit_search'  onclick='history.back()'>Назад к списку</button>";  ?>                  
                    <script src="/wp-content/plugins/tzs/assets/js/table_reload.js"></script>
                     <div style='clear: both'>
            <br />
            <div class="container-fluid">
                <div class="row-fluid" >
                    <div class="span4" id="">
                        <div class="well well-large">
                        <?php $logo = get_user_meta($row->user_id, 'company_logo',true) ?>
                            <img src="<?php echo $logo; ?>"/>
                        </div>
                    </div>
                    <div class="span8">
                        <div class="well well-large" id="company-contact">
                            <?php
                                //echo tzs_print_user_table_ed($row->user_id);
                                 $form_type = get_param_def('form_type', '');
                                 echo tzs_print_user_contacts($row, $form_type, 2);
                                 
                            ?>
                            
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12" id="">
                        <div class="well well-large">
                            <?php $desc=get_user_meta($row->user_id, 'company_description',true); echo $desc;?>
                        </div>
                </div>
                </div>
                <div class="row-fluid">
                    <div class="span12" id="">
                        <?php /*echo do_shortcode('[tzs-view-user-products user_id=1]');*/echo do_shortcode('[tzs-view-user-products user_id='.$row->user_id.']');?>
                    </div>
                </div>
            </div>

<script>
    jQuery('document').ready(function(){
        var title=document.getElementsByClassName('entry-title');
        if (document.getElementById('company-name')) {
            var company=document.getElementById('company-name').innerHTML;
            title[0].innerHTML=company;    
        }
        
        
    });
</script>
            
     
			
			
			
<?php
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}
?>