<?php

include_once(TZS_PLUGIN_DIR.'/functions/tzs.shipment.functions.php');

function tzs_print_shipment_form($errors, $edit=false) {
	$d = date("d.m.Y");
	
	print_errors($errors);
?>

<script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
<script src="/wp-content/plugins/tzs/assets/js/autocomplete.js"></script>
<form enctype="multipart/form-data" method="post" id="bpost" class="post-form" action="">

<div id="cost_div" style="display:none;">
<table>
	<tr>
		<td>
			<input type="radio" name="set_price" value="1" <?php if (isset($_POST['set_price']) && $_POST['set_price'] == '1') echo 'checked="checked"'; ?>><b>Указать стоимость перевозки и форму оплаты</b><br/>
			<input type="text" for="price" name="price" value="<?php echo_val('price'); ?>" size="10">
			<select for="price" name="cost_curr">
			<?php
				foreach ($GLOBALS['tzs_curr'] as $key => $val) {
					echo '<option value="'.$key.'" ';
					if ($val == '')
						$val = '-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-';
					if (isset($_POST['cost_curr']) && $_POST['cost_curr'] == $key && $key != 0) {
						echo 'selected="selected"';
					}
					if ($key == 0) {
						echo 'disabled="disabled"';
					}
					echo '>'.htmlspecialchars($val).'</option>\n';
				}
			?>
			</select><br/>
			<input type="radio" for="price" name="payment" value="nocash" <?php tzs_cost_print_option('payment', 'nocash'); ?>/> <span id="opt_nocash">без нал.</span>
			<input type="radio" for="price" name="payment" value="cash" <?php tzs_cost_print_option('payment', 'cash'); ?>/> <span id="opt_cash">нал.</span>
			<input type="radio" for="price" name="payment" value="mix_cash" <?php tzs_cost_print_option('payment', 'mix_cash'); ?>/> <span id="opt_mix_cash">комбинир.</span><br/>
			<input type="radio" for="price" name="payment" value="soft" <?php tzs_cost_print_option('payment', 'soft'); ?>/> <span id="opt_soft">софт</span>
			<input type="radio" for="price" name="payment" value="conv" <?php tzs_cost_print_option('payment', 'conv'); ?>/> <span id="opt_conv">удобная</span>
			<input type="radio" for="price" name="payment" value="on_card" <?php tzs_cost_print_option('payment', 'on_card'); ?>/> <span id="opt_on_card">на карту</span>
		</td>
		
		<td>
			<input type="checkbox" for="price" opt="true" name="payment_way_nds" value="nds" <?php tzs_cost_print_option_def('payment_way_nds'); ?>> <span id="opt_nds">НДС</span><br/>
			<input type="checkbox" for="price" opt="true" name="payment_way_ship" value="ship" <?php tzs_cost_print_option_def('payment_way_ship'); ?>> <span id="opt_ship">При погрузке</span><br/>
			<input type="checkbox" for="price" opt="true" name="payment_way_debark" value="debark" <?php tzs_cost_print_option_def('payment_way_debark'); ?>> <span id="opt_debark">При выгрузке</span><br/>
			<input type="checkbox" for="price" name="payment_way_prepay" value="prepay" <?php tzs_cost_print_option_def('payment_way_prepay'); ?>> <span id="opt_prepay">Предоплата</span>
			<input type="text" for="price" name="prepayment" value="<?php echo_val('prepayment'); ?>" size="5"> <span id="opt_prepayment">%</span><br/>
			<input type="checkbox" for="price" opt="true" name="payment_way_barg" value="barg" <?php tzs_cost_print_option_def('payment_way_barg'); ?>> <span id="opt_barg">Торг</span><br/>
		</td>
		
		<td>
			<input type="radio" name="set_price" value="0" <?php if ((isset($_POST['set_price']) && $_POST['set_price'] == '0') || !isset($_POST['set_price'])) echo 'checked="checked"'; ?>><b>Не указывать стоимость перевозки</b><br/>(цена договорная)
			<input type="checkbox" for="noprice" name="price_query" value="" <?php tzs_cost_print_option_def('price_query'); ?>> <span id="opt_price_query">Запрос цены</span><br/>
		</td>
	</tr>
</table>
</div>

<table id="shipment">
	<?php if (!$edit) {?>
	<tr>
		<th colspan="3">
			<h3>Добавление заявки на перевозку груза</h3>
			<p>укажите пожалуйста населенные пункты погрузки и выгрузки, параметры груза и контактную информацию</p>
		</th>
	</tr>
	<?php } ?>

	<!-- Погрузка/выгрузка календарики -->
	<tr>
		<td colspan="3">
			<table id="date_from_to">
				<tr>
					<td>
						Погрузка c 
					</td>
					<td>
						<input name="sh_date_from" value="<?php echo_val_def('sh_date_from', $d); ?>" type="text" id="datepicker1">
					</td>
					<td>
						по
					</td>
					<td>
						<input name="sh_date_to" value="<?php echo_val_def('sh_date_to', $d); ?>" type="text" id="datepicker2">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<!-- Города -->
	
	<tr>
		<td valign="top" align="left" width="50%">
			<table style="table-layout:fixed" width="100%">
				<tr>
					<td width="170">
						Нас. пункт погрузки:
					</td>
					<td class="cityRows">
						<input autocomplete="city" type="text" size="25" name="sh_city_from" value="<?php echo_val('sh_city_from'); ?>" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
		<td id="arrow">
			>
		</td>
		<td style="padding-left:26px" class="bottomFormBorder rightFormBorder" valign="top" align="left">
			<table class="normalText" width="100%">
				<tr>
					<td width="180">
						Нас. пункт выгрузки:
					</td>
					<td class="cityRows">
						<input autocomplete="city" type="text" size="25" name="sh_city_to" value="<?php echo_val('sh_city_to'); ?>" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3"><a id="show_dist_link" href="javascript:showDistanceDialog();">Просчитать расстояние?</a></td>
	</tr>
	<!-- Х-р груза, тип транспорта -->
	
	<tr>
		<td valign="top">
			<table width="100%" id="left">
				<tr>
					<td width="114" align="left">
						<b>Описание груза:</b>
					</td>
					<td width="300" align="left">
						<input type="text" size="25" name="sh_descr" value="<?php echo_val('sh_descr'); ?>">
					</td>
				</tr>
				<tr>
					<td align="left" colspan="2">
						 вес груза (т): <input type="text" size="5" name="sh_weight" value="<?php echo_val('sh_weight'); ?>" maxlength = "5" style="margin-right: 20px;"> объем груза (м³): <input type="text" size="5" name="sh_volume" value="<?php echo_val('sh_volume'); ?>" maxlength = "7">
					</td>
				</tr>
				<tr>
					<td align="left">
						<b>Цена:</b>
						<span id="cost_str"></span>
					</td>
					
					<td><a href="javascript: showCostForm();" id="button_price">Указать</a></td>
				</tr>
				<tr>
					<td width="114" align="left">
						<b>Комментарии:</b>
					</td>
					<td width="300" align="left">
						<input type="text" size="25" name="comment" value="<?php echo_val('comment'); ?>">
					</td>
				</tr>
			</table>
		</td>
		<td valign="top" align="left" colspan="2">
			<table cellspacing="0" id="right">
				<tr>
					<td width="105" align="left">
						Тип транспорта:
					</td>
					<td width="300" align="left">
						<select name="trans_type">
							<?php
								foreach ($GLOBALS['tzs_tr_types'] as $key => $val) {
									echo '<option value="'.$key.'" ';
									if ((isset($_POST['trans_type']) && $_POST['trans_type'] == $key) || (!isset($_POST['trans_type']) && $key == 0)) {
										echo 'selected="selected"';
									}
									echo '>'.htmlspecialchars($val).'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align="left">
						Кол-во машин:
					</td>
					<td align="left">
						<input type="text" size="5" name="trans_count" value="<?php echo_val('trans_count'); ?>" maxlength = "2" placeholder = "1">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="checkbox" name="set_dim" id="set_dim" <?php if (isset($_POST['set_dim'])) echo 'checked="checked"'; ?>>
						<span id="dim_label">Указать размеры груза, в метрах&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" >
						<span>
							длина:&nbsp;
							<input type="text" size="3" name="sh_length" id="sh_length" value="<?php echo_val('sh_length'); ?>" maxlength = "5">
							&nbsp;&nbsp;м&nbsp;&nbsp;&nbsp;&nbsp;ширина:&nbsp;
							<input type="text" size="3" name="sh_width" id="sh_width" value="<?php echo_val('sh_width'); ?>" maxlength = "5">
							&nbsp;&nbsp;м&nbsp;&nbsp;&nbsp;&nbsp;высота:&nbsp;
							<input type="text" size="3" name="sh_height" id="sh_height" value="<?php echo_val('sh_height'); ?>" maxlength = "5">
							&nbsp;&nbsp;м
						</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
	
	<?php if ($edit) {?>
		<input type="hidden" name="action" value="editshipment"/>
		<input type="hidden" name="id" value="<?php echo_val('id'); ?>"/>
	<?php } else { ?>
		<input type="hidden" name="action" value="addshipment"/>
	<?php } ?>
	<input type="hidden" name="formName" value="shipment" />
	<input name="addpost" type="submit" id="addpostsub" class="submit_button" value="<?php echo $edit ? "Изменить" : "Разместить" ?>"/>
	</form>
	
	<script>
		function setEnabledByInstance(cl, el, enabled) {
			if (enabled) {
				el.removeAttr('disabled');
				jQuery(cl).find('span[id=opt_'+el.attr('value')+']').attr('style', 'color: #000;');
			} else {
				el.attr('disabled', 'disabled');
				jQuery(cl).find('span[id=opt_'+el.attr('value')+']').attr('style', 'color: #d3d3d3;');
			}
		}
		
		function setEnabled(cl, name, enabled) {
			if (enabled) {
				var el = jQuery(cl).find('[name='+name+']');
				el.removeAttr('disabled');
				jQuery(cl).find('span[id=opt_'+el.attr('value')+']').attr('style', 'color: #000;');
			} else {
				var el = jQuery(cl).find('[name='+name+']');
				el.attr('disabled', 'disabled');
				jQuery(cl).find('span[id=opt_'+el.attr('value')+']').attr('style', 'color: #d3d3d3;');
			}
		}
	
		function showHide(cl) {
			var price = jQuery(cl).find('input:radio[name=set_price]:checked').val() == '1';
			jQuery(cl).find('[for=price]').each(function() {
				setEnabledByInstance(cl, jQuery(this), price);
			});
			jQuery(cl).find('[for=noprice]').each(function() {
				setEnabledByInstance(cl, jQuery(this), !price);
			});
			var prepay = jQuery(cl).find('input[name=payment_way_prepay]').is(':checked');
			setEnabled(cl, 'prepayment', price && prepay);
			
			if (price && prepay) {
				jQuery(cl).find('span[id=opt_prepayment]').attr('style', 'color: #000;');
			} else {
				jQuery(cl).find('span[id=opt_prepayment]').attr('style', 'color: #d3d3d3;');
			}
		}
	
		function showCostForm() {
			var el = jQuery('#cost_div');
			var sel = jQuery(el).find('select[name=cost_curr] option:selected');
			
			var cl = jQuery(el).clone();
			if (sel != null) {
				jQuery(cl).find("select[name=cost_curr] option[value='"+sel.val()+"']").attr('selected', 'selected');
			}
			
			jQuery(cl).find('input[name=set_price]').click(function () {
				showHide(cl);
			});
			jQuery(cl).find('input[name=payment_way_prepay]').click(function () {
				showHide(cl);
			});
			showHide(cl);
			
			jQuery(cl).appendTo('body')
				.dialog({
					modal: true,
					title: 'Стоимость перевозки',
					zIndex: 10000,
					autoOpen: true,
					width: 'auto',
					resizable: false,
					buttons: {
						'Сохранить': function () {
							jQuery(this).dialog("close");
							var newEl = jQuery(this);
							newEl.attr('style', 'display:none;');
							newEl.attr('id', 'cost_div');
							var cl1 = newEl.clone();
							var sel = jQuery(newEl).find('select[name=cost_curr] option:selected');
							if (sel != null) {
								jQuery(cl1).find("select[name=cost_curr] option[value='"+sel.val()+"']").attr('selected', 'selected');
							}
							el.replaceWith(cl1);
							updateCostValue();
						},
						'Отмена': function () {
							jQuery(this).dialog("close");
						}
					},
					close: function (event, ui) {
						jQuery(this).remove();
					}
				});
		}
		
		function updateCostValue() {
			var str = '';
			if (jQuery('input:radio[name=set_price]:checked').val() == '1') {
				str += jQuery('input[name=price]').val();
				str += ' ';
				str += jQuery('select[name=cost_curr] option:selected').text();
				
				var opt = jQuery('input:radio[name=payment]:checked');
				if (opt.val() != null) {
					str += ', ';
					str += jQuery('#opt_'+opt.val()).html();
				}
				
				jQuery("input[opt='true']").each(function() {
					if (jQuery(this).is(':checked')) {
						str += ', ';
						str += jQuery('#opt_'+jQuery(this).val()).html();
					}
				});
				
				if (jQuery('input[name=payment_way_prepay]').is(':checked')) {
					str += ', предоплата: ';
					str += jQuery('input[name=prepayment]').val();
					str += '%';
				}
			} else {
				if (jQuery('input:radio[name=price_query]').is(':checked')) {
					str += 'запрос цены';
				}
			}
			jQuery('#cost_str').html(str);
		}
		
		function onSetDim(ch) {
			if (ch) {
				jQuery("#sh_length").removeAttr("disabled");
				jQuery("#sh_width").removeAttr("disabled");
				jQuery("#sh_height").removeAttr("disabled");
			} else {
				jQuery("#sh_length").attr("disabled", "disabled");
				jQuery("#sh_width").attr("disabled", "disabled");
				jQuery("#sh_height").attr("disabled", "disabled");
			}
		}
		
		function showDistanceDialog() {
			displayDistance([jQuery('input[name=sh_city_from]').val(), jQuery('input[name=sh_city_to]').val()], null);
		}
		
		jQuery('#set_dim').click(function() {
			onSetDim(this.checked);
		});
		
		jQuery(document).ready(function(){
			jQuery('#bpost').submit(function() {
				jQuery('#addpostsub').attr('disabled','disabled');
				return true;
			});
			jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
			jQuery( "#datepicker1" ).datepicker({ dateFormat: "dd.mm.yy" });
			jQuery( "#datepicker2" ).datepicker({ dateFormat: "dd.mm.yy" });
			onSetDim(jQuery('#set_dim').prop('checked'));
			updateCostValue();
		});
	</script>
<?php
}

function tzs_edit_shipment($id) {
	$sh_date_from = get_param('sh_date_from');
	$sh_date_to = get_param('sh_date_to');
	$sh_city_from = get_param('sh_city_from');
	$sh_city_to = get_param('sh_city_to');
	$comment = get_param('comment');
	
	$sh_descr = get_param('sh_descr');
	$sh_weight = get_param_def('sh_weight','0');
	$sh_volume = get_param_def('sh_volume','0');
	$trans_type = get_param('trans_type');
	$trans_count = get_param('trans_count');
	
	$set_dim = isset($_POST['set_dim']);
	$sh_length = get_param('sh_length');
	$sh_height = get_param('sh_height');
	$sh_width = get_param('sh_width');
	
	$sh_date_from = is_valid_date($sh_date_from);
	$sh_date_to = is_valid_date($sh_date_to);
	
	$errors = array();
	
	// cost
	$price = get_param_def('set_price','0') == '1';
	$price_json = array();
	$price_json['set_price'] = $price ? 1 : 0;
	if ($price) {
		$price_val = get_param_def('price','0');
		if (!is_valid_num($price_val)) {
			array_push($errors, "Неверно задана стоимость");
		} else {
			$price_json['price'] = floatval($price_val);
		}
		
		$cost_curr = get_param_def('cost_curr','0');
		if (!is_valid_num($cost_curr) || !isset($GLOBALS['tzs_curr'][intval($cost_curr)])) {
			array_push($errors, "Неверно задана валюта");
		} else {
			$price_json['cost_curr'] = intval($cost_curr);
		}
		
		$payment = get_param_def('payment', null);
		if ($payment != null) {
			if ($payment != 'nocash' && $payment != 'cash' && $payment != 'mix_cash' && $payment != 'soft' && $payment != 'conv' && $payment != 'on_card') {
				array_push($errors, "Неверно задана форма оплаты");
			} else {
				$price_json['payment'] = $payment;
			}
		}
		
		if (isset($_POST['payment_way_nds']))
			$price_json['payment_way_nds'] = true;
		if (isset($_POST['payment_way_ship']))
			$price_json['payment_way_ship'] = true;
		if (isset($_POST['payment_way_debark']))
			$price_json['payment_way_debark'] = true;
		if (isset($_POST['payment_way_barg']))
			$price_json['payment_way_barg'] = true;
			
		if (isset($_POST['payment_way_prepay'])) {
			$price_json['payment_way_prepay'] = true;
			$prepayment = get_param_def('prepayment', '0');
			if (!is_valid_num($prepayment) || floatval($prepayment) > 100) {
				array_push($errors, "Неверно задан размер предоплаты");
			} else {
				$price_json['prepayment'] = floatval($prepayment);
			}
		}
	} else {
		if (isset($_POST['price_query']))
			$price_json['price_query'] = true;
	}
	// ----
	
	if ($sh_date_from == null || $sh_date_to == null) {
		array_push($errors, "Неверный формат даты");
	}
	
	if (!is_valid_city($sh_city_from)) {
		array_push($errors, "Неверный пункт погрузки");
	}
	
	if (!is_valid_city($sh_city_to)) {
		array_push($errors, "Неверный пункт разгрузки");
	}
	
	if (strlen($sh_descr) < 2) {
		array_push($errors, "Введите описание груза");
	}
	
	if (!is_valid_num_zero($sh_weight)) {
		array_push($errors, "Неверно задан вес");
	}
	
	if (!is_valid_num_zero($sh_volume)) {
		array_push($errors, "Неверно задан объем");
	}
	
	if (strlen($trans_count) == 0) {
		$trans_count = '1';
	}
	if (!is_valid_num($trans_count)) {
		array_push($errors, "Неверно задано количество машин");
	}
	
	if (!is_numeric($trans_type) || intval($num) < 0) {
		array_push($errors, "Неверно задан тип");
	}
	
	if ($set_dim) {
		if (!is_valid_num($sh_length)) {
			array_push($errors, "Неверно задана длинна груза");
		}
		if (!is_valid_num($sh_width)) {
			array_push($errors, "Неверно задана ширина груза");
		}
		if (!is_valid_num($sh_height)) {
			array_push($errors, "Неверно задана высота груза");
		}
	} else {
		$sh_length = '0';
		$sh_width = '0';
		$sh_height = '0';
	}
	
	$user_id = get_current_user_id();
	
	$from_info = null;
	$to_info = null;
	if (count($errors) == 0) {
		$from_info = tzs_yahoo_convert($sh_city_from);
		if (isset($from_info["error"])) {
			array_push($errors, "Не удалось распознать населенный пункт погрузки: ".$from_info["error"]);
		}
		$to_info = tzs_yahoo_convert($sh_city_to);
		if (isset($to_info["error"])) {
			array_push($errors, "Не удалось распознать населенный пункт выгрузки: ".$to_info["error"]);
		}
	}
	
	if (count($errors) > 0) {
		tzs_print_shipment_form($errors, $id > 0);
	} else {
		global $wpdb;
	
		$sh_date_from = date('Y-m-d', mktime(0, 0, 0, $sh_date_from['month'], $sh_date_from['day'], $sh_date_from['year']));
		$sh_date_to = date('Y-m-d', mktime(0, 0, 0, $sh_date_to['month'], $sh_date_to['day'], $sh_date_to['year']));
		
		$dis = tzs_calculate_distance(array($sh_city_from, $sh_city_to));
		
		if ($id == 0) {
			$sql = $wpdb->prepare("INSERT INTO ".TZS_SHIPMENT_TABLE.
				" (time, last_edited, user_id, sh_date_from, sh_date_to, sh_city_from, sh_city_to, sh_descr, sh_weight, sh_volume, sh_length, sh_height, sh_width, trans_count, trans_type, active, comment, cost, distance, from_cid,from_rid,from_sid,to_cid,to_rid,to_sid)".
				" VALUES (now(), NULL, %d, %s, %s, %s, %s, %s, %f, %f, %f, %f, %f, %d, %d, 1, %s, %s, %d, %d,%d,%d,%d,%d,%d);",
				$user_id, $sh_date_from, $sh_date_to, stripslashes_deep($sh_city_from), stripslashes_deep($sh_city_to),
				stripslashes_deep($sh_descr), floatval($sh_weight), floatval($sh_volume), floatval($sh_length),
				floatval($sh_height), floatval($sh_width), intval($trans_count), intval($trans_type), stripslashes_deep($comment), stripslashes_deep(json_encode($price_json)), round($dis['distance'] / 1000),
				$from_info["country_id"],$from_info["region_id"],$from_info["city_id"],$to_info["country_id"],$to_info["region_id"],$to_info["city_id"]);
		
			if (false === $wpdb->query($sql)) {
				array_push($errors, "Не удалось опубликовать Ваш груз. Свяжитесь, пожалуйста, с администрацией сайта");
				array_push($errors, $wpdb->last_error);
				$errors = array_merge($errors, $dis['errors']);
				tzs_print_shipment_form($errors, false);
			} else {
				print_errors($dis['errors']);
				echo "Ваш груз опубликован!";
				echo "<br/>";
				echo '<a href="/view-shipment/?id='.tzs_find_latest_shipment_rec().'&spis=new">Просмотреть груз</a>';
			}
		} else {
			$sql = $wpdb->prepare("UPDATE ".TZS_SHIPMENT_TABLE." SET ".
				" last_edited=now(), sh_date_from=%s, sh_date_to=%s, sh_city_from=%s, sh_city_to=%s, sh_descr=%s, sh_weight=%f, sh_volume=%f, sh_length=%f, sh_height=%f, sh_width=%f, trans_count=%d, trans_type=%d, comment=%s, cost=%s, distance=%d, ".
				" from_cid=%d,from_rid=%d,from_sid=%d,to_cid=%d,to_rid=%d,to_sid=%d".
				" WHERE id=%d AND user_id=%d;", $sh_date_from, $sh_date_to, stripslashes_deep($sh_city_from),
				stripslashes_deep($sh_city_to), stripslashes_deep($sh_descr), floatval($sh_weight), floatval($sh_volume),
				floatval($sh_length), floatval($sh_height), floatval($sh_width), intval($trans_count), intval($trans_type), stripslashes_deep($comment), stripslashes_deep(json_encode($price_json)), round($dis['distance'] / 1000),
				$from_info["country_id"],$from_info["region_id"],$from_info["city_id"],$to_info["country_id"],$to_info["region_id"],$to_info["city_id"],
				$id, $user_id);
			
			if (false === $wpdb->query($sql)) {
				array_push($errors, "Не удалось изменить Ваш груз. Свяжитесь, пожалуйста, с администрацией сайта");
				array_push($errors, $wpdb->last_error);
				$errors = array_merge($errors, $dis['errors']);
				tzs_print_shipment_form($errors, true);
			} else {
				print_errors($dis['errors']);
				echo "Ваш груз изменен";
				echo "<br/>";
				echo '<a href="/view-shipment/?id='.$id.'">Просмотреть груз</a>';
			}
		}
	}
}

function tzs_front_end_del_shipment_handler($attrs) {
	ob_start();
	
	$user_id = get_current_user_id();
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
	
	if ( !is_user_logged_in() ) {
		print_error("Вход в систему обязателен");
	} else if ($sh_id <= 0) {
		print_error('Груз не найден');
	} else {
		global $wpdb;
		$sql = "DELETE FROM ".TZS_SHIPMENT_TABLE." WHERE id=$sh_id AND user_id=$user_id;";
		if (false === $wpdb->query($sql)) {
			$errors = array();
			array_push($errors, "Не удалось удалить Ваш груз. Свяжитесь, пожалуйста, с администрацией сайта");
			array_push($errors, $wpdb->last_error);
			print_errors($errors);
		} else {
			echo "Груз удален";
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

function tzs_front_end_edit_shipment_handler($atts) {
	ob_start();
	
	$user_id = get_current_user_id();
	$sh_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
	
	if ( !is_user_logged_in() ) {
		print_error("Вход в систему обязателен");
	} else if ($sh_id <= 0) {
		print_error('Груз не найден');
	} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST['action'] ) && $_POST['action'] == 'editshipment' && ($_POST['formName'] == 'shipment')) {
		$id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval($_POST['id']) : 0;
		tzs_edit_shipment($id);
	} else {
		global $wpdb;
		$sql = "SELECT * FROM ".TZS_SHIPMENT_TABLE." WHERE id=$sh_id AND user_id=$user_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			print_error('Не удалось отобразить информацию о грузе. Свяжитесь, пожалуйста, с администрацией сайта');
		} else if ($row == null) {
			print_error('Груз не найден');
		} else {
			$cost = json_decode($row->cost);
			foreach ($cost as $key => $val) {
				$_POST[$key] = ''.$val;
			}
			
			$_POST['sh_date_from'] = date("d.m.Y", strtotime($row->sh_date_from));
			$_POST['sh_date_to'] = date("d.m.Y", strtotime($row->sh_date_to));
			$_POST['sh_city_from'] = $row->sh_city_from;
			$_POST['sh_city_to'] = $row->sh_city_to;
			$_POST['sh_descr'] = $row->sh_descr;
			$_POST['comment'] = $row->comment;
			if ($row->sh_weight > 0)
				$_POST['sh_weight'] = ''.remove_decimal_part($row->sh_weight);
			if ($row->sh_volume > 0)
				$_POST['sh_volume'] = ''.remove_decimal_part($row->sh_volume);
			$_POST['trans_type'] = ''.$row->trans_type;
			$_POST['trans_count'] = ''.$row->trans_count;
			if ($row->sh_length > 0 || $row->sh_height > 0 || $row->sh_width > 0) {
				$_POST['set_dim'] = '';
				$_POST['sh_width'] = ''.remove_decimal_part($row->sh_width);
				$_POST['sh_height'] = ''.remove_decimal_part($row->sh_height);
				$_POST['sh_length'] = ''.remove_decimal_part($row->sh_length);
			}
			$_POST['id'] = ''.$row->id;
			tzs_print_shipment_form(null, true);
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

function tzs_front_end_shipment_handler($atts) {
	ob_start();
	
	if ( !is_user_logged_in() ) {
		print_error("Вход в систему обязателен");
	} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST['action'] ) && $_POST['action'] == 'addshipment' && ($_POST['formName'] == 'shipment')) {
		tzs_edit_shipment(0);
	} else {
		tzs_print_shipment_form(null);
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>