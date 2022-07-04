<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/rest_api/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "timeFilterLength":"T000101"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic dXNlcjp1c2Vy',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
echo $response;

$data_status = json_decode($response);
$data_get = $data_status->timestamp;

echo "you get timestamp : $data_get</br";

?>




