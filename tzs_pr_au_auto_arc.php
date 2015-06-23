<?php

/* 
 * Скрипт для автоматического переноса в архив товаров и тендеров,
 * у которых завершен срок публикации.
 */

/* 
 * Функция для отправки письма с вложенными файлами
 */
function ksk_sendMailAttachments($mail_to, $mail_from, $mail_subject, $mail_message, $mail_reply_to = '', $mail_attachments = array()) {
    if (($mail_to === null) || ($mail_to === '')) {
        return array(false, 'Не задана обязательный параметр $mail_to');
    }
    
    if (($mail_from === null) || ($mail_from === '')) {
        return array(false, 'Не задана обязательный параметр $mail_from');
    }
    
    if (($mail_subject === null) || ($mail_subject === '')) {
        return array(false, 'Не задана обязательный параметр $mail_subject');
    }
    
    if (($mail_message === null) || ($mail_message === '')) {
        return array(false, 'Не задана обязательный параметр $mail_message');
    }
    
    // Если на задан адрес Reply-To, то он совпадает с From
    if ($mail_reply_to === '') {
        $mail_reply_to = $mail_from;
    }
    
    $EOF = "\r\n";
    
    //Письмо с вложением состоит из нескольких частей, которые разделяются разделителем
    // Генерируем разделитель    
    $boundary = md5(uniqid(time()));
    
    // разделитель указывается в заголовке в параметре boundary
    $mailheaders = "MIME-Version: 1.0;".$EOF; 
    $mailheaders .= "Content-Type: multipart/mixed; boundary=$boundary".$EOF; 
    
    $mailheaders .= "From: $mail_from".$EOF; 
    $mailheaders .= "Reply-To: $mail_reply_to".$EOF; 
    
    // первая часть само сообщение    
    $multipart = "--$boundary".$EOF; 
    $multipart .= "Content-Type: text/plain; charset=UTF-8".$EOF;
    $multipart .= "Content-Transfer-Encoding: base64".$EOF;    
    $multipart .= $EOF;
    $multipart .= chunk_split(base64_encode($mail_message));
    
    // Цикл по кол-ву вложений
    for ($i=0;$i<count($mail_attachments);$i++) {
        // чтение файла        
        if (file_exists($mail_attachments[$i])) {
            $fp = fopen($mail_attachments[$i],"r"); 
            if (!$fp) { 
                return array(false, 'Не удается открыть файл '.$mail_attachments[$i]); 
            } 
            $file = fread($fp, filesize($mail_attachments[$i])); 
            fclose($fp);
            
            $fn = basename($mail_attachments[$i]);

            $message_part = "--$boundary".$EOF; 
            $message_part .= "Content-Type: application/octet-stream; name==?utf-8?B?".base64_encode($fn)."?=".$EOF;  
            $message_part .= "Content-Transfer-Encoding: base64".$EOF; 
            $message_part .= "Content-Disposition: attachment; filename==?utf-8?B?".base64_encode($fn)."?=".$EOF; 
            $message_part .= "".$EOF;
            $message_part .= chunk_split(base64_encode($file)) . $EOF;

            // второй частью прикрепляем файл, можно прикрепить два и более файла
            $multipart .= $message_part;
        }
    }
    
    $multipart .= "--$boundary--".$EOF; 
    
    // отправляем письмо 
    $result = mail($mail_to, $mail_subject, $multipart, $mailheaders);
    if ($result) {
        return array($result, 'Письмо отправлено успешно по адресу '.$mail_to); 
    } else {
        return array($result, 'Ошибка при отправке письма по адресу '.$mail_to); 
    }
}


/* 
 * Функция для отправки письма и завершения работы
 */
function ksk_sendMailAttachmentsAndExit($mail_to, $mail_from, $mail_subject, $mail_message, $mail_reply_to = '', $mail_attachments = array()) {
    echo $mail_message;
    $res = ksk_sendMailAttachments($mail_to, $mail_from, $mail_subject, $mail_message, $mail_reply_to, $mail_attachments);
    exit;
}


/* 
 * Функция для вывода результата запроса в CSV-файл 
 */
