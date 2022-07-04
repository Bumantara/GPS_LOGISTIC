<?php
include "koneksi.php";
include_once 'guid.php'; 
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // get posted data
  $data = json_decode(file_get_contents("php://input"));
  date_default_timezone_set("Asia/Jakarta");
  $datetime =  date('Y-m-d H:i:s');
 

  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    $responseAPI=
      [
        "statusCode" => "401",
        "statusMessage" => "Unauthorized: Missing or invalid authentication token.",
        "timestamp" => "$datetime",
        "data" => "no data"
      ];
  } elseif ($_SERVER['PHP_AUTH_USER'] == "user" && $_SERVER['PHP_AUTH_PW'] == "user") {
    if(empty($data->timeFilterType)){ 
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "timeFilterType request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];
     
    }else{
      
      $guid = GUID();
      
      $responseAPI=[
        "statusCode" => "200",
        "statusMessage" => "Succeed",
        "timestamp" => "$datetime",
        "guid" => "$guid",
        "data" => []
      ];

      $data_detail = [
        'percentage' => '33.1',
        'timestamp' => '30',
        
        ];
      array_push( $responseAPI["data"],  $data_detail);

      $data_detail = [
        'percentage' => '26.7',
        'timestamp' => '32',
        
        ];
      array_push( $responseAPI["data"],  $data_detail);

      $data_detail = [
        'percentage' => '22.5',
        'timestamp' => '34',
        
        ];
      array_push( $responseAPI["data"],  $data_detail);

      $data_detail = [
        'percentage' => '62.1',
        'timestamp' => '38',
        
        ];
      array_push( $responseAPI["data"],  $data_detail);

      $data_detail = [
        'percentage' => '60.2',
        'timestamp' => '31',
        
        ];
      array_push( $responseAPI["data"],  $data_detail);

      $data_detail = [
        'percentage' => '50.6',
        'timestamp' => '36',
        
        ];
      array_push( $responseAPI["data"],  $data_detail);

    }
  }else{
    $responseAPI=
      [
        "statusCode" => "401",
        "statusMessage" => "Unauthorized: invalid username.",
        "timestamp" => "$datetime",
        "data" => "no data"
      ];
  }
  

  $data = json_encode($responseAPI);
  echo $data
?>