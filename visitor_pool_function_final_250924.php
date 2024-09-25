<?php
//Скрипт записывает даныые каждого посетителя с учетом большого кол-ва запросов и ограниченных ресурсов бд/сети
date_default_timezone_set('Europe/Moscow');
$date_time = date('Y-m-d H:i:s');
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host_server = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$current_url = $protocol . '://' . $host_server . $request_uri;
$roistat_param_company = isset($_COOKIE['roistat_param_company']) ? $_COOKIE['roistat_param_company'] : '';
$visitors_info = isset($_COOKIE['visitorsInfo']) ? $_COOKIE['visitorsInfo'] : '';
$_ym_uid = isset($_COOKIE['_ym_uid']) ? $_COOKIE['_ym_uid'] : '';
$_ga = isset($_COOKIE['_ga']) ? $_COOKIE['_ga'] : '';
$screen_width = isset($_COOKIE['screen_width']) ? $_COOKIE['screen_width'] : '';
$roistat_mail = isset($_COOKIE['roistat_mail']) ? $_COOKIE['roistat_mail'] : '';
$FINGERPRINT_ID = isset($_COOKIE['FINGERPRINT_ID']) ? $_COOKIE['FINGERPRINT_ID'] : '';
$remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
function getClientIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $clientIP = trim($ips[0]);
    } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $clientIP = $_SERVER['HTTP_X_REAL_IP'];
    } else {
        $clientIP = $_SERVER['REMOTE_ADDR'];
    }
    return $clientIP;
}
$ip_address = getClientIP();

$file_sql = 'file.sql';
$file_counter = 'file_counter.txt';
$db_table = 'visitor_data_pool_test';

file_put_contents($file_counter, 'IP, ', FILE_APPEND);

if (file_exists($file_sql)) {
    $content = file_get_contents($file_sql);
    $string_count = substr_count($content, '),');
}

$insert_string = "('$ip_address', '$roistat_param_company', '$referrer', '$current_url', '$_ga', '$_ym_uid', '$visitors_info', '$remote_addr', '$request_uri', '$screen_width', '$date_time', '$roistat_mail', '$FINGERPRINT_ID', '$http_user_agent'),\n";

if (!file_exists($file_sql) || filesize($file_sql) == 0) {
    file_put_contents($file_sql, "INSERT INTO `$db_table` (ip, roistat_param_company, referrer, current_url, _ga, _ym_uid, visitors_info, remote_addr, request_uri, screen_width, date_time, roistat_mail, FINGERPRINT_ID, http_user_agent) VALUES\n");
    file_put_contents($file_sql, $insert_string, FILE_APPEND);
} else {
    file_put_contents($file_sql, $insert_string, FILE_APPEND);

    if ($string_count > 99) {
        $mysql_insert = file_get_contents($file_sql);

        file_put_contents($file_sql, '');

        $lastPos = strrpos($mysql_insert, '),');
        $update_insert = substr_replace($mysql_insert, ');', $lastPos, 2);

        $host = '';
        $db   = '';
        $user = '';
        $pass = '';
        $charset = 'utf8';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        );

        try {
            $pdo = new PDO($dsn, $user, $pass, $opt);
            $stmt = $pdo->prepare($update_insert);
            $stmt->execute();
        } catch (PDOException $e) {
            $logMessage = $date_time . ' ' . $e->getMessage() . "\n";
            file_put_contents('error.log', $logMessage, FILE_APPEND);
            file_put_contents('file_err_string.log', $update_insert . "\n", FILE_APPEND);
        }
    }
}
