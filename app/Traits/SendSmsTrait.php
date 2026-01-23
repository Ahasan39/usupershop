<?php
namespace App\Traits;
use App\Models\Sms;

trait SendSmsTrait{
    public function send_rapid_message($mobile = '', $message = '')
    {
        $sms = Sms::first();
        $apiUrl = "https://api.mimsms.com/api/SmsSending/SMS";
        $apiKey = $sms->apiKey; // Replace with your API Key
        $userName = $sms->userName; // Replace with your MiMSMS username
        $sender = $sms->SenderName;; // Sender name (from MiMSMS)

        $postData = [
            "UserName"       => $userName,
            "Apikey"         => $apiKey,
            "MobileNumber"   => $mobile,
            "CampaignId"     => "null",
            "SenderName"     => $sender,
            "TransactionType" => "T",
            "Message"        => $message
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        // Execute the request
        $response = curl_exec($ch);
        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['status' => 'error', 'message' => $error_msg];
        }
        // Close cURL session
        curl_close($ch);
        // Decode JSON response
        return json_decode($response, true);
    }
}