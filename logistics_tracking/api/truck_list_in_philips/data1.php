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

      $q_find_gps_philips = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000001' OR current_position_id = 'P0000002' OR current_position_id = 'P0000003' OR current_position_id = 'P0000004' OR current_position_id = 'P0000005' OR current_position_id = 'P0000008' OR manual_location_id = 'P0000001' OR manual_location_id = 'P0000002' OR manual_location_id = 'P0000003' OR manual_location_id = 'P0000004'  OR manual_location_id = 'P0000005'  OR manual_location_id = 'P0000008') AND need_monitor = '1'";

        foreach ($conn->query($q_find_gps_philips) as $dt_gps_philips) {
          $supplier_name = $dt_gps_philips['supplier_name'];
          $supplier_lorry_id = $dt_gps_philips['supplier_lorry_id'];
          $vehicle_number = $dt_gps_philips['vehicle_number'];
          $main_position_time_in = $dt_gps_philips['main_position_time_in'];
          $current_position_name = $dt_gps_philips['current_position_name'];
          $current_position_id = $dt_gps_philips['current_position_id'];
          $current_position_time_in = $dt_gps_philips['current_position_time_in'];
          $last_position_id = $dt_gps_philips['last_position_id'];
          $last_position_name = $dt_gps_philips['last_position_name'];
          $last_position_time_in = $dt_gps_philips['last_position_time_in'];
          $Remark = $dt_gps_philips['ontime'];
          $manual_position_id = $dt_gps_philips['manual_location_id'];
          $manual_location_name = $dt_gps_philips['manual_location_name'];
          $manual_time_in = $dt_gps_philips['manual_time_in'];  
          $taget_duration = $dt_gps_philips['taget_duration'];            
          $remark_show = $dt_gps_philips['remark'];

              if ($manual_position_id == "") {
                $method_show = "Auto";
                
                $duration_past =  number_format((strtotime($current_position_time_in) - strtotime($last_position_time_in))/60, 1, '.', '') ;

                if ($last_position_id == "") {
                  $last_position_show = "";
                }else{
                  $last_position_show = "$last_position_name : $duration_past";
                }

                $main_time_show = date("H:i" , strtotime($main_position_time_in));
              }else{
                $current_position_name = $manual_location_name;
                $method_show = "Manual";
                $current_position_time_in = $manual_time_in;
                $last_position_show = "";
                $main_time_show = date("H:i" , strtotime($manual_time_in));
              }

              
        $current_time_show = date("H:i" , strtotime($current_position_time_in));
        
        $duration_current =  number_format((strtotime("now") - strtotime($current_position_time_in))/60, 1, '.', '') ; 
        
            $data_detail = [
            'Supplier_name' => $supplier_name,
            'Vehicle_number' => $vehicle_number,
            'Method' => $method_show,
            'Time_In_Philips' => $main_time_show,
            'Location'=>$current_position_name,
            'Time_In_Location'=>$current_time_show,
            'Scheduled'=>$Remark,
            'Past_Duration_min'=>$last_position_show,
            'Remark'=>$remark_show,
            'supplier_lorry_id'=>$supplier_lorry_id,                 
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
?>