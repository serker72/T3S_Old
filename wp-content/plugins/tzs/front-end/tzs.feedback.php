<?php

add_action( 'wp_ajax_tzs_add_feedback', 'tzs_add_feedback_callback' );
add_action( 'wp_ajax_tzs_del_feedback', 'tzs_del_feedback_callback' );

function tzs_add_feedback_callback() {
	$user_id = get_current_user_id();
	$id = isset($_POST['id']) && is_valid_num($_POST['id']) ? intval($_POST['id']) : 0;
	$tp = isset($_POST['type']) && is_valid_num_zero($_POST['type']) ? intval($_POST['type']) : 1;
	if ($tp > 2)
		$tp = 2;
	$cont = isset($_POST['cont']) ? trim($_POST['cont']) : "";
	
	global $wpdb;
	
	if ($user_id == 0 || $id == 0) {
		echo 'Пользователь не найден';
	} else if (strlen($cont) < TZS_FEEDBACK_MIN_LEN) {
		echo 'Слишком короткий отзыв';
	} else {
		$cont = $tp.$cont;
		
		$u_comment = $wpdb->get_row($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND user_id = %d", $id, $user_id));
		
		if (count($u_comment) > 0) {
			$commentdata = array(
				'comment_ID' => $u_comment->comment_ID,
				'comment_date' => current_time('mysql'),
				'comment_content' => $cont
			);
			wp_update_comment($commentdata);
			echo 1;
		} else {
			$commentdata = array(
				'comment_post_ID' => $id,
				'comment_content' => $cont,
				'comment_type' => '',
				'user_id' => $user_id,
			);
			$comment_id = wp_new_comment($commentdata);
			echo 1;
		}
	}
	die();
}

function tzs_del_feedback_callback() {
	$user_id = get_current_user_id();
	$id = isset($_POST['id']) && is_valid_num($_POST['id']) ? intval($_POST['id']) : 0;
	
	global $wpdb;
	
	if ($user_id == 0 || $id == 0) {
		echo 'Пользователь не найден';
	} else {
		$res = $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->comments WHERE comment_post_ID = %d AND user_id = %d LIMIT 1", $id, $user_id));
		if ($res === false) {
			echo "Не удалось удалить запись с базе данных";
		} else {
			echo 1;
		}
	}
	die();
}

function tzs_feedback_build_url($id) {
	return "javascript:feedback_id=$id; feedback_page=1; doFeedbackDialog();";
}

function tzs_front_end_feedback_handler($atts) {
	ob_start();
	tzs_copy_get_to_post();
	
	$pp = TZS_FEEDBACKS_PER_PAGE;
	
	$id = isset($_POST['id']) && is_valid_num($_POST['id']) ? intval($_POST['id']) : 0;
	$page = isset($_POST['pg']) && is_valid_num($_POST['pg']) ? intval($_POST['pg']) : 1;
	$user_id = get_current_user_id();
	
	$user_info = $id > 0 ? get_userdata($id) : null;
	
	if ($user_info == null) {
		print_error('Пользователь не найден');
	} else {
		global $wpdb;
		
		$res_neg = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as cnt FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) ) AND comment_content like %s", $id, $user_id, '0%'));
		$res_pos = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as cnt FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) ) AND comment_content like %s", $id, $user_id, '2%'));
		
		$res_cnt = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as cnt FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )", $id, $user_id));
		$records = $res_cnt->cnt;
		$pages = ceil($records / $pp);
		if ($pages == 0)
			$pages = 1;
		if ($page > $pages)
			$page = $pages;
		$from = ($page-1) * $pp;
		
		$rate = 0 - $res_neg->cnt + $res_pos->cnt;
		
		$rate_class = "feedback_neutral";
		if ($rate < 0)
			$rate_class = "feedback_negative";
		else if ($rate > 0)
			$rate_class = "feedback_positive";
		
		$u_comment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND user_id = %d", $id, $user_id));
		
		$u_exists = count($u_comment) > 0;
		$u_text = $u_exists ? substr($u_comment->comment_content, 1) : "";
		$u_type = $u_exists ? substr($u_comment->comment_content, 0, 1) : "1";
		
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt DESC LIMIT %d,%d", $id, $user_id, $from, $pp));
	?>
	<div id="feedback_title">Компания <?php $meta = get_user_meta($id, 'company'); echo $meta[0]; ?>, контактное лицо &quot;<?php $meta = get_user_meta($user_id, 'fio'); echo $meta[0]; //echo htmlspecialchars($user_info->display_name); ?>&quot;</div>
	
	<div id="feedback_rating">
		Рейтинг: <span class="<?php echo $rate_class;?>"><?php echo $rate;?></span>
	</div>
	
	<?php
	if (count($comments) == 0) {
		?>
			<div id="info">У этого пользователя еще нет отзывов.</div>
		<?php
	} else {
	?>
	<div id="feedback_content">
		<table>
			<tr>
				<th id="adds">Дата</th>
				<th id="type">Тип отзыва</th>
				<th id="auth">Автор</th>
				<th id="feedback">Отзыв</th>
			</tr>
			<?php
			foreach ( $comments as $row ) {
				$cont = $row->comment_content;
				$type = substr($cont, 0, 1);
				$user_id = $row->user_id;
				$user_info = get_userdata($user_id);
				if (is_valid_num_zero($type)) {
					$cont = substr($cont, 1);
					if ($type == '0')
						$type = "<span id=\"negative\">Отрицательный<span>";
					else if ($type == '1')
						$type = "<span id=\"neutral\">Нейтральный<span>";
					else
						$type = "<span id=\"positive\">Положительный<span>";
				} else {
					$type = "<span id=\"neutral\">Нейтральный<span>";
				}
				?>
				<tr>
					<td><b><?php echo convert_date_no_year($row->comment_date); ?></b><br/><?php echo convert_time_only($row->comment_date);?></td>
					<td><?php echo $type;?></td>
					<td><?php echo $user_info->display_name;?></td>
					<td><?php echo htmlspecialchars($cont);?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
		build_pages_footer($page, $pages, "fpage");
		?>
	</div>
	<?php
	}
	?>
	
	<div id="feedback_form">
		<input type="hidden" name="user_id" value="<?php echo $id;?>"></input>
		<select name="feedback_type">
			<option value="0" <?php if ($u_type == "0") echo "selected";?>>Отрицательный</option>
			<option value="1" <?php if ($u_type == "1") echo "selected";?>>Нейтральный</option>
			<option value="2" <?php if ($u_type == "2") echo "selected";?>>Положительный</option>
		</select>
		<textarea name="feedback_text"><?php echo $u_text;?></textarea>
		<?php if ($u_exists) {?>
			<button onClick="javascript:sendFeedback();">Изменить</button>
			<button onClick="javascript:delFeedback();">Удалить</button>
		<?php } else {?>
			<button onClick="javascript:sendFeedback();">Добавить</button>
		<?php }?>
	</div>
	
	<!--<button onClick="javascript:doReloadFeedback();">Обновить</button>-->
	
	<script>
		function sendFeedback() {
			var tp = jQuery("select[name=feedback_type] option:selected").attr('value');
			var cont = jQuery("textarea[name=feedback_text]").val();
			var id = jQuery("input[name=user_id]").val();
			
			var data = {
				'action': 'tzs_add_feedback',
				'cont': cont,
				'type': tp,
				'id': id
			};
			
			jQuery.post(ajax_url, data, function(response) {
				if (response != '1') {
					alert("Ошибка: "+response);
				} else {
					doReloadFeedback();
				}
			}).fail(function() {
				alert("Не удалось добавить/изменить отзыв. Попробуйте, пожалуйста, еще раз.");
			});
		}
		
		function delFeedback() {
			var id = jQuery("input[name=user_id]").val();
			
			var data = {
				'action': 'tzs_del_feedback',
				'id': id
			};
			
			jQuery.post(ajax_url, data, function(response) {
				if (response != '1') {
					alert("Ошибка: "+response);
				} else {
					doReloadFeedback();
				}
			}).fail(function() {
				alert("Не удалось удалить отзыв. Попробуйте, пожалуйста, еще раз.");
			});
		}
		
		function hijackFeedbackLinks() {
			jQuery('a[tag=fpage]').each(function() {
				jQuery(this).click(function(){
					feedback_page = parseInt(jQuery(this).attr('page'));
					doReloadFeedback();
					return false;
				});
			});
		}
		
		jQuery(document).ready(function() {
			hijackFeedbackLinks();
		});
	</script>
	
	<?php
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>