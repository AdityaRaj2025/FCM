<?php
require_once 'vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FCM {
    private $url = 'https://fcm.googleapis.com/fcm/send';
    private $apiKey = 'AAAAmvk1uaQ:APA91bEWp8FLbC3Gm3qeNS7FJQXlHCJqTeWuLrERo4G5GNG_vRHRIyK4zI3-XxxWnV_a8DNgC1KfTNKFSqqmSu1IYktzD3JvUdK2ekh-NM4K4VgcwEQsgy52uxvv5_fUgQdCmEpzABHN'; // This apikey is from Raj Google account
    
    public function sendFCM($notifMsg, $tokens) {
        $headers = array(
            'Authorization:key=' . $this->apiKey,
            'Content-Type:application/json'
        );
        
        $notifData = [
            'title' => 'Notification',
            'body' => $notifMsg,
            'click_action' => 'activities.NotifHandlerActivity'
        ];
        
        $dataPayload = [
            'to' => 'My Name',
            'points' => 80,
            'other_data' => 'This is extra payload'
        ];
        
        $apiBody = [
            'notification' => $notifData,
            'data' => $dataPayload,
            'time_to_live' => 600,
            'registration_ids' => $tokens // Use the tokens passed as an argument
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiBody));
        
        $result = curl_exec($ch);
        
        $logger = new Logger('FCM');
        $logFile = 'C:\log5.txt';
        $logger->pushHandler(new StreamHandler($logFile, Logger::INFO));
        
        $logger->info('Response: ' . $result);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP response code  HTTPレスポンスコードを取得する
        $response = json_decode($result, true); // Parse the JSON response  JSON 応答を解析する
         
        curl_close($ch);
        
        if ($httpCode === 200 && isset($response['success'])) {
            // The push notification was successfully sent  プッシュ通知が正常に送信されました
            return array('success' => $response['success']);
        } else {
            // There was an error sending the push notification  プッシュ通知の送信でエラーが発生しました
            return array('error' => $response);
        }
    }
}