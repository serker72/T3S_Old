<?php
function tzs_yahoo_get_id() {
	global $wpdb;
	$res = $wpdb->get_row("SELECT id, appid FROM ".TZS_YAHOO_KEYS_TABLE." ORDER BY last_used ASC LIMIT 1;");
	if (count($res) == 0 && $wpdb->last_error != null) {
		return NULL;
	}
	$appid = $res->appid;
	$id = $res->id;
	
	$sql = "UPDATE ".TZS_YAHOO_KEYS_TABLE." SET last_used=now() WHERE id=$id;";
	
	if (false === $wpdb->query($sql)) {
		return NULL;
	}
	
	return $appid;
}

function tzs_yahoo_convert0($key, $city_str) {
	$url = "http://where.yahooapis.com/v1/places.q('".urlencode($city_str)."')?format=json&lang=ru&appid=$key";
	//echo $url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result=curl_exec($ch);
	curl_close($ch);
	
	$res = json_decode($result, true);
	
	if (isset($res["error"])) {
		return array("error" => $res["error"]["description"]);
	}
	
	if (!isset($res["places"])) {
		return array("error" => "Неверный ответ сервера: places не найден");
	}
	
	if ($res["places"]["count"] <= 0) {
		return array("error" => "Совпадений не найдено");
	}
	
	$rec = $res["places"]["place"][0];
	
	$country = isset($rec["country"]) ? $rec["country"] : NULL;
	$country_code = isset($rec["country attrs"]) && isset($rec["country attrs"]["code"]) ? $rec["country attrs"]["code"] : NULL;
	$country_id = isset($rec["country attrs"]) && isset($rec["country attrs"]["woeid"]) ? $rec["country attrs"]["woeid"] : NULL;
	
	$region = isset($rec["admin1"]) ? $rec["admin1"] : NULL;
	$region_id = isset($rec["admin1 attrs"]) && isset($rec["admin1 attrs"]["woeid"]) ? $rec["admin1 attrs"]["woeid"] : NULL;
	
	$city = isset($rec["locality1"]) ? $rec["locality1"] : NULL;
	$city_id = isset($rec["locality1 attrs"]) && isset($rec["locality1 attrs"]["woeid"]) ? $rec["locality1 attrs"]["woeid"] : NULL;
	
        // KSK - добавляем выбор кооринат города для сохранения в таблице
        $lat = isset($rec["centroid"]) && isset($rec["centroid"]["latitude"])? $rec["centroid"]["latitude"] : NULL;
        $lng = isset($rec["centroid"]) && isset($rec["centroid"]["longitude"])? $rec["centroid"]["longitude"] : NULL;

        // KSK - добавляем проверку данных города, полученных от сервиса Yahoo
	if ($country_id == NULL || $city_id == NULL) {
		return array("error" => "Совпадений не найдено");
		//return array("error" => "Сервис Yahoo не располагает информацией о населенном пункте ".$city_str);
	}
        
	$result = array("country" => $country, "country_code" => $country_code, "country_id" => $country_id,
		"region" => $region, "region_id" => $region_id, "city" => $city, "city_id" => $city_id,
                "lat" => $lat, "lng" => $lng);
	
	return $result;
}

function tzs_check_country($rec) {
	if (!isset($rec["country_id"]))
		return;
	$id = $rec["country_id"];
	global $wpdb;
	$res = $wpdb->get_row("SELECT COUNT(id) AS cnt FROM ".TZS_COUNTRIES_TABLE." WHERE country_id=$id;");
	if (count($res) == 0 && $wpdb->last_error != null)
		return;
	if ($res->cnt > 0)
		return;
	tzs_add_country($rec);
}
function tzs_check_region($rec) {
	if (!isset($rec["region_id"]))
		return;
	$id = $rec["region_id"];
	global $wpdb;
	$res = $wpdb->get_row("SELECT COUNT(id) AS cnt FROM ".TZS_REGIONS_TABLE." WHERE region_id=$id;");
	if (count($res) == 0 && $wpdb->last_error != null)
		return;
	if ($res->cnt > 0)
		return;
	tzs_add_region($rec);
}
function tzs_check_city($rec) {
	if (!isset($rec["city_id"]))
		return;
	$id = $rec["city_id"];
	global $wpdb;
	$res = $wpdb->get_row("SELECT COUNT(id) AS cnt FROM ".TZS_CITIES_TABLE." WHERE city_id=$id;");
	if (count($res) == 0 && $wpdb->last_error != null)
		return;
	if ($res->cnt > 0)
		return;
	tzs_add_city($rec);
}