function ksk_saveQueryResultsToCSV($connection, $query, $csv_file) {
    $EOF = "\r\n";
    $Csv_Body = '';
    $csv_terminated = "\r\n";
    $csv_separator = ";";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
    
    if (!$connection) {
        return array(false, 'Отсутствует подклчение к серверу MySQL');
    }
    
    if ($query === '') {
        return array(false, 'Отсутствует текст SQL-запроса');
    }
    
    if ($csv_file === '') {
        return array(false, 'Отсутствует имя CSV-файла');
    }

    if (file_exists($csv_file)) {
        unlink($csv_file);
    }
    
    $cursor = mysqli_query($connection, $query);
    if (!$cursor) {
        return array(false, "Ошибка при выполнении запроса \"".$query."\" : ".  mysqli_error().$EOF);
    }
    
    // Получим информацию обо всех столбцах
    $field_info = mysqli_fetch_fields($cursor);
    
    $i = 0;
    $field_name = array();
    foreach ($field_info as $val) {
        if ($i > 0) {
            $Csv_Body .= $csv_separator;
        }
        $Csv_Body .= $csv_enclosed . $val->name . $csv_enclosed;
        $field_name[$i] = $val->name;
        $i++;
    }
    
    $Csv_Body .= $csv_terminated;
    
    while($row = mysqli_fetch_assoc($cursor)) {
        for($i=0;$i<count($field_name);$i++) {
            if ($i > 0) {
                $Csv_Body .= $csv_separator;
            }
            
            $Csv_Body .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$field_name[$i]]) . $csv_enclosed;
        }
        
        $Csv_Body .= $csv_terminated;
    }
    
    $Csv_Body_Conv = iconv("utf-8", "cp1251", $Csv_Body);
    
    $res = file_put_contents($csv_file, $Csv_Body_Conv, LOCK_EX);
    if (!$res) {
        return array(false, "Ошибка при записи в CSV-файл ".  $csv_file . $EOF);
    } else {
        return array(true, "Успешно создан CSV-файл ". $csv_file . $EOF);
    }
}

/*---------------------------------------------------------------
 * Основная часть скрипта
 *---------------------------------------------------------------
 */
//$time_zone = ini_get('date.timezone');
$time_zone = 'Europe/Kiev';
date_default_timezone_set($time_zone);

$EOF = "\r\n";
$admin_email = 'info@t3s.biz, serker72@gmail.com';
$from_email = 'info@t3s.biz';
$msg_subject = 'Transfer of the archive expired products and tenders - '.date('d.m.Y H:i:s');
$msg_body = '';

//sys_get_temp_dir()
//$csv_file_path = __DIR__.'//wp-content//uploads//'.date('Y').'//'.date('m').'//';
$csv_file_path = __DIR__ . '//';
$pr_csv_file = $csv_file_path.'pr_arch.csv';
$au_csv_file = $csv_file_path.'au_arch.csv';

// Удалим файлы
if (file_exists($pr_csv_file)) {
    unlink($pr_csv_file);
}

if (file_exists($au_csv_file)) {
    unlink($au_csv_file);
}

// Подключим файл wp-config.php для определния параметров подключения к БД
require_once 'wp-config.php';

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);

if (!$connection) {
    $msg_body .= "Ошибка подключения к БД: ".mysqli_error().$EOF;
    ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
}

if (!mysqli_select_db($connection, DB_NAME)) {
    $msg_body .= "Ошибка выбора БД ".DB_NAME.": ".  mysqli_error().$EOF;
    ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
} 

if (!mysqli_set_charset($connection, DB_CHARSET)) {
    $msg_body .= "Ошибка установки charset ".DB_CHARSET." для БД ".DB_NAME.": ". mysqli_error().$EOF;
    ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
}

// Товары
$query = "SELECT COUNT(*) as cnt FROM wp_tzs_products WHERE active=1 AND expiration IS NOT NULL AND expiration <= NOW();";
$cursor = mysqli_query($connection, $query);
if (!$cursor) {
    $msg_body .= "Ошибка при выполнении запроса \"".$query."\" : ".  mysqli_error().$EOF;
    ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
}

$row = mysqli_fetch_assoc($cursor);
$cnt = $row['cnt'];

$msg_body .= 'Количество товаров, у которых завершился срок публикации: '.$cnt.$EOF;

