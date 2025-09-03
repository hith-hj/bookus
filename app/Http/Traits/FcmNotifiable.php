<?php

namespace App\Http\Traits;

trait FcmNotifiable {

    public function sendToTopic($title, $body, $token,$type='other',$id=null){
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $subData = [
            'title' => $title,
            'content' => $body,
            'ar_title' => $title,
            'ar_content' => $body,
            'payload' => ['type'=>$type,'id'=>$id]
        ];

        $data = [
            'data' => $subData,
            'notification' => $subData
        ];

        $notification = [
            'body' => $body,
            'title' => $title
        ];

        if(is_array($token)){
            $reciver = [];
            foreach($token as $userFCM){
                $reciver[] = $userFCM;   
            }
        }else{
            $reciver = $token;
        }
        
        $fcmNotification = [
            'to' => $reciver,
            'data' => $data,
            'notification' => $notification,
            ];
    
        $headers = [
            'Authorization: key=' . config('firebase.FCM_SERVER_KEY'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

}