function tzs_add_country($rec) {
	global $wpdb;
	$sql = $wpdb->prepare("INSERT INTO ".TZS_COUNTRIES_TABLE.
		" (country_id, code, title_ru, title_ua, title_en)".
		" VALUES (%d, %s, %s, %s, %s);",
		$rec["country_id"], stripslashes_deep($rec["country_code"]),
		stripslashes_deep($rec["country"]), stripslashes_deep($rec["country_ua"]), stripslashes_deep($rec["country_en"]));
	
	return ($wpdb->query($sql) !== false);
}
function tzs_add_region($rec) {
	global $wpdb;
	$sql = $wpdb->prepare("INSERT INTO ".TZS_REGIONS_TABLE.
		" (country_id, region_id, title_ru, title_ua, title_en)".
		" VALUES (%d, %d, %s, %s, %s);",
		$rec["country_id"], $rec["region_id"],
		stripslashes_deep($rec["region"]), stripslashes_deep($rec["region_ua"]), stripslashes_deep($rec["region_en"]));
	
	return ($wpdb->query($sql) !== false);
}
function tzs_add_city($rec) {
	global $wpdb;
	$sql = $wpdb->prepare("INSERT INTO ".TZS_CITIES_TABLE.
		" (country_id, region_id, city_id, title_ru, title_ua, title_en, lat, lng)".
		" VALUES (%d, %d, %d, %s, %s, %s, %.6F, %.6F);",
		$rec["country_id"], $rec["region_id"], $rec["city_id"],
		stripslashes_deep($rec["city"]), stripslashes_deep($rec["city_ua"]), stripslashes_deep($rec["city_en"]),
                $rec["lat"], $rec["lng"]);
	
	return ($wpdb->query($sql) !== false);
}

function tzs_yahoo_info($id, $lang) {
	$key = tzs_yahoo_get_id();
	if ($key == NULL)
		return false;
	
	$url = "http://where.yahooapis.com/v1/place/$id?format=json&lang=$lang&appid=$key";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result=curl_exec($ch);
	curl_close($ch);
	
	$res = json_decode($result, true);
	
	if (isset($res["error"])) {
		return false;
	}
	
	if (!isset($res["place"])) {
		return false;
	}
	
	$rec = $res["place"];
	
	$country = isset($rec["country"]) ? $rec["country"] : NULL;
	$country_code = isset($rec["country attrs"]) && isset($rec["country attrs"]["code"]) ? $rec["country attrs"]["code"] : NULL;
	$country_id = isset($rec["country attrs"]) && isset($rec["country attrs"]["woeid"]) ? $rec["country attrs"]["woeid"] : NULL;
	
	$region = isset($rec["admin1"]) ? $rec["admin1"] : NULL;
	$region_id = isset($rec["admin1 attrs"]) && isset($rec["admin1 attrs"]["woeid"]) ? $rec["admin1 attrs"]["woeid"] : NULL;
	
	$city = isset($rec["locality1"]) ? $rec["locality1"] : NULL;
	$city_id = isset($rec["locality1 attrs"]) && isset($rec["locality1 attrs"]["woeid"]) ? $rec["locality1 attrs"]["woeid"] : NULL;
	
	$result = array("country" => $country, "country_code" => $country_code, "country_id" => $country_id,
		"region" => $region, "region_id" => $region_id, "city" => $city, "city_id" => $city_id);
	
	return $result;
}

