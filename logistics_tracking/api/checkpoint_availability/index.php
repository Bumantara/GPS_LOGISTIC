<?php
  include "../data_connection.php";
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
        "data" => array(),
      ];

      /* This code filters for philips based checkpoints and ommits the Philips truck used for internal deliveries*/ 
    $q_find_gps_current_loc = "SELECT geofence_checkpoint_id, geofence_checkpoint_description, 
      availability, checkpoint_order, count(supplier_lorry_id) as lot_total 
      FROM incoming_lorry_gps.supplier_lorry RIGHT JOIN incoming_lorry_gps.geofence_checkpoint 
      ON incoming_lorry_gps.supplier_lorry.current_position_id  = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
      and incoming_lorry_gps.supplier_lorry.need_monitor != 0
      WHERE geofence_main_id LIKE 'PHI%' AND checkpoint_order IS NOT NULL group by geofence_checkpoint_id 
      ORDER BY checkpoint_order"; /* and condition filters a single table, filtering after the where filters after the table*/


    foreach ($conn->query($q_find_gps_current_loc) as $dt_Checkpoint) {
      $location_name = $dt_Checkpoint["geofence_checkpoint_description"];
      $availability = $dt_Checkpoint["availability"];
      $lot_total = $dt_Checkpoint["lot_total"];

      $data_detail = [
        "locationName" => "$location_name",
        "IotAvailability" => $availability,
        "IotTotal" => $lot_total
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