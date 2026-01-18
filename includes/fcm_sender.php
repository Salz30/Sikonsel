<?php
function sendPushNotification($targetToken, $title, $body) {
    // GANTI DENGAN SERVER KEY FIREBASE ANDA (Dari Firebase Console)
    $serverKey = 'BN0GXngX-4BqunJrWSJmVkT3wsMdiqfgHjKD-xumqmjSmCcitmHcQ_fXJzryKnCqxS_jmKkZHDl9i5Dc-GGbeSE'; 
    
    $url = "https://fcm.googleapis.com/fcm/send";
    
    $notification = [
        'title' => $title,
        'body' => $body,
        'sound' => 'default',
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    ];
    
    $arrayToSend = [
        'to' => $targetToken,
        'notification' => $notification,
        'priority' => 'high'
    ];
    
    $json = json_encode($arrayToSend);
    $headers = [
        'Content-Type: application/json',
        'Authorization: key=' . $serverKey
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
?>