function tzs_yahoo_fill($rec) {
	$id;
	if ($rec["city_id"] != null)
		$id = $rec["city_id"];
	elseif ($rec["region_id"] != null)
		$id = $rec["region_id"];
	elseif ($rec["country_id"] != null)
		$id = $rec["country_id"];
	else
		return false;
	
	// yahoo не знает украинский язык!!!
	//$ua = tzs_yahoo_info($id, "ua");
	//if ($ua === false)
	//	return false;
	$en = tzs_yahoo_info($id, "en");
	if ($en === false)
		return false;
	//$rec["country_ua"] = $ua["country"];
	//$rec["region_ua"] = $ua["region"];
	//$rec["city_ua"] = $ua["city"];
	
	// поэтому используем рус
	$rec["country_ua"] = $rec["country"];
	$rec["region_ua"] = $rec["region"];
	$rec["city_ua"] = $rec["city"];
	
	$rec["country_en"] = $en["country"];
	$rec["region_en"] = $en["region"];
	$rec["city_en"] = $en["city"];
	
	return $rec;
}

function tzs_yahoo_convert($city_str) {
	$key = tzs_yahoo_get_id();
	if ($key == NULL) {
		return array("error" => "Внутренняя ошибка. AppID не найден");
	}
	$res = tzs_yahoo_convert0($key, $city_str);
	if (!isset($res["error"])) {
		$res = tzs_yahoo_fill($res);
		tzs_check_country($res);
		tzs_check_region($res);
		tzs_check_city($res);
	}
	return $res;
}

function tzs_get_country($id) {
	global $wpdb;
	$sql = "SELECT title_ru FROM ".TZS_COUNTRIES_TABLE." WHERE country_id=$id;";
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
		return "Ошибка";
	} else if ($row == null) {
		return "Не_найдено";
	}
	return $row->title_ru;
}

function tzs_get_country_code($id) {
	global $wpdb;
	$sql = "SELECT code FROM ".TZS_COUNTRIES_TABLE." WHERE country_id=$id;";
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
		return "Ошибка";
	} else if ($row == null) {
		return "Не_найдено";
	}
	return $row->code;
}

function tzs_get_region($id) {
	global $wpdb;
	$sql = "SELECT title_ru FROM ".TZS_REGIONS_TABLE." WHERE region_id=$id;";
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
		return "Ошибка";
	} else if ($row == null) {
		return "Не_найдено";
	}
	return $row->title_ru;
}

function tzs_get_city($id) {
	global $wpdb;
	$sql = "SELECT title_ru FROM ".TZS_CITIES_TABLE." WHERE city_id=$id;";
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
		return "Ошибка";
	} else if ($row == null) {
		return "Не_найдено";
	}
	return $row->title_ru;
}

function tzs_city_to_str($country_id, $region_id, $city_id, $def, $title='') {
	$def = htmlspecialchars($def);
	if ($city_id != NULL && $city_id > 0) {
		return '<span title="'.($title !== '' ? $title : $def).'"><strong>'.htmlspecialchars(tzs_get_city($city_id)).'</strong>'.(($region_id != NULL && $region_id > 0 && $region_id != 20070188) ? '<br>'.htmlspecialchars(tzs_get_region($region_id)) : '').(($country_id != 23424976) ? ' ('.htmlspecialchars(tzs_get_country_code($country_id)).')' : '').'</span>';
	} elseif ($region_id != NULL && $region_id > 0) {
		return "<span title=\"$def\">".htmlspecialchars(tzs_get_region($region_id))." (".htmlspecialchars(tzs_get_country_code($country_id)).")</span>";
	} elseif ($country_id != NULL && $country_id > 0) {
		return "<span title=\"$def\">".htmlspecialchars(tzs_get_country($country_id))."</span>";
	} else
		return "<span>".$def."</span>";
}

