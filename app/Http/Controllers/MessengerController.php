<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessengerController extends Controller
{
    public function verifyWebhook()
    {
        $verify_token = env('MESSENGER_VERIFY_TOKEN');
        $token = request()->input('hub_verify_token');

        // hub
        $hub_challenge = request()->input('hub_challenge');
        $mode = request()->input('hub_mode');
        $hub_verify_token = request()->input('hub_verify_token');


        if ($token === $verify_token){
            return response($_GET['hub_challenge']);
        }
        return response('Forbidden', 403);
    }

    public function receiveMessage()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        $message = $input['entry'][0]['messaging'][0]['message']['text'];
        $this->sendMessage($sender, $message);
    }

    public function sendMessage($sender, $message)
    {
        $access_token = env('PAGE_ACCESS_TOKEN');

        $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";

        $jsonData = '{
            "recipient": {
                "id": "' . $sender . '"
            },
            "message": {
                "text": "' . $message . '"
            }
        }';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
