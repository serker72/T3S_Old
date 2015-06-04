<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo '<pre>';

// T3S.SU
//$connection = mysql_connect('localhost', 'root', '');
//$db_name = "t3s";

// T3S.VIDEO
//$connection = mysql_connect('93.127.226.130', 'admin', 'prog');
//$db_name = "t3s";

// T3S.BIZ
$connection = mysql_connect('31.28.169.230', 'gilatrade_t3s', 'agwg856qd!@57');
$db_name = "gilatrade_t3s";

if (!$connection) {
    die("Ошибка подключения к БД: ".  mysql_error());
}

if (!mysql_select_db($db_name, $connection)) {
    die("Ошибка выбора БД ".$db_name.": ".  mysql_error());
} 
if (!mysql_set_charset('utf8', $connection)) {
    die("Ошибка установки charset utf8 для БД ".$db_name.": ".  mysql_error());
}

$query = "UPDATE wp_tzs_cities SET lat = NULL, lng = NULL WHERE id IS NOT NULL";
$cursor = mysql_query($query, $connection);
if (!$cursor) {
    echo "Обнуление координат - Ошибка: ".  mysql_error().'<br/>';
}
else {
    echo 'Обнуление координат - OK<br/>';
}

$query = "SELECT * FROM wp_tzs_cities WHERE country_id > 0 AND city_id > 0 AND (lat IS NULL OR lng is NULL)";
echo 'query = '.$query.'<br/>';

$cursor = mysql_query($query, $connection);
if (!$cursor) {
    die("Ошибка выполнения запроса ".$query." : ".  mysql_error());
}

$upd_array = array();
$key = "dj0yJmk9emc1SWFXZnJ2UUxoJmQ9WVdrOWFESm1abkprTjJVbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1kNQ";

while($row = mysql_fetch_assoc($cursor)) {
    $id = $row['city_id'];
    $name = $row['title_ru'];
    
    echo $id.'-'.$name.'-';
    
    $url = "http://where.yahooapis.com/v1/place/$id?format=json&lang=ru&appid=$key";
	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result=curl_exec($ch);
    curl_close($ch);
	
    $res = json_decode($result, true);
	
    if (isset($res["error"])) {
        echo '- Error: '.$res["error"].'<br/>';
    }
    else if (!isset($res["place"])) {
        echo '- Error: NOT PLACE<br/>';
    }
	
    $rec = $res["place"];
    
    $lat = isset($rec["centroid"]) && isset($rec["centroid"]["latitude"])? $rec["centroid"]["latitude"] : NULL;
    $lng = isset($rec["centroid"]) && isset($rec["centroid"]["longitude"])? $rec["centroid"]["longitude"] : NULL;

    echo ' OK: lat='.$lat.', lng='.$lng.'<br/>';
    
    //$upd_array .= array($row['id'], $lat, $lng);
    
    //$query = "UPDATE wp_tzs_cities SET lat = ".$lat.", lng = ".$lng." WHERE id = ".$row['id'];
    $query = "UPDATE wp_tzs_cities SET lat = ".  sprintf("%.6F", $lat).", lng = ".sprintf("%.6F", $lng)." WHERE id = ".$row['id'];
    $upd_array[$row['id']] = $query;
}

print_r($upd_array);

$count = 0;
foreach ($upd_array as $key => $query) {
    echo $key.' - '.$query;
    $cursor = mysql_query($query, $connection);
    if (!$cursor) {
        echo " - Ошибка: ".  mysql_error().'<br/>';
    }
    else {
        echo ' - OK<br/>';
        $count++;
        if ($count > 0) {
            //break;
        }
    }
}
echo '</pre>';
