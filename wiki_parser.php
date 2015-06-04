<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
$ru_url = urlencode ('Города_Украины');
$html = file_get_contents('http://ru.wikipedia.org/wiki/'.$ru_url);
//$pattern = '|<h2><span class="mw-headline" id=".D0.[A-Z0-9]{2}">[А-Я]</span></h2>|i';
$pattern = '|<h2><span class="mw-headline" id=".D0.[A-Z0-9]{2}">(.*)|i';
preg_match_all ($pattern, $html, $res, PREG_SET_ORDER);
echo count($res);
print_r($res[0]);
//print_r($res);
*/

/*
 * Днепропетровск 
 * Широта 48°27′53″N (48.464717)
 * Долгота 35°2′46″E (35.046181)
 * 
 * Днепродзержинск
 * Широта 48°31′23″N (48.523116)
 * Долгота 34°36′49″E (34.613678)
 */
//$url = "http://geocode-maps.yandex.ru/1.x/?geocode=48.45,34.9833333&sco=latlong&kind=locality&format=json&results=1000&spn=0.552069,0.400552&rspn=1";
$url = "http://geocode-maps.yandex.ru/1.x/?geocode=48.45,34.9833333&sco=latlong&kind=locality&spn=0.652069,0.600552&rspn=1&format=json&results=200";
//echo $url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result=curl_exec($ch);
	curl_close($ch);
	
	$res = json_decode($result, true);
        
	if (isset($res["error"])) {
		print_r(array("error" => $res["error"]["description"]));
	}
echo "<pre>";
echo count($res)."<br/>";
echo count($res["response"]["GeoObjectCollection"]["featureMember"])."<br/>";
//print_r($res["response"]["GeoObjectCollection"]["featureMember"]);

for($i=0;$i < count($res["response"]["GeoObjectCollection"]["featureMember"]);$i++) {
    $a = $res["response"]["GeoObjectCollection"]["featureMember"][$i]["GeoObject"];
    print_r(array($a["name"], $a["Point"]));
    //print_r($a["Point"]);
}
echo "</pre>";
