<?php
include "koneksi.php";
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
    $responseAPI=
      [
        "statusCode" => "200",
        "statusMessage" => "succeed.",
        "timestamp" => "$datetime",
        "data" => [],
      ];

   $q_find_gps_current_loc = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000001' OR manual_location_id = 'P0000001') AND need_monitor = '1'";
          $results = $conn->query($q_find_gps_current_loc);
          $number_entrance = $results->rowCount();
    $data_detail = [
      'locationName' => 'Waiting Area',
      'IotAvailability' => 1,
      'IotTotal' => $number_entrance
    ];
    array_push( $responseAPI["data"],  $data_detail);


    $q_find_gps_current_loc = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000002' OR manual_location_id = 'P0000002') AND need_monitor = '1'";
          $results = $conn->query($q_find_gps_current_loc);
          $number_entrance = $results->rowCount();
    $data_detail = [
      'locationName' => 'B3-Garment Care',
      'IotAvailability' => 2,
      'IotTotal' => $number_entrance
    ];
    array_push( $responseAPI["data"],  $data_detail);


    $q_find_gps_current_loc = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000003' OR manual_location_id = 'P0000003') AND need_monitor = '1'";
          $results = $conn->query($q_find_gps_current_loc);
          $number_entrance = $results->rowCount();
    $data_detail = [
      'locationName' => 'MCC-Garment Care',
      'IotAvailability' => 1,
      'IotTotal' => $number_entrance
    ];
    array_push( $responseAPI["data"],  $data_detail);


    $q_find_gps_current_loc = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000004' OR manual_location_id = 'P0000004') AND need_monitor = '1'";
          $results = $conn->query($q_find_gps_current_loc);
          $number_entrance = $results->rowCount();
     $data_detail = [
      'locationName' => 'B2-Garment Care',
      'IotAvailability' => 2,
      'IotTotal' => $number_entrance
    ];
    array_push( $responseAPI["data"],  $data_detail);


   $q_find_gps_current_loc = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000005' OR manual_location_id = 'P0000005') AND need_monitor = '1'";
        $results = $conn->query($q_find_gps_current_loc);
        $number_entrance = $results->rowCount();
    $data_detail = [
      'locationName' => 'B4-Garment Care',
      'IotAvailability' => 2,
      'IotTotal' => $number_entrance
       ];
    array_push( $responseAPI["data"],  $data_detail);


   $q_find_gps_current_loc = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000008' OR manual_location_id = 'P0000008') AND need_monitor = '1'";
        $results = $conn->query($q_find_gps_current_loc);
        $number_entrance = $results->rowCount();
    $data_detail = [
      'locationName' => 'Lot 6',
      'IotAvailability' => 4,
      'IotTotal' => $number_entrance
       ];
    array_push( $responseAPI["data"],  $data_detail);


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