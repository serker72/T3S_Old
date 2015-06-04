<?php

include_once(TZS_PLUGIN_DIR.'/functions/tzs.auction.functions.php');
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.trade.images.php');

        
function tzs_print_auction_form($errors, $edit=false) {
    //$d = date("d.m.Y");
    // Добавим 7 дней к текущей дате
    $dt = new DateTime();
    date_add($dt, date_interval_create_from_date_string('8 days'));
    $d = date_format($dt, "d.m.Y");

    if(isset($_GET['spis'])) echo "<a id='edit_search' href='/account/my-auctions/'>Назад к списку</a> <div style='clear: both'></div>";
    else echo "<button id='edit_search'  onclick='history.back()'>Назад к списку</button> <div style='clear: both'></div>";
    
    echo '<div style="clear: both;"></div>';
    print_errors($errors);
    ?>
    <script src="/wp-content/plugins/tzs/assets/js/autocomplete.js"></script>
    <div style="clear: both;"></div>
    
    
    <form enctype="multipart/form-data" method="post" id="bpost" class="pr_edit_form post-form" action="">

<!-- Новый вид формы, навеяно http://xiper.net/collect/html-and-css-tricks/verstka-form/blochnaya-verstka-form -->
        <div>
            <hr/>
            <!--h3>Добавление тендера</h3-->
            <!--p>Укажите, пожалуйста, категорию, наименование, описание, количество, стоимость, форму оплаты, месторасположение, дату окончания тендера и комментарии</p-->
            <p>Минимальный период действия тендера - <strong><?php echo TZS_AU_EXPIRATION_MIN_DAYS; ?></strong> дней.<br/>При наступлении даты окончания тендер будет автоматически перенесен в архив.</p>
            <hr/>
        </div>
	<?php if ($edit) {?>
        <div class="pr_edit_form_line">
            <label for="pr_id">Номер</label>
            <input type="text" id="" name="pr_id" size="15" value="<?php echo_val('id'); ?>" disabled="disabled">
        </div>
	<?php } ?>
        <div class="pr_edit_form_line">
            <label for="pr_active">Статус</label>
            <select name="pr_active">
                <option value="1" <?php if (isset($_POST["pr_active"]) && ($_POST["pr_active"] === 1)) echo 'selected="selected"'; ?> >Действующий</option>
                <option value="0" <?php if (isset($_POST["pr_active"]) && ($_POST["pr_active"] === 0)) echo 'selected="selected"'; ?> >Архивный</option>
            </select>
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_type_id">Категория</label>
            <select name="pr_type_id">
                <?php tzs_build_product_types('pr_type_id', TZS_AU_ROOT_CATEGORY_PAGE_ID); ?>
            </select>
            <?php wp_nonce_field( 'pr_type_id', 'pr_type_id_nonce' ); ?>
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_is_lot">Тип тендера</label>
            <select name="pr_is_lot">
                <option value="1" <?php if (isset($_POST["pr_is_lot"]) && ($_POST["pr_is_lot"] === 1)) echo 'selected="selected"'; ?> >Продам</option>
                <option value="0" <?php if (isset($_POST["pr_is_lot"]) && ($_POST["pr_is_lot"] === 0)) echo 'selected="selected"'; ?> >Куплю</option>
            </select>
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_title">Наименование</label>
            <input type="text" id="pr_edit_text_big" name="pr_title" size="135" maxlength="255" value="<?php echo_val('pr_title'); ?>">
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_description">Описание</label>
            <?php
                $args = array(  'wpautop' => 1,
                                'media_buttons' => 0,
                                'textarea_name' => 'pr_description', //нужно указывать!
                                'textarea_rows' => 10,
                                'tabindex'      => null,
                                'editor_css'    => '',
                                'editor_class'  => '',
                                'teeny'         => 1,
                                'dfw'           => 0,
                                'tinymce'       => 1,
                                'quicktags'     => array(
                                                    'id' => 'editpost',
                                                    'buttons' => 'strong,em,ul,ol,li,close'
                                                    ),
                                'drag_drop_upload' => false
                            );
                wp_editor($_POST['pr_description'], 'editpost', $args);
            ?>
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_copies">Количество</label>
            <input type="number" id="" name="pr_copies" size="2" value="<?php echo_val('pr_copies'); ?>" min="0">
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_price">Стоимость</label>
            <input type="text" id="" name="pr_price" size="10" value="<?php echo_val('pr_price'); ?>">
            <select for="price" name="pr_currency">
            <?php
                foreach ($GLOBALS['tzs_pr_curr'] as $key => $val) {
                        echo '<option value="'.$key.'" ';
                        if ($val == '')
                                $val = '-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-';
                        if (isset($_POST['pr_currency']) && $_POST['pr_currency'] == $key && $key != 0) {
                                echo 'selected="selected"';
                        }
                        if ($key == 0) {
                                echo 'disabled="disabled"';
                        }
                        //echo '>'.htmlspecialchars($val).'</option>\n';
                        echo '>'.$val.'</option>\n';
                }
            ?>
            </select>
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_payment">Форма оплаты</label>
            <select name="pr_payment">
            <?php
                foreach ($GLOBALS['tzs_pr_payment'] as $key => $val) {
                        echo '<option value="'.$key.'" ';
                        if ($val == '')
                                $val = '-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-';
                        if (isset($_POST['tzs_pr_payment']) && $_POST['tzs_pr_payment'] == $key) { // && $key != 0
                                echo 'selected="selected"';
                        }
                        if ($key == 0) {
                            //echo 'disabled="disabled"';
                        }
                        //echo '>'.htmlspecialchars($val).'</option>\n';
                        echo '>'.$val.'</option>\n';
                }
            ?>
            </select>
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_city_from">Местонахождение</label>
            <input autocomplete="city" id="pr_edit_text_big" type="text" size="135" name="pr_city_from" value="<?php echo_val('pr_city_from'); ?>" autocomplete="off">
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_expiration">Дата окончания</label>
            <input type="text" id="datepicker1" name="pr_expiration" size="" value="<?php echo_val_def('pr_expiration', $d); ?>">
        </div>
        <div class="pr_edit_form_line">
            <label for="pr_comment">Комментарии</label>
            <input type="text" id="pr_edit_text_big" name="pr_comment" size="135" value="<?php echo_val('pr_comment'); ?>">
        </div>

	<?php if ($edit) {?>
		<input type="hidden" name="action" value="editauction"/>
		<input type="hidden" name="id" value="<?php echo_val('id'); ?>"/>
	<?php } else { ?>
		<input type="hidden" name="action" value="addauction"/>
	<?php } ?>
	<input type="hidden" name="formName" value="auction" />
        <table>
            <tr>
                <td width="210px">&nbsp;</td>
                <td>
                    <input name="addpost" type="submit" id="addpostsub" class="submit_button" value="<?php echo $edit ? "Изменить" : "Разместить" ?>"/>
                </td>
                <td width="15px">&nbsp;</td>
                <td>
                    <?php if ($edit) { ?>
                        <a href="/edit-images-pr/?id=<?php echo_val('id'); ?>&form_type=auction" id="edit_images">Изменить изображения</a>
                        <!--button id="edit_images" onClick="javascript: window.open('/edit-images-pr/?id=<?php echo_val('id');?>&form_type=auction', '_self');">Изменить изображения</button-->
                    <?php }?>
                </td>
            </tr>
        </table>
    </form>
	
    <script>
        jQuery(document).ready(function(){
            jQuery('#bpost').submit(function() {
                    jQuery('#addpostsub').attr('disabled','disabled');
                    return true;
            });
            jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
            jQuery( "#datepicker1" ).datepicker({ dateFormat: "dd.mm.yy" });
        });
    </script>
<?php
}

