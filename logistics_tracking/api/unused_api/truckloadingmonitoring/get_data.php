 <?php 
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://idgbthb2a1vw003/scm_delivery_monitoring/modul/rest_api_layer1/truckloadingmonitoring/data.php',
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
              $Supplier_name = $boxData["Supplier_name"];
              $Vehicle_number = $boxData["Vehicle_number"];
              $Method = $boxData["Method"];
              $Time_In_Philips =$boxData['Time_In_Philips'];
              $Location =$boxData['Location'];
              $Time_In_Location =$boxData['Time_In_Location'];
              $Scheduled =$boxData['Scheduled'];
              $Past_Duration_min =$boxData['Past_Duration_min'];
              $Remark = $boxData['Remark'];
              $supplier_lorry_id = $boxData['supplier_lorry_id'];

              echo " $Supplier_name $Vehicle_number

              " ;
            }
          ?>