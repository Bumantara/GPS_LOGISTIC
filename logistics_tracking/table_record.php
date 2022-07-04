<!DOCTYPE html>
<html lang='en'>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
  <meta charset='UTF-8'>
  <title>GPS Live Monitoring</title> 
  <link rel='shortcut icon' href='img/'/>
  <link rel='shortcut icon' href='img/philips_logo_icon.ico'/>
  <link href='asset/bootstrap/css/bootstrap.css' rel='stylesheet' />  
  <link href='asset/css/loading_monitoring1.css' rel='stylesheet' />  
  <link rel="stylesheet" href="asset/font_awesome/css/font-awesome.min.css">

  <script src='asset/JQuery/jquery.min.js'></script>
  <script src='asset/bootstrap/js/bootstrap.js'></script>
  <meta http-equiv="refresh" content="10" >
</head>

<?php echo "";
  date_default_timezone_set('Asia/Jakarta');  
  include "config/koneksi pdo.php"; 
?>


<body> 
  <div class='header_1'>
    <div class="box_header_1">
      <div class="box_title">
        <div class="icon_menu"onclick="goto_menu()">
              <i class="fa fa-bars" aria-hidden="true"></i>
          </div>
        <div class='dashboard_information'>
          LOGISTICS TRACKING
        </div> 
      </div>
      <div class="box_total">
        <div class="total_gps">
          <?php echo "";
            $q_find_gps = "SELECT * FROM supplier_lorry where gps_imey != '' " ;
            $result = $conn->query($q_find_gps);
            $total_gps = $result->rowCount();

            $q_find_not_active_gps = "SELECT * FROM supplier_lorry where checking_position_name = '' and gps_imey != ''";
            $result_not_active = $conn->query($q_find_not_active_gps);
            $gps_not_active = $result_not_active->rowCount();

            $gps_active = $total_gps  - $gps_not_active;

            echo "Total GPS Devices : $total_gps | online: $gps_active | offline: $gps_not_active";
          ?>
        </div>   
      </div>
      <div class="box_time">
        <div class='time_information' id='timeshow'>
          
        </div>   
      </div>
    </div>
  </div>

  <div class="navbar2">
    <div class="navbar_title">
      <div class="icon_back">
        <img src="img/back.svg"onclick="gotolanding_page();">
      </div>
      <div class="title_icon">Table Record Supplier Lorry</div>  
    </div>
  </div>
  <div class='gate_information'>
        <?php 
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://130.147.66.78/bcc/logistics_tracking/api/checkpoint_availability/',
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
            $locationlot = json_decode($response,true);

            foreach ($locationlot['data'] as $boxData) {
              $location_name = $boxData["locationName"];
              $IotTotal = $boxData["IotTotal"];
              $IotAvailability = $boxData["IotAvailability"];
              $gate_info = "gate_available";
              if ($IotTotal >= $IotAvailability) {
                $gate_info = "gate_full";
              }
              echo "
              
                
                  <div class='gate_information_sub'>
                    <div class='gate_information_content'>
                      <div class='gate_information_top'>$location_name</div>
                      <div class='gate_information_middle'>No.Of Trucks</div>
                        <div class='gate_information_buttom $gate_info '>$IotTotal / $IotAvailability </div>
                    </div>      
                  </div>
                
                             
              " ;
            }
          ?>
  </div>
  <div class='lorry_information'>
     <div class='table-lorry-information'>
      <table  class='table table-striped table-lorry-information-detail' cellspacing='0' width='150px'>
        <thead>          
          <tr>
            <th rowspan='1'><div align='center'>Supplier</div></th>
            <th rowspan='1'><div align='center'>Vehicle No</div></th>
            <th rowspan="1"><div align='center'>Method</div></th>
            <th rowspan='1'><div align='center'>Time in (Philips)</div></th>
            <th rowspan='1'><div align='center'>Location</div></th>                      
            <th rowspan='1'><div align='center'>Time In </div></th>
            <th rowspan='1'><div align='center'>Scheduled</div></th>   
            <th rowspan='1'><div align='center'>Duration (min) </div></th>
            <th rowspan='1'><div align='center'>Past Duration (min)</div></th>            
            <th rowspan='1'><div align='center'>Remark</div></th>
            
          </tr>

       
        </thead>

        <tbody>

          <?php 
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://130.147.66.78/bcc/logistics_tracking/api/truck_list_in_philips/',
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
        
        </tbody>
  </div>
</div>
<script> 
     function gotolanding_page()
    {
      window.location='index.php';
    }
</script>
  <script type='text/javascript'>

    var myVar = setInterval(myTimer, 500);
    var number = 1;
    var reload_counter = 1;
    var days =  ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var datetime = new Date();
    initial_hour = datetime.getHours();
    var data_output = 12; 

    if (initial_hour < 10) {initial_hour = '0' + initial_hour};

    function myTimer() {
      var d = new Date();
      var day = days[ d.getDay() ];
      var date = d.getDate(); 
      var month = months[ d.getMonth() ];
      hour = d.getHours();
      minute = d.getMinutes();
      seconds = d.getSeconds();

      if (day < 10) {day = '0' + day}
      if (hour < 10) {hour = '0' + hour}
      if (minute < 10) {minute = '0' + minute}
      if(seconds < 10 ) {seconds = '0' + seconds}

      
      var timeshow =  hour + ':' + minute + ':' + seconds;
      dateshow = day + ', ' + date + ' ' + month ;//+ ' ' + d.getFullYear();

      document.getElementById('timeshow').innerHTML =  dateshow + ' ' + timeshow  ;   


    }

     function goto_menu(){
    window.location='http://130.147.66.91:8009/#/dashboard/landing2';
   }
    function gotowelcome(){
      window.location='../welcome/';
    }

    function update_remark(supplier_lorry_id){
      remark_data = $("#remarks"+ supplier_lorry_id + " option:selected").val();
      if (remark_data != "") {
        $("#data"+ supplier_lorry_id).load("../data_provide/get_update_remark.php", {
          supplier_lorry_id: supplier_lorry_id,
          remark_data: remark_data
        });
      }
    }
  </script>

</body>
</html>