function tzs_city_to_ids($city, $region_id, $country_id) {
	$key = tzs_yahoo_get_id();
	if ($key == NULL) {
		return array("error" => "Внутренняя ошибка. AppID не найден");
	}
	
	$city_str = $city;
	
	global $wpdb;
	
	if ($region_id > 0) {
		// convert region_id to title
		$sql = "SELECT title_ru FROM ".TZS_REGIONS_TABLE." WHERE region_id=$region_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			return array("error" => "Внутренняя ошибка (область).");
		} else if ($row == null) {
			return array("error" => "Неизвестная область.");
		}
		$city_str .= ' '.$row->title_ru;
	}
	
	if ($country_id > 0) {
		// convert country_id to title
		$sql = "SELECT title_ru FROM ".TZS_COUNTRIES_TABLE." WHERE country_id=$country_id;";
		$row = $wpdb->get_row($sql);
		if (count($row) == 0 && $wpdb->last_error != null) {
			return array("error" => "Внутренняя ошибка (страна).");
		} else if ($row == null) {
			return array("error" => "Неизвестная страна.");
		}
		$city_str .= ' '.$row->title_ru;
	}
	
	$res = tzs_city_ids_from_db($city_str);
	if (!isset($res["error"]) && !isset($res["ids"])) {
		$res = tzs_yahoo_convert1($key, $city_str);
		if (!isset($res["error"])) {
			$ids = $res['ids'];
			$ids_str = '';
			foreach ($ids as $id) {
				if (strlen($ids_str) > 0)
					$ids_str .= ' ';
				$ids_str .= $id;
			}
			$sql = $wpdb->prepare("INSERT INTO ".TZS_CITY_IDS_TABLE." (title, ids) VALUES (%s, %s);", $city_str, $ids_str);
			if (false === $wpdb->query($sql)) {
				return array("error" => "Не удалось сохранить результат в кэш. ".$wpdb->last_error);
			}
		}
	}
	
	if (!isset($res['ids']) || count($res['ids']) == 0)
		return array("error" => "Населенный пункт не найден: ".$city_str);
	return $res;
}

function tzs_city_ids_from_db($city) {
	global $wpdb;
	$sql = $wpdb->prepare("SELECT ids FROM ".TZS_CITY_IDS_TABLE." WHERE title=%s;", $city);
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
		return array('error' => 'Не удалось извлечь информацию из кэша. Свяжитесь, пожалуйста, с администрацией сайта');
	} else if ($row == null) {
		return array();
	} else {
		$ids = array();
		if (strlen($row->ids) > 0) {
			$ids_str = explode(' ', $row->ids);
			foreach ($ids_str as $id) {
				array_push($ids, floatval($id));
			}
		}
                
		return array('ids' => $ids);
	}
}

