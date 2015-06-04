<?php

include_once(TZS_PLUGIN_DIR.'/functions/tzs.product.functions.php');

// Эти файлы должны быть подключены в лицевой части (фронт-энде).
require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );

function tzs_print_edit_image_form($errors) {
    $php_max_file_uploads = (int)ini_get('max_file_uploads');
    if ($php_max_file_uploads > TZS_PR_MAX_IMAGES) {
        $php_max_file_uploads = TZS_PR_MAX_IMAGES;
    }
    
    if (isset($_POST['image_id_lists']) && $_POST['image_id_lists'] !== '') {
        $img_names = explode(';', $_POST['image_id_lists']);
    } else {
        $img_names = array();
    }
    
    $form_type = get_param('form_type');
    
    $form_type_info = array(
        'product' => array ('product', TZS_PRODUCTS_TABLE, 'Товар/услуга', 'товаре/услуге', 'товар/услугу', 'товара/услуги'),
        'auction' => array ('auction', TZS_AUCTIONS_TABLE, 'Тендер', 'тендере', 'тендер', 'тендера'),
    );
        
    echo '<div style="clear: both;"></div>';
    print_errors($errors);
    ?>
    <div style="clear: both;"></div>
    <div id="images_edit" style="width: 100%;">
    
    <form enctype="multipart/form-data" method="post" id="fpost" class="pr_edit_form post-form" action="">
        <div id="">
            <!--h3>Изменение прикрепленных изображений</h3>
            <hr/-->
            <p>Наименование <?php echo $_POST['form_type'] == 'product' ? 'товара/услуги' : 'тендера'; ?>: <strong>"<?php echo $_POST['title']; ?>"</strong> (Id=<?php echo $_POST['id']; ?>)</p>
            <p>Допустимое кол-во изображений: <strong><?php echo $php_max_file_uploads;?></strong></p>
            <p>Допустимый размер одного изображения: <strong>2 Мб</strong></p>
        </div>
        <div id="">
            <table id="tbl_products" border='0'>
                <tr>
                    <th id="wight">Номер изображения</th>
                    <th>Прикрепленное изображение</th>
                    <th id="wight">Удалить изображение</th>
                    <th>Новое изображение</th>
                    <th>Главное изображение</th>
                </tr>
                <?php for ($i=0;$i < $php_max_file_uploads;$i++) { ?>
                    <tr>
                        <td><?php echo ($i+1); ?></td>
                    <?php if (count($img_names) > 0 && $img_names[$i] !== null && $img_names[$i] !== '') {
                        $main_image_disabled = '';
                        $img_info = wp_get_attachment_image_src($img_names[$i], 'thumbnail'); ?>
                        <td><img src="<?php echo $img_info[0]; ?>" name="image_<?php echo $i; ?>" alt="Изображение №<?php echo ($i+1); ?>"></td>
                        <td><input type="checkbox" id="" name="del_image_<?php echo $i; ?>" <?php if (isset($_POST["del_image_$i"])) echo 'checked="checked"'; ?>></td>
                        <td><input type="file" id="chg_image" name="chg_image_<?php echo $i; ?>" multiple="false" accept="image/*"></td>
                    <?php } else { 
                        $main_image_disabled = 'disabled="disabled"'; ?>
                        <td><input type="file" id="add_image" name="add_image_<?php echo $i; ?>" multiple="false" accept="image/*"></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    <?php } ?>
                        <td>
                            <input type="radio" <?php echo $main_image_disabled; ?> tag="main_image_'.$i.'" id="main_image" name="main_image" value="<?php echo $i; ?>" <?php if (isset($_POST['main_image']) && $_POST['main_image'] == "$i") echo 'checked="checked"'; ?>>
                            <?php if ($i === 0) wp_nonce_field( 'image_0', 'image_0_nonce' ); ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
            <input type="hidden" name="action" value="editimages"/>
            <input type="hidden" name="id" value="<?php echo_val('id'); ?>"/>
            <input type="hidden" name="image_id_lists" value="<?php echo_val('image_id_lists'); ?>"/>
            <?php if (isset($_POST['main_image'])) { ?>
                <input type="hidden" name="old_main_image" value="<?php echo_val('main_image'); ?>"/>
            <?php } ?>
            <input type="hidden" name="formName" value="<?php echo_val('form_type'); ?>images" />
        <table>
            <tr>
                <td width="130px">&nbsp;</td>
                <td>
                    <input name="addpost" type="submit" id="addpostsub" class="submit_button" value="Загрузить/обновить изображения"/>
                </td>
                <td width="15px">&nbsp;</td>
                <td>
                    <a href="/view-<?php echo $form_type_info[$form_type][0]; ?>/?id=<?php echo_val('id'); ?>&spis=new" id="edit_images">Просмотреть <?php echo $form_type_info[$form_type][4]; ?></a>
                </td>
            </tr>
        </table>
    </form>
    </div>
    
	
    <script>
        function onMainImageRadioStatus(e) {
            fname = e.target.name;
            fname_ar = fname.split('_');
            i = fname_ar[2];
            s3 = 'input:radio[name=main_image]:nth(' + i + ')';
            if (e.target.files.length > 0) {
                jQuery(s3).removeAttr('disabled');
            } else {
                jQuery(s3).attr('disabled', 'disabled');
            }
        }

        jQuery(document).ready(function(){
            jQuery('input[type=file]').change(function(e) {
                onMainImageRadioStatus(e);
            });
            
            jQuery('#fpost').submit(function() {
                jQuery('#addpostsub').attr('disabled','disabled');
                jQuery('#images_edit').after('<div id="fpost_status"><h4>Идет загрузка файлов, подождите...</h4></div>');
                return true;
            });
        });
    </script>
    <?php
}

function tzs_edit_pr_images() {
    $errors = array();
    $error_message = array(
        0 => 'Ошибок не возникло, файл был успешно загружен на сервер',
        1 => 'Размер принятого файла превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini',
        2 => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме',
        3 => 'Загружаемый файл был получен только частично',
        4 => 'Файл не был загружен',
        5 => '',
        6 => 'Отсутствует временная папка',
        7 => 'Не удалось записать файл на диск',
        8 => 'PHP-расширение остановило загрузку файла',
    );
    
    $php_max_file_uploads = (int)ini_get('max_file_uploads');
    if ($php_max_file_uploads > TZS_PR_MAX_IMAGES) {
        $php_max_file_uploads = TZS_PR_MAX_IMAGES;
    }
    
    $user_id = get_current_user_id();
    
    // Проверим защиту nonce
    if (isset($_POST['image_0_nonce']) && wp_verify_nonce($_POST['image_0_nonce'], 'image_0')) {
        $title = get_param('title');
        $image_id_lists = get_param('image_id_lists');
        $main_image = get_param_def('main_image', '0');
        $old_main_image = get_param_def('old_main_image', '0');
        $id = get_param_def('id', '0');
        $form_type = get_param('form_type');
    
        $form_type_info = array(
            'product' => array ('product', TZS_PRODUCTS_TABLE, 'Товар/услуга', 'товар/услугу', 'товар/услуга', 'товара/услуги'),
            'auction' => array ('auction', TZS_AUCTIONS_TABLE, 'Тендер', 'тендер', 'тендер', 'тендера'),
        );
        
        if ($image_id_lists !== null && $image_id_lists !== '') {
            $img_names = explode(';', $image_id_lists);
        } else {
            $img_names = array();
        }
        
        $pr_pictures = '';
        $img_names_new = array();
        $img_count = 0;
        
        // Необходимо реализовать проверку на замещение изображений и удаление старых на сервере
        
        for ($i=0;$i < $php_max_file_uploads;$i++) {
            $add_image_index = 'add_image_'.$i;
            $chg_image_index = 'chg_image_'.$i;
            $del_image_index = 'del_image_'.$i;
            
            // Удаление изображения
            if ((count($errors) === 0) && isset($_POST[$del_image_index])) {
                if( false === wp_delete_attachment($img_names[$i], true) ) {
                    array_push($errors, "Не удалось удалить файл с изображением: ".$img_names[$i]->get_error_message());
                } else {
                    $img_names_new[$i] = null;
                    $img_count++;
                }
            } else if ((count($errors) == 0) && ((strlen($_FILES[$add_image_index]['name']) > 0) || (strlen($_FILES[$chg_image_index]['name']) > 0) )) {
                if ($_FILES[$add_image_index]['error']) {
                    array_push($errors, "Не удалось загрузить файл с изображением: ".$error_message[$_FILES[$add_image_index]['error']]);
                } else if ($_FILES[$chg_image_index]['error']) {
                    array_push($errors, "Не удалось загрузить файл с изображением: ".$_FILES[$chg_image_index]['error']);
                } else {
                    // Позволим WordPress перехватить загрузку.
                    // не забываем указать атрибут name поля input
                    if ($_FILES[$add_image_index]) {
                        $attachment_id = media_handle_upload($add_image_index, 0);
                    } else {
                        $attachment_id = media_handle_upload($chg_image_index, 0);
                    }

                    if ( is_wp_error($attachment_id) ) {
                        array_push($errors, "Не удалось загрузить файл с изображением: ".$attachment_id->get_error_message());
                    } else {
                        //$attachment_info = wp_get_attachment_image_src($attachment_id, 'full');
                        //if (strlen($pr_pictures) > 0) $pr_pictures .= ';';
                        //$pr_pictures .= str_replace(get_site_url().'/', '', $attachment_info[0]);
                        $img_names_new[$i] = $attachment_id;
                        $img_count++;
                        
                        // Удаляем старую картинку, если была замена
                        if ($_FILES[$chg_image_index]) {
                            if( false === wp_delete_attachment($img_names[$i], true) ) {
                                array_push($errors, "Не удалось удалить файл с изображением: ".$img_names[$i]->get_error_message());
                            }
                        }
                    }
                }
            }
        }
        
        if ((count($errors) === 0) && (($img_count > 0) || ($main_image !== $old_main_image))) {
            global $wpdb;
            
            if (($main_image !== $old_main_image) && ($img_count === 0)) {
                $main_image_new = $img_names[$main_image];

                $sql = $wpdb->prepare("UPDATE ".$form_type_info[$form_type][1]." SET ".
                        " last_edited=now(), main_image_id=%d".
                        " WHERE id=%d AND user_id=%d;", 
                        intval($main_image_new), $id, $user_id);
            } else {
                $img_names_new = array_replace($img_names, $img_names_new);
                $main_image_new = $img_names_new[$main_image];
                $img_names_new = array_filter($img_names_new);
                if ($main_image_new === null) {
                    $main_image_new = $img_names_new[0];
                }

                $sql = $wpdb->prepare("UPDATE ".$form_type_info[$form_type][1]." SET ".
                        " last_edited=now(), image_id_lists=%s, main_image_id=%d".
                        " WHERE id=%d AND user_id=%d;", 
                        implode(';', $img_names_new), intval($main_image_new), $id, $user_id);
            }

            if (false === $wpdb->query($sql)) {
                array_push($errors, "Не удалось изменить Ваш ".$form_type_info[$form_type][3].". Свяжитесь, пожалуйста, с администрацией сайта");
            }
        }
        
        if (count($errors) > 0) {
            //print_errors($errors);
            tzs_print_edit_image_form($errors);
        } else {
            echo "<div>";
            echo "<h2>Выполнены изменения изображений для Вашего ".$form_type_info[$form_type][5]." !</h2>";
            echo "<br/>";
            echo '<a href="/view-'.$form_type_info[$form_type][0].'/?id='.$id.'&spis=new">Просмотреть '.$form_type_info[$form_type][3].'</a>';
            echo "</div>";
        }
    } else {
        print_error("Проверка формы не пройдена. Свяжитесь, пожалуйста, с администрацией сайта.");
    }
}

function tzs_front_end_pr_images_handler($atts) {
    ob_start();
    
    tzs_copy_get_to_post();
	
    $user_id = get_current_user_id();
    $pr_id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval($_POST['id']) : 0;
    $form_type = get_param_def('form_type', '');
    
    $form_type_info = array(
        'product' => array ('product', TZS_PRODUCTS_TABLE, 'Товар/услуга', 'товаре/услуге'),
        'auction' => array ('auction', TZS_AUCTIONS_TABLE, 'Тендер', 'тендере'),
    );
	
    if ( !is_user_logged_in() ) {
        print_error("Вход в систему обязателен");
    } else if (($form_type !== 'product') && ($form_type !== 'auction')) {
        print_error('Не указан параметр "form_type"'); 
    } else if ($pr_id <= 0) {
        print_error($form_type_info[$form_type][2].' не найден');
    } else if ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST['action'] ) && $_POST['action'] == 'editimages' && ($_POST['formName'] == $form_type.'images')) {
        // Проверим защиту nonce
        if (isset($_POST['image_0_nonce']) && wp_verify_nonce($_POST['image_0_nonce'], 'image_0')) {
            tzs_edit_pr_images();
        } else {
            print_error("Проверка формы не пройдена. Свяжитесь, пожалуйста, с администрацией сайта.");
        }
    } else {
        global $wpdb;
        $sql = "SELECT * FROM ".$form_type_info[$form_type][1]." WHERE id=$pr_id AND user_id=$user_id;";
        $row = $wpdb->get_row($sql);
        if (count($row) == 0 && $wpdb->last_error != null) {
            print_error('Не удалось отобразить информацию о '.$form_type_info[$form_type][3].'. Свяжитесь, пожалуйста, с администрацией сайта');
        } else if ($row == null) {
            print_error($form_type_info[$form_type][2].' не найден');
        } else {
            $_POST['title'] = $row->title;
            $_POST['image_id_lists'] = $row->image_id_lists;
            $_POST['main_image'] = array_search($row->main_image_id, explode(';', $row->image_id_lists));
            $_POST['id'] = ''.$row->id;
            $_POST['form_type'] = $form_type_info[$form_type][0];
            
            tzs_print_edit_image_form(null);
        }
    }

    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}

?>