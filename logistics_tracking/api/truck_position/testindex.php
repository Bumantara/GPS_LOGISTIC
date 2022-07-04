<?php 
  date_default_timezone_set('Asia/Jakarta');  
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://idgbthb2a1vw003/scm_delivery_monitoring/api/truck_position/data.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Authorization: Basic dXNlcjp1c2Vy',
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  $active_truck_coordinate = json_decode($response,true);
  $data_truck_coordinate = array();
  
  foreach($active_truck_coordinate['data'] as $mapdata){
      $mapdisplayid = $mapdata['map_display_id'];
      $latitude = $mapdata['latitude'];
      $longtitude = $mapdata['longtitude'];
      $truck_status = $mapdata['truck_status'];
      $data_truck_coordinate[] = array($mapdisplayid, $latitude, $longtitude,$truck_status);
  } 

echo json_encode($data_truck_coordinate);

?>
