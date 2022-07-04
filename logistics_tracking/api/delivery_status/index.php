<?php 
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://idgbthb2a1vw003/scm_delivery_monitoring/api/delivery_status/data.php',
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
  $delivery_status = json_decode($response,true);
  $data_delivery_status = array();
  $data_missing = array();
  $data_missing = array('GC'=> 0,'MG'=>0,'MCC'=>0,'OHC'=>0);

  foreach($delivery_status['data'] as $graphdata){
      $Business_group = $graphdata['Business_group'];
      $Count_on_time = $graphdata['Count_on_time'];
      $Count_late = $graphdata['Count_late'];
      if ($Count_on_time == 0){
        $Count_on_time = 1;
      }
      $text = number_format($Count_on_time/($Count_on_time + $Count_late)*100,1);
      $text = strval($text);
      $text = $text;
      $data_delivery_status[] = array($Business_group, $Count_on_time, "#0fe287",$Count_late,"#1E2226",$text);
      $data_missing[$Business_group] = 1;
  }

  foreach ($data_missing as $key => $val){
    if ($val == 0){
      $data_delivery_status[] = array($key, 1, "#1E2226",0,"#1E2226",'0');
    }
  }


echo json_encode($data_delivery_status);

?>
