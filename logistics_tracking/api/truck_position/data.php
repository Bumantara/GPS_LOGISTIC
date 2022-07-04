
<?php
echo "";
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
  $guid = GUID();

  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    $responseAPI=
      [
        "statusCode" => "400",
        "statusMessage" => "Unauthorized: Missing or invalid authentication token.",
        "timestamp" => "$datetime",
        "data" => "no data",
        "locationNAme" =>"",
        "IoAvailability" =>"",
        "IotTotal" => ""
      ];
  } else{
    if ($_SERVER['PHP_AUTH_USER'] == "user" && $_SERVER['PHP_AUTH_PW'] == "user") {
      $responseAPI=
        [
          "statusCode" => "200",
          "statusMessage" => "succeed.",
          "timestamp" => "$datetime",
          "guid" => "$guid",
          "data" => array(),
        ];
        /*
        $q_find_truck_position = "SELECT * FROM supplier_lorry WHERE (current_position_id = 'P0000001' OR current_position_id = 'P0000002' OR current_position_id = 'P0000003' OR current_position_id = 'P0000004' OR current_position_id = 'P0000005' OR current_position_id = 'P0000008' OR manual_location_id = 'P0000001' OR manual_location_id = 'P0000002' OR manual_location_id = 'P0000003' OR manual_location_id = 'P0000004'  OR manual_location_id = 'P0000005'  OR manual_location_id = 'P0000008') AND need_monitor = '1'";*/
         $q_find_truck_position = "SELECT * FROM supplier_lorry WHERE need_monitor = '1'";
         foreach ($conn->query($q_find_truck_position) as $dt_gps_philips) {
             $supplier_lorry_id = $dt_gps_philips['supplier_lorry_id'];
             $map_display_id = $dt_gps_philips['map_display_id'];
             $supplier_name = $dt_gps_philips['supplier_name'];
             $vehicle_number = $dt_gps_philips['vehicle_number'];
             $latitude = $dt_gps_philips['lattitude'];
             $longtitude = $dt_gps_philips['longitude'];
             $ontime = $dt_gps_philips['ontime'];

             if($ontime == 'On Time'){
                $truck_status = 1;
                  }
              elseif($ontime == 'Not On Time'){
                  $truck_status = 2;
                  }
              else{
                  $truck_status = 3;
                  }
          $data_detail = [
            'supplier_lorry_id' => $supplier_lorry_id ,
            'map_display_id' => $map_display_id,
            'Supplier_name' => $supplier_name,
            'Vehicle_number' => $vehicle_number,
            'latitude' => $latitude,
            'longtitude'=> $longtitude,
            'truck_status' => $truck_status,
            ];
          array_push( $responseAPI["data"],  $data_detail);

         }

    }else{
      $responseAPI=
        [
          "statusCode" => "400",
          "statusMessage" => "Unauthorized: invalid username.",
          "timestamp" => "$datetime",
          "data" => "no data"
        ];
    }
  }

  $data = json_encode($responseAPI);
  echo $data
?>