function tzs_edit_auction($id) {
    $errors = array();
    
    $user_id = get_current_user_id();
    
    // Проверим защиту nonce
    if (isset($_POST['pr_type_id_nonce']) && wp_verify_nonce($_POST['pr_type_id_nonce'], 'pr_type_id')) {
	$pr_active = get_param_def('pr_active','0');
	$pr_type_id = get_param_def('pr_type_id','0');
        $pr_is_lot = get_param_def('pr_is_lot', '0');
	$pr_title = get_param('pr_title');
	$pr_description = get_param('pr_description');
	$pr_copies = get_param_def('pr_copies','0');
	$pr_currency = get_param_def('pr_currency','0');
	$pr_price = get_param_def('pr_price','0');
	$pr_payment = get_param_def('pr_payment','0');
	$pr_city_from = get_param('pr_city_from');
	$pr_comment = get_param('pr_comment');
	$pr_expiration = get_param('pr_expiration');
	
        
	if (is_valid_date($pr_expiration) === null) {
            array_push($errors, "Неверный формат даты");
	} else {
            $cur_date = new DateTime();
            $exp_date = new DateTime($pr_expiration);
            $interval = date_diff($cur_date, $exp_date);
            if ($interval->days < TZS_PR_PUBLICATION_MIN_DAYS) {
                array_push($errors, "Минимальный период действия тендера - ".TZS_AU_EXPIRATION_MIN_DAYS." дней");
            }
        }

	$pr_expiration = is_valid_date($pr_expiration);
        
	if (!is_valid_city($pr_city_from)) {
            array_push($errors, "Неверный населенный пункт");
	}
        
	if (strlen($pr_title) < 2) {
            array_push($errors, "Введите наименование товара");
	}
        
	if (strlen($pr_description) < 2) {
            array_push($errors, "Введите описание товара");
	}
        
	if (!is_valid_num_zero($pr_type_id)) {
            array_push($errors, "Неверно задана категория товара");
	}
        
	if (!is_valid_num_zero($pr_active)) {
            array_push($errors, "Неверно задан статус товара");
	}
        
	if (!is_valid_num_zero($pr_is_lot)) {
            array_push($errors, "Неверно задан тип тендера");
	}
        
	if (!is_valid_num_zero($pr_copies)) {
            array_push($errors, "Неверно задано количество экземпляров товара");
	}
        
	if (!is_valid_num_zero($pr_currency)) {
            array_push($errors, "Неверно задана валюта");
	}
        
	if (!is_valid_num_zero($pr_payment)) {
            array_push($errors, "Неверно задана форма оплаты");
	}
        
	if (!is_valid_num_zero($pr_price)) {
            array_push($errors, "Неверно задана стоимость товара");
	}
    }
    else {
        array_push($errors, "Проверка формы не пройдена. Свяжитесь, пожалуйста, с администрацией сайта.");
    }
	
    $from_info = null;
    if (count($errors) == 0) {
            $from_info = tzs_yahoo_convert($pr_city_from);
            if (isset($from_info["error"])) {
                    array_push($errors, "Не удалось распознать населенный пункт: ".$from_info["error"]);
            }
    }

    
    if (count($errors) > 0) {
            tzs_print_auction_form($errors, $id > 0);
    } else {
        global $wpdb;
        
        $pr_expiration = date('Y-m-d', mktime(0, 0, 0, $pr_expiration['month'], $pr_expiration['day'], $pr_expiration['year']));

        if ($id == 0) {
                $sql = $wpdb->prepare("INSERT INTO ".TZS_AUCTIONS_TABLE.
                        " (type_id, user_id, is_lot, title, description, copies, currency, price, payment, city_from, from_cid, from_rid, from_sid, created, comment, last_edited, active, expiration)".
                        " VALUES (%d, %d, %d, %s, %s, %d, %d, %f, %d, %s, %d, %d, %d, now(), %s, NULL, %d, %s);",
                        intval($pr_type_id), $user_id, intval($pr_is_lot), stripslashes_deep($pr_title), stripslashes_deep($pr_description), intval($pr_copies), intval($pr_currency), floatval($pr_price), intval($pr_payment), stripslashes_deep($pr_city_from),
                        $from_info["country_id"],$from_info["region_id"],$from_info["city_id"], stripslashes_deep($pr_comment), intval($pr_active), $pr_expiration);

                if (false === $wpdb->query($sql)) {
                        array_push($errors, "Не удалось опубликовать Ваш товар/услугу. Свяжитесь, пожалуйста, с администрацией сайта");
                        array_push($errors, $wpdb->last_error);
                        tzs_print_auction_form($errors, false);
                } else {
                        echo "<div>";
                        echo "<h2>Ваш товар/услуга опубликован !</h2>";
                        echo "<br/>";
                        //echo '<a href="/view-auction/?id='.tzs_find_latest_auction_rec().'&spis=new">Просмотреть товар/услугу</a>';
                        echo "<h3>Сейчас будет открыта страница для добавления изображений !</h3>";
                        echo "<div>";
                        $new_url = get_site_url().'/edit-images-pr/?id='.tzs_find_latest_auction_rec().'&form_type=auction';
                        echo '<meta http-equiv="refresh" content="0; url='.$new_url.'">';
                }
        } else {
                $sql = $wpdb->prepare("UPDATE ".TZS_AUCTIONS_TABLE." SET ".
                        " last_edited=now(), type_id=%d, is_lot=%d, title=%s, description=%s, copies=%d, currency=%d, price=%d, payment=%f, ".
                        " city_from=%s, from_cid=%d, from_rid=%d, from_sid=%d, comment=%s, active=%d, expiration=%s".
                        " WHERE id=%d AND user_id=%d;", 
                        intval($pr_type_id), intval($pr_is_lot), stripslashes_deep($pr_title), stripslashes_deep($pr_description), intval($pr_copies), intval($pr_currency), floatval($pr_price), intval($pr_payment), 
                        stripslashes_deep($pr_city_from), $from_info["country_id"],$from_info["region_id"],$from_info["city_id"], stripslashes_deep($pr_comment), intval($pr_active), $pr_expiration,
                        $id, $user_id);

                if (false === $wpdb->query($sql)) {
                        array_push($errors, "Не удалось изменить Ваш товар/услугу. Свяжитесь, пожалуйста, с администрацией сайта");
                        array_push($errors, $wpdb->last_error);
                        tzs_print_auction_form($errors, true);
                } else {
                        echo "<div>";
                        echo "<h2>Ваш товар/услуга изменен !</h2>";
                        echo "<br/>";
                        //echo '<a href="/view-auction/?id='.$id.'">Просмотреть товар/услугу</a>';
                        echo "<h3>Сейчас будет открыта страница для добавления изображений !</h3>";
                        echo "<div>";
                        $new_url = get_site_url().'/edit-images-pr/?id='.$id.'&form_type=auction';
                        echo '<meta http-equiv="refresh" content="0; url='.$new_url.'">';
                }
        } 
    }
}