if ($cnt > 0) {
    // Сформируем табличку в csv-файле
    $query = "SELECT wptp.id, wptp.user_id, wu.user_login, wum.meta_value AS fio,
  wptp.type_id, wp.post_title, wptp.title, wptp.city_from,
  wptp.copies, wptp.price,
  CASE wptp.currency
    WHEN 1 THEN 'грн'
    WHEN 2 THEN 'грн/м.кв.'
    WHEN 3 THEN 'грн/м.куб.'
    WHEN 4 THEN 'грн/м.пог.'
    WHEN 5 THEN 'грн/кг'
    WHEN 6 THEN 'грн/т'
    WHEN 7 THEN 'грн/л'
    WHEN 8 THEN 'грн/ч'
    ELSE ''
  END AS t_currency,
  wptp.created, wptp.expiration
  FROM wp_tzs_products wptp, wp_users wu, wp_usermeta wum, wp_posts wp
  WHERE wptp.active = 1 
  AND wptp.expiration IS NOT NULL 
  AND wptp.expiration <= NOW()
  AND wu.ID = wptp.user_id
  AND wum.user_id = wptp.user_id
  AND wum.meta_key = 'fio'
  AND wp.ID = wptp.type_id
  ;";
/*
SELECT 'id', 'user_id', 'user_login', 'fio', 'type_id', 
        'type_title', 'title', 'city_from', 'copies', 'price', 'currency',
        'created', 'expiration'
UNION
(
   INTO OUTFILE '".addslashes($pr_csv_file)."'
  CHARACTER SET 'cp1251'
  FIELDS TERMINATED BY ';'
  ENCLOSED BY '\"' ESCAPED BY '".addslashes('\\')."'
  LINES STARTING BY '' TERMINATED BY '\r\n'
)
 */    
/*    $cursor = mysqli_query($connection, $query);
    if (!$cursor) {
        $msg_body .= "Ошибка при выполнении запроса \"".$query."\" : ".  mysqli_error().$EOF;
        ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
    }*/
    $res = ksk_saveQueryResultsToCSV($connection, $query, $pr_csv_file);
    $msg_body .= $res[1] . $EOF;
    if (!$res[0]) {
        ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
    }
    
    // Обновим записи
    $query = "UPDATE wp_tzs_products SET active=0, last_edited=now() WHERE active=1 AND expiration IS NOT NULL AND expiration <= NOW();";
    $cursor = mysqli_query($connection, $query);
    if (!$cursor) {
        $msg_body .= "Ошибка при выполнении запроса \"".$query."\" : ".  mysqli_error().$EOF;
        ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
    }
}


/*/ Тендеры
$query = "SELECT COUNT(*) as cnt FROM wp_tzs_auctions WHERE active=1 AND expiration IS NOT NULL AND expiration <= NOW();";
$cursor = mysqli_query($connection, $query);
if (!$cursor) {
    $msg_body .= "Ошибка при выполнении запроса \"".$query."\" : ".  mysqli_error().$EOF;
    ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
}

$row = mysqli_fetch_assoc($cursor);
$cnt = $row['cnt'];

$msg_body .= 'Количество тендеров, у которых завершился срок публикации: '.$cnt.$EOF;

if ($cnt > 0) {
    // Сформируем табличку в csv-файле
    $query = "SELECT wptp.id, wptp.user_id, wu.user_login, wum.meta_value AS fio,
  wptp.type_id, wp.post_title, wptp.title, wptp.city_from,
  wptp.copies, wptp.price,
  CASE wptp.currency
    WHEN 1 THEN 'грн'
    WHEN 2 THEN 'грн/м.кв.'
    WHEN 3 THEN 'грн/м.куб.'
    WHEN 4 THEN 'грн/м.пог.'
    WHEN 5 THEN 'грн/кг'
    WHEN 6 THEN 'грн/т'
    WHEN 7 THEN 'грн/л'
    WHEN 8 THEN 'грн/ч'
    ELSE ''
  END AS t_currency,
  (SELECT MAX(wtar.rate) 
     FROM  wp_tzs_auction_rates wtar 
     WHERE wtar.auction_id = wptp.id 
       AND wtar.active = 1 
       AND wtar.reviewed IS NULL) as max_rate,
  wptp.created, wptp.expiration
  FROM wp_tzs_auctions wptp, wp_users wu, wp_usermeta wum, wp_posts wp
  WHERE wptp.active = 1 
  AND wptp.expiration IS NOT NULL 
  AND wptp.expiration <= NOW()
  AND wu.ID = wptp.user_id
  AND wum.user_id = wptp.user_id
  AND wum.meta_key = 'fio'
  AND wp.ID = wptp.type_id
;";
    
    $res = ksk_saveQueryResultsToCSV($connection, $query, $au_csv_file);
    $msg_body .= $res[1] . $EOF;
    if (!$res[0]) {
        ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
    }
    
    
    // Обновим записи
    $query = "UPDATE wp_tzs_auctions SET active=0, last_edited=now() WHERE active=1 AND expiration IS NOT NULL AND expiration <= NOW();";
    $cursor = mysqli_query($connection, $query);
    if (!$cursor) {
        $msg_body .= "Ошибка при выполнении запроса \"".$query."\" : ".  mysqli_error().$EOF;
        ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body);
    }
}
*/
//ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body, '', array($pr_csv_file, $au_csv_file));
ksk_sendMailAttachmentsAndExit($admin_email, $from_email, $msg_subject, $msg_body, '', array($pr_csv_file));
