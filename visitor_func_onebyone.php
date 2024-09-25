<?php
// Запись данных каждого посетителя в бд, первая версия. 

$sql_to_visinfo = "INSERT INTO visitor_data (ip, roistat_param_company, referrer, current_url, _ga, _ym_uid, visitors_info, remote_addr, request_uri, screen_width, date_time, roistat_mail, FINGERPRINT_ID, http_user_agent)
        VALUES (:ip, :roistat_param_company, :referrer, :current_url, :_ga, :_ym_uid, :visitors_info, :remote_addr, :request_uri, :screen_width, :date_time, :roistat_mail, :FINGERPRINT_ID, :http_user_agent)";
try {
    $pdo = new PDO($dsn, $user, $pass, $opt);
    $stmt = $pdo->prepare($sql_to_visinfo);
    $stmt->bindValue(':ip', $ip_address);
    $stmt->bindValue(':roistat_param_company', $roistat_param_company);
    $stmt->bindValue(':referrer', $referrer);
    $stmt->bindValue(':current_url', $current_url);
    $stmt->bindValue(':_ga', $_ga);
    $stmt->bindValue(':_ym_uid', $_ym_uid);
    $stmt->bindValue(':visitors_info', $visitors_info);
    $stmt->bindValue(':remote_addr', $remote_addr);
    $stmt->bindValue(':request_uri', $request_uri);
    $stmt->bindValue(':screen_width', $screen_width);
    $stmt->bindValue(':date_time', $date_time);
    $stmt->bindValue(':roistat_mail', $roistat_mail);
    $stmt->bindValue(':FINGERPRINT_ID', $FINGERPRINT_ID);
    $stmt->bindValue(':http_user_agent', $http_user_agent);
    $stmt->execute();
} catch (PDOException $e) {
    $logMessage = $date_time . ' ' . $e->getMessage() . "\n";
    file_put_contents('error.log', $logMessage, FILE_APPEND);
}
