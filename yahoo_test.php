<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$key = "dj0yJmk9emc1SWFXZnJ2UUxoJmQ9WVdrOWFESm1abkprTjJVbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1kNQ";

    $url = "http://where.yahooapis.com/v1/places.q('".urlencode('Парутино Украина')."')?format=json&lang=ru&appid=$key";
	
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

    echo '<pre>';
    print_r($res);
    echo '</pre>';
    