//*******************************************************************************
// KSK
function tzs_city_from_radius_to_ids($city, $region_id, $country_id, $radius_value) {
    $key = tzs_yahoo_get_id();
    if ($key == NULL) {
        return array("error" => "Внутренняя ошибка. AppID не найден");
    }
	
    $city_str = $city;
    $radius = $radius_value + 1;
    
    global $wpdb;
    
    $sql1 = "SELECT city_id, lat, lng FROM ".TZS_CITIES_TABLE." WHERE title_ru='".$city."'";
    $lat = null;
    $lng = null;
	
    if ($region_id > 0) {
        $sql1 .= " AND region_id=".$region_id;
        
        // convert region_id to title
        $sql = "SELECT title_ru FROM ".TZS_REGIONS_TABLE." WHERE region_id=$region_id;";
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
            return array("error" => "Внутренняя ошибка (область).");
	} else if ($row == null) {
            return array("error" => "Неизвестная область.");
	}
        
	$city_str .= ' '.$row->title_ru;
    }
	
    if ($country_id > 0) {
        $sql1 .= " AND $country_id=".$country_id;
        
	// convert country_id to title
	$sql = "SELECT title_ru FROM ".TZS_COUNTRIES_TABLE." WHERE country_id=$country_id;";
	$row = $wpdb->get_row($sql);
	if (count($row) == 0 && $wpdb->last_error != null) {
            return array("error" => "Внутренняя ошибка (страна).");
	} else if ($row == null) {
            return array("error" => "Неизвестная страна.");
	}
	
        $city_str .= ' '.$row->title_ru;
    }
    
    
    ksk_debug($city_str, 'tzs_city_from_radius_to_ids: Поиск населенных пунктов в радиусе '.$radius_value);
    
    $row = $wpdb->get_row($sql1);
    if ($wpdb->last_error != null) {
        return array("error" => "При поиске населенного пункта погрузки '".$city_str."' возникла ошибка :".$wpdb->last_error);
    } 
    // Запись обнаружена - возьмем координаты
    else if ($row != null) {
        $lat = $row->lat;
        $lng = $row->lng;
        ksk_debug($lat.':'.$lng, 'tzs_city_from_radius_to_ids: Запись обнаружена - возьмем координаты');
    }
    // Запись не обнаружена - поищем в yahoo и возьмем координаты
    else {
        $res = tzs_yahoo_convert($city_str);
        ksk_debug($res, 'tzs_city_from_radius_to_ids: Запись не обнаружена - поищем в yahoo и возьмем координаты');
        if (isset($res["error"])) {
            return array("error" => "Не удалось распознать населенный пункт погрузки: ".$res["error"]);
	}
        // Координаты
        $lat = $res['lat'];
        $lng = $res['lng'];
    }
    
    //******************************
    //* Поищем в таблице населенные пункты в указанном радиусе от указанного
    //******************************
    $sql  = "SELECT city_id, title_ru, lat, lng,";
    $sql .= " (6371.009 * acos(sin(RADIANS(lat)) * sin(RADIANS(".$lat.")) + cos(RADIANS(lat)) * cos(RADIANS(".$lat.")) * cos(RADIANS(lng) - RADIANS(".$lng.")))) as distance";
    $sql .= " FROM ".TZS_CITIES_TABLE." WHERE lat IS NOT NULL AND lng IS NOT NULL";
    $sql .= " HAVING distance < ".$radius;
    $rows = $wpdb->get_results($sql);
    if ($wpdb->last_error != null) {
        return array("error" => "При поиске пунктов погрузки в радиусе ".$radius_value." км от населенного пункта '".$city_str."' возникла ошибка :".$wpdb->last_error);
    }
    
    $ids = array();
    ksk_debug($rows, 'tzs_city_from_radius_to_ids: населенные пункты в указанном радиусе от указанного');
  
    foreach ( $rows as $row ) {
        if ($row->city_id != null)
//            ksk_debug($row->city_id.' - '.$row->title_ru, 'tzs_city_from_radius_to_ids: населенные пункты в указанном радиусе от указанного');
            array_push($ids, $row->city_id);
    }
    
    $res = array('ids' => $ids);
    
    if (!isset($res['ids']) || count($res['ids']) == 0)
        return array("error" => "Пункты погрузки в радиусе ".$radius_value." км от населенного пункта '".$city_str."' не обнаружены");
    
    return $res;
}
//*******************************************************************************

function tzs_yahoo_convert1($key, $city_str) {
	$url = "http://where.yahooapis.com/v1/places.q('".urlencode($city_str)."');start=0;count=1000?format=json&lang=ru&appid=$key";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result=curl_exec($ch);
	curl_close($ch);
	
	$res = json_decode($result, true);
	
	if (isset($res["error"])) {
		return array("error" => $res["error"]["description"]);
	}
	
	$ids = array();
	
	if (isset($res["places"]) && isset($res["places"]["count"]) && $res["places"]["count"] > 0) {
		foreach ($res["places"]["place"] as $rec) {
			$city_id = isset($rec["locality1 attrs"]) && isset($rec["locality1 attrs"]["woeid"]) ? $rec["locality1 attrs"]["woeid"] : NULL;
			if ($city_id != null)
				array_push($ids, $city_id);
		}
	}
	return array('ids' => $ids);
}

function ksk_debug($val, $label = null) {
    $file_name = ABSPATH . 'ksk_debug.log.html';
    if ($label != null) {
        $out_str = '<p>'.date('d.m.Y H:i:s').'&nbsp;&nbsp;'.$label.'<br/>';
    } else {
        $out_str = '<p>'.date('d.m.Y H:i:s').'<br/>';
    }
    
    if (is_array($val) || is_object($val)) {
        $out_str .= '<pre>'.print_r($val, true).'</pre>';
    } else {
        $out_str .= $val;
    }
    $out_str .= '</p>';
    
    file_put_contents($file_name, $out_str, FILE_APPEND | LOCK_EX);
}

?>