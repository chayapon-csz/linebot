<?php
$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = '6UKG7kGjLg/PT/ofHS/AQ/TC76xqLc8nx9J6I0Z8YmTn+yrWmXJBvlc3okxcDqYhabfeyvEK0jPRdD8QI8nMdE46aBHWhb2pO7GPM3cXLmB81HERRtCXGgfaGKzfPB/U3nJl/9cmBiM2G5QAv/wMEwdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);
$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

//Write access log
$file = 'access.log';
$current = file_get_contents($file);
$current .= $request . "\n";
file_put_contents($file, $current);

//If event not null ==> For loop send back all message ==> check type if message (will echo same message back) .. if not --> will resend message type back ..
if ( sizeof($request_array['events']) > 0 )
{
 foreach ($request_array['events'] as $event)
 {
  $reply_message = '';
  $reply_token = $event['replyToken'];
  if ( $event['type'] == 'message' ) 
  {
   //If check status Kplus
   if( $event['message']['text'] == 'Server status (Kplus)' ) {
    $reply_message = 'Server status (Kplus) = FE_Mercury_native down ! please call INC!';
    $message = [['type' => 'text', 'text' => $reply_message],['type' => 'image', 'originalContentUrl' => 'https://384uqqh5pka2ma24ild282mv-wpengine.netdna-ssl.com/wp-content/uploads/2015/04/logstash-dashboard.png', 'previewImageUrl' => 'https://384uqqh5pka2ma24ild282mv-wpengine.netdna-ssl.com/wp-content/uploads/2015/04/logstash-dashboard.png']];
   } else if( $event['message']['text'] == 'Server status (S1)' ) {
    $reply_message = 'Server status (S1) = no server down';
    $message = [['type' => 'text', 'text' => $reply_message],['type' => 'image', 'originalContentUrl' => 'https://www.elastic.co/assets/bltc02a4b0eadbc0ed1/kibana-timeseries.jpg', 'previewImageUrl' => 'https://www.elastic.co/assets/bltc02a4b0eadbc0ed1/kibana-timeseries.jpg']];
   } else {
    $reply_message = 'ระบบไม่เข้าใจคำสั่งของคุณครับ';
    $message = [['type' => 'text', 'text' => $reply_message]];
   }
  }
 
  if( strlen($reply_message) > 0 )
  {
   //$reply_message = iconv("tis-620","utf-8",$reply_message);
   $data = [
    'replyToken' => $reply_token,
    'messages' => $message
   ];
   $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);
   $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
   echo "Result: ".$send_result."\r\n";
  }
 }
}

//Test call webhook
echo "OK";

function send_reply_message($url, $post_header, $post_body)
{
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 $result = curl_exec($ch);
 curl_close($ch);
 return $result;
}
?>
