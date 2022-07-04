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
    if(empty($data->Latitude_1)){ 
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Latitude_1 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];
    }elseif (empty($data->Longtitude_1)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Longtitude_1 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];      

     }elseif (empty($data->Latitude_2)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Latitude_2 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];   

       }elseif (empty($data->Longtitude_2)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Longtitude_2 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];   

       }elseif (empty($data->Latitude_3)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Latitude_3 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];   

       }elseif (empty($data->Longtitude_3)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Longtitude_3 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];   

       }elseif (empty($data->Latitude_4)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Latitude_4 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];   

       }elseif (empty($data->Longtitude_4)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "Longtitude_4 request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];   
    }else{
      
      $guid = GUID();
      $responseAPI=[
        "statusCode" => "200",
        "statusMessage" => "Succeed",
        "timestamp" => "$datetime",
        "guid" => "$guid",
        "data" => [
            "supplier_lorry_id" => "100004384723525",
            "Supplier_name" => "Accord",
            "Vehicle_Number" => "BP 8781 ZD",
            "Latitude" => "1.77878",
            "Longitude" => "104.123123",
          ]
      ];
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