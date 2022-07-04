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
    if(empty($data->supplier_lorry_id)){ 
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "supplier_lorry_id request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];
    }elseif (empty($data->remark_description)) {
      $responseAPI=[
        "statusCode" => "404",
        "statusMessage" => "remark_description request Not Found: The requested resource could not be found.",
        "timestamp" => "$datetime"
      ];      
    }else{
      $remark_description = $data->remark_description;
      $guid = GUID();
      $supplier_lorry_id = $data->supplier_lorry_id;
      $q_update_supplier_lorry = "UPDATE supplier_lorry SET remark_description = '$remark_description' WHERE supplier_lorry_id  = '$supplier_lorry_id'";
      $conn->exec($q_update_supplier_lorry);

      $responseAPI=[
        "statusCode" => "200",
        "statusMessage" => "Succeed",
        "timestamp" => "$datetime",
        "guid" => "$guid",
        "data" => [
            "remark_description" => "$remark_description",
            "supplier_lorry_id" => "$supplier_lorry_id"
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