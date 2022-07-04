 <?php 
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://idgbthb2a1vw003/scm_delivery_monitoring/api/truck_list_in_philips/',
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
    echo $response;
    $truck_list_in_philips = json_decode($response,true);

    
    foreach ($truck_list_in_philips['data'] as $boxData) {
      $supplier_name = $boxData["Supplier_name"];
      $vehicle_number = $boxData["Vehicle_number"];
      $method_show = $boxData["Method"];
      $main_time_show =$boxData['Time_In_Philips'];
      $current_position_name =$boxData['Location'];
      $current_position_time_in =$boxData['Time_In_Location'];
      $Remark =$boxData['Scheduled'];
      $last_position_show =$boxData['Past_Duration_min'];
      $remark_show = $boxData['Remark'];
      $supplier_lorry_id = $boxData['supplier_lorry_id'];

      $current_time_show = date("H:i" , strtotime($current_position_time_in));
      $duration_current =  number_format((strtotime("now") - strtotime($current_position_time_in))/60, 1, '.', '') ;

      echo "
        <tr table-striped table-lorry-information-detail>
          <th><div align='center'>$supplier_name </div></th>
          <th><div align='center'>$vehicle_number</div></th>
          <th><div align='center'>$method_show</div></th>
          <th><div align='center'>$main_time_show</div></th>
          <th><div align='center'>$current_position_name</div></th>                       
          <th><div align='center'>$current_time_show</div></th>
          <th><div align='center'>$Remark</div></th> 
          <th><div align='center'>$duration_current</div></th>
          <th><div align='center'>$last_position_show</div></th>
          <th>$remark_show</th>  
        </tr>
      ";
    }
?>