function tzs_front_end_del_auction_handler($attrs) {
    ob_start();

    $errors = array();
    $user_id = get_current_user_id();
    $sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

    if ( !is_user_logged_in() ) {
        print_error("Вход в систему обязателен");
    } else if ($sh_id <= 0) {
        print_error('Товар/услуга не найден');
    } else {
        global $wpdb;
        
        // Вначале попытаемся удалить изображения
        $sql = "SELECT * FROM ".TZS_AUCTIONS_TABLE." WHERE id=$sh_id AND user_id=$user_id;";
        $row = $wpdb->get_row($sql);
        if (count($row) === 0 && $wpdb->last_error != null) {
            array_push($errors, 'Не удалось получить список тендеров. Свяжитесь, пожалуйста, с администрацией сайта');
            array_push($errors, $wpdb->last_error);
            print_errors($errors);
        } else if ($row === null) {
            print_error("Тендер не найден (id=$sh_id AND user_id=$user_id)");
        } else {
            if (strlen($row->image_id_lists) > 0) {
                $img_names = explode(';', $row->image_id_lists);
                for ($i=0;$i < count($img_names);$i++) {
                    if( false === wp_delete_attachment($img_names[$i], true) ) {
                        array_push($errors, "Не удалось удалить файл с изображением: ".$img_names[$i]->get_error_message());
                    }
                }
            }
            
            if (count($errors) > 0) {
                print_errors($errors);
            } else {
                // Удаление записи
                $sql = "DELETE FROM ".TZS_AUCTIONS_TABLE." WHERE id=$sh_id AND user_id=$user_id;";
                if (false === $wpdb->query($sql)) {
                    $errors = array();
                    array_push($errors, "Не удалось удалить Ваш тендер. Свяжитесь, пожалуйста, с администрацией сайта");
                    array_push($errors, $wpdb->last_error);
                    print_errors($errors);
                } else {
                    echo "Тендер удален";
                }
            }
        }
    }

    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}

