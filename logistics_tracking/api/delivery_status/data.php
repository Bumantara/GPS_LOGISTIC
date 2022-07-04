<?php
echo "";
include_once 'guid.php';
include "koneksi.php"; 
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // get posted data
  $data = json_decode(file_get_contents("php://input"));
  date_default_timezone_set('Asia/Jakarta');
  $datetime =  date('Y-m-d H:i:s');
  $currentdate = date("Y-m-d");

  $hour = date("H", strtotime($datetime));
  $start_hour = sprintf('%02d', floor($hour / 24) * 1) ;
  $start_window = " $start_hour:07:00";
  $end_window = date("H:07:00", strtotime("$start_window + 24 Hours"));
  $guid = GUID();

  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    $responseAPI=
      [
        "statusCode" => "401",
        "statusMessage" => "Unauthorized: Missing or invalid authentication token.",
        "timestamp" => "$datetime",
        "data" => "no data",
        "CheckpointId" =>"",
        "Count_on_time" =>"",
        "Count_late" => ""
      ];
  } elseif ($_SERVER['PHP_AUTH_USER'] == "user" && $_SERVER['PHP_AUTH_PW'] == "user") {
      $responseAPI=
        [
          "statusCode" => "200",
          "statusMessage" => "succeed.",
          "timestamp" => "$datetime",
          "guid" => "$guid",
          "data" => array(),
        ];


        /* This code filters for philips based checkpoints and ommits the Philips truck used for internal deliveries*/ 
      $q_find_deliverystatus = "SELECT geofence_checkpoint_id,
      geofence_checkpoint_description ,
      DATE(time_entrance) as DATE_IN,
      TIME(time_entrance) as TIME_IN,
        COUNT(*) AS total,
        SUM(CASE WHEN `on_time` = 'On Time' AND duration_actual > 180 then 1 else 0 end) AS 'on_time_by_checkpoint', 
        SUM(CASE WHEN `on_time` != 'Not On Time' AND duration_actual > 180 then 1 else 0 end) AS 'Notontime_by_checkpoint'
        FROM incoming_lorry_gps.records_checkpoints
        WHERE DATE(time_entrance) >= '" . $currentdate . "' AND TIME(time_entrance) > '". $start_window ."' and TIME(time_entrance) <= '" .$end_window ."' AND remark <> 'System Issue' AND  (geofence_checkpoint_id = 'P0000002' OR  geofence_checkpoint_id = 'P0000003') AND supplier_id <> 'PHI000001' AND supplier_id <> 'P00000001' and supplier_id <> 'LSP000001' 
        GROUP BY geofence_checkpoint_id";


      foreach ($conn->query($q_find_deliverystatus) as $dt_Checkpoint) {
        $geofence_checkpoint_id = $dt_Checkpoint["geofence_checkpoint_id"];
        $on_time_by_checkpoint = $dt_Checkpoint["on_time_by_checkpoint"];
        $Notontime_by_checkpoint = $dt_Checkpoint["Notontime_by_checkpoint"];
          if ($geofence_checkpoint_id == 'P0000002'){
            $Business_group = 'MG';
              }
          else if ($geofence_checkpoint_id == 'P0000003'){
            $Business_group = 'MCC';
              }

        $data_detail = [
          "Business_group" => $Business_group,
          "Count_on_time" => $on_time_by_checkpoint,
          "Count_late" => $Notontime_by_checkpoint
        ];
        array_push( $responseAPI["data"],  $data_detail);
      } 

      $q_find_deliverystatus = "SELECT geofence_checkpoint_id,
      geofence_checkpoint_description ,
      DATE(time_entrance) as DATE_IN,
      TIME(time_entrance) as TIME_IN,
        COUNT(*) AS total,
        SUM(CASE WHEN `on_time` = 'On Time' AND duration_actual > 180 then 1 else 0 end) AS 'on_time_by_checkpoint', 
        SUM(CASE WHEN `on_time` != 'Not On Time' AND duration_actual > 180 then 1 else 0 end) AS 'Notontime_by_checkpoint'
        FROM incoming_lorry_gps.records_checkpoints
        WHERE DATE(time_entrance) >= '" . $currentdate . "' AND TIME(time_entrance) > '". $start_window ."' and TIME(time_entrance) <= '" .$end_window ."' AND remark <> 'System Issue' AND  (geofence_checkpoint_id = 'P0000004' OR  geofence_checkpoint_id = 'P0000005' OR  geofence_checkpoint_id = 'P0000008') AND supplier_id <> 'PHI000001' AND supplier_id <> 'P00000001' and supplier_id <> 'LSP000001' ";
  
        foreach ($conn->query($q_find_deliverystatus) as $dt_Checkpoint) {
        $on_time_by_checkpoint = $dt_Checkpoint["on_time_by_checkpoint"];
        $Notontime_by_checkpoint = $dt_Checkpoint["Notontime_by_checkpoint"];

        $data_detail = [
          "Business_group" => 'GC',
          "Count_on_time" => $on_time_by_checkpoint,
          "Count_late" => $Notontime_by_checkpoint
        ];
        array_push( $responseAPI["data"],  $data_detail); 
      }

      $q_find_deliverystatus = "SELECT geofence_checkpoint_id,
      geofence_checkpoint_description ,
      DATE(time_entrance) as DATE_IN,
      TIME(time_entrance) as TIME_IN,
        COUNT(*) AS total,
        SUM(CASE WHEN `on_time` = 'On Time' AND duration_actual > 180 then 1 else 0 end) AS 'on_time_by_checkpoint', 
        SUM(CASE WHEN `on_time` != 'Not On Time' AND duration_actual > 180 then 1 else 0 end) AS 'Notontime_by_checkpoint'
        FROM incoming_lorry_gps.records_checkpoints
        WHERE DATE(time_entrance) >= '" . $currentdate . "' AND TIME(time_entrance) > '". $start_window ."' and TIME(time_entrance) <= '" .$end_window ."' AND remark <> 'System Issue' AND  geofence_checkpoint_id = 'P0000005' AND (supplier_id = '100175243' OR supplier_id = '100175260' OR supplier_id = 'AME00001' OR supplier_id = '100175357')
        GROUP BY geofence_checkpoint_id"; /* */

        foreach ($conn->query($q_find_deliverystatus) as $dt_Checkpoint) {
        $on_time_by_checkpoint = $dt_Checkpoint["on_time_by_checkpoint"];
        $Notontime_by_checkpoint = $dt_Checkpoint["Notontime_by_checkpoint"];

        $data_detail = [
          "Business_group" => 'OHC',
          "Count_on_time" => $on_time_by_checkpoint,
          "Count_late" => $Notontime_by_checkpoint
        ];
        array_push( $responseAPI["data"],  $data_detail); 
      }

  }else{
    $responseAPI=
      [
        "statusCode" => "401",
        "statusMessage" => "Unauthorized: invalid username.",
        "timestamp" => "$datetime",
        "data" => "no data",
         "locationNAme" =>"",
        "IoAvailability" =>"",
        "IotTotal" => ""
      ];
  }
  
  $data = json_encode($responseAPI);
  echo $data
?>