function tzs_front_end_edit_auction_handler($atts) {
	ob_start();
	
	$user_id = get_current_user_id();
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
	
	if ( !is_user_logged_in() ) {
		print_error("Вход в систему обязателен");
	} else if ($sh_id <= 0) {
		print_error('Тендер не найден');
	} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST['action'] ) && $_POST['action'] == 'editauction' && ($_POST['formName'] == 'auction')) {
		$id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval($_POST['id']) : 0;
		tzs_edit_auction($id);
	} else {
		global $wpdb;
		$sql = "SELECT * FROM ".TZS_AUCTIONS_TABLE." WHERE id=$sh_id AND user_id=$user_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить информацию о тендере. Свяжитесь, пожалуйста, с администрацией сайта');
		} else if ($row == null) {
			print_error('Тендер не найден');
		} else {
                    //" (type_id, title, description, copies, currency, price, payment, city_from, comment, expiration)".
                    $_POST['pr_type_id'] = ''.$row->type_id;
                    $_POST['pr_title'] = $row->title;
                    $_POST['pr_description'] = $row->description;
                    $_POST['pr_copies'] = ''.$row->copies;
                    $_POST['pr_currency'] = ''.$row->currency;
                    if ($row->price > 0)
                            $_POST['pr_price'] = ''.remove_decimal_part($row->price);
                    $_POST['pr_payment'] = ''.$row->payment;
                    $_POST['pr_city_from'] = $row->city_from;
                    $_POST['pr_comment'] = $row->comment;
                    if ($row->expiration !== null)
                        $_POST['pr_expiration'] = date("d.m.Y", strtotime($row->expiration));
                    $_POST['id'] = ''.$row->id;
                    
                    tzs_print_auction_form(null, true);
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

function tzs_front_end_auction_handler($atts) {
    ob_start();
	
    if ( !is_user_logged_in() ) {
            print_error("Вход в систему обязателен");
    } else if ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST['action'] ) && $_POST['action'] == 'addauction' && ($_POST['formName'] == 'auction')) {
            tzs_edit_auction(0);
    } else {
            tzs_print_auction_form(null);
    }

    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}

?>