<!DOCTYPE html>
<html lang='en'>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
  <meta charset='UTF-8'>
  <title>LOGISTICS TRACKING</title> 
  <link rel='shortcut icon' href='img/'/>
  <link rel='shortcut icon' href='img/philips_logo_icon.ico'/>
  <link href='asset/bootstrap/css/bootstrap.css' rel='stylesheet' />
  <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.1.1/mapbox-gl.css' rel='stylesheet' />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href='asset/css/logistic_tracking.css' rel='stylesheet' /> 
  <link rel="stylesheet" href="asset/font_awesome/css/font-awesome.min.css">

  <script src='asset/JQuery/jquery.min.js'></script>
  <script src='asset/bootstrap/js/bootstrap.js'></script>
  <script src="asset/JQuery/google_map.js"></script>
  <script src="asset/chart/canvasjs.min.js"></script>
  <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.1.1/mapbox-gl.js'></script>
</head>

<?php
  date_default_timezone_set('Asia/Jakarta');  
  include "config/koneksi pdo.php"; 
?>

<body> 
<div class="class">
  
  <div class="header1_left">
    <div class="navbar_left">
      <div class="text">LOGISTICS TRACKING</div>
    </div>
    <div class="navbar_top">
      <div class="call">
        <div class="icon_menubar">
          <div class="icon_menu" onclick="goto_menu()">
              <i class="fa fa-bars" aria-hidden="true"></i>
          </div>
          <div class="title_text">LOGISTICS TRACKING</div>
        </div>
        <div class="time_info"id='timeshow'>
        </div> 
      </div>
    </div>
  </div>
  <div class="glass">
    <div class="banner">
      <div class="banner_left">
        
        <div  class="box_posisition">
          <?php 
            $q_find_gps_current_loc = "SELECT geofence_checkpoint_id, geofence_checkpoint_description, 
              availability, checkpoint_order, count(supplier_lorry_id) as lot_total 
              FROM incoming_lorry_gps.supplier_lorry RIGHT JOIN incoming_lorry_gps.geofence_checkpoint 
              ON incoming_lorry_gps.supplier_lorry.current_position_id  = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
              and incoming_lorry_gps.supplier_lorry.need_monitor != 0
              WHERE geofence_main_id LIKE 'PHI%' AND checkpoint_order IS NOT NULL group by geofence_checkpoint_id 
              ORDER BY checkpoint_order"; 

            foreach ($conn->query($q_find_gps_current_loc) as $dt_Checkpoint) {
              $location_name = $dt_Checkpoint["geofence_checkpoint_description"];
              $IotAvailability = $dt_Checkpoint["availability"];
              $lot_total = $dt_Checkpoint["lot_total"];

              $gate_info = "gate_available";
              if ($lot_total >= $IotAvailability) {
                $gate_info = "gate_full";
              }
              echo "
                 <div  class= 'posisition' onclick=\"gotoloading_monitoring()\">
                  <div class='area'>$location_name</div>
                  <div class='location'>No.Of Trucks</div>
                  <div class='volume $gate_info '>$lot_total / $IotAvailability </div>                 
                </div>
              " ;
            }            
          ?>
        </div>
        
        <div class="box_posision_icon">
          <div class="posision_icon">
            <div class="icon_gambar">
             <img src="img/icon/location_blue_philips.svg" alt="">
            </div>
            <div style="color:#CCCDCE" class="icon_text">Philips</div>
          </div>
          <div class="posision_icon">
            <div class="icon_gambar">
              <img  src="img/icon/location_green1.svg" alt="">
            </div>
            <div style="color:#CCCDCE" class="icon_text">Supplier</div>
          </div>
          <div class="posision_icon">
            <div class="icon_gambar">
              <img src="img/icon/truck_blue.svg" alt="">
            </div>
            <div  style="color:#CCCDCE" class="icon_text">On Route</div>
          </div>
          <div class="posision_icon">
            <div class="icon_gambar">
              <img src="img/icon/truck_red.svg" alt="">
            </div>
            <div style="color:#CCCDCE" class="icon_text">Late</div>
          </div>
          <div class="posision_icon">
            <div class="icon_gambar">
              <img src="img/icon/truck_greend.svg" alt="">
            </div>
            <div style="color:#CCCDCE" class="icon_text">On Time</div>
          </div>
          <div class="posision_icon">
            <div class="icon_gambar1">
              <di class="icon_area"></di>
            </div>
            <div style="color:#CCCDCE" class="icon_text">Zoning Area</div>
          </div>          
        </div>
        <canvas id="myCanvas" width="1380" height="950"  onclick="gotophilips_historical()">
      </div>
    </div>
      <div class="banner_right">
        <div class="chart_status">
          <div class="status_chart_top">Delivery Status</div>
          <div class="time_chart_dwn"  id='time_window'>
            <script>
              const d = new Date();
              var current_time = d.getHours();
              console.log(current_time)
              var show = '';
              if (current_time >= 7 && current_time < 18){
                show = 'Current Day';
              }
              else if (current_time >= 18  &&  current_time < 6) {
                show = 'Current Day';
              }
              
              console.log(show)
              document.getElementById("time_window").innerHTML = show ;
            </script>
          </div>
        </div>
        <div class="chart">
          <div class="title_chart" onclick="gotodelivery_status('GC/OHC')" >GC/OHC</div>
          <div id="chartGC" style="height: 19.5vh; width:40%;"></div></div>
        <div class="chart">
          <div class="title_chart" onclick="gotodelivery_status('MG')">MG</div>
          <div id="chartMG" style="height: 19.5vh; width:40%;"></div>
        </div>
        <div class="chart">
          <div class="title_chart" onclick="gotodelivery_status('MCC')">MCC</div>
          <div id="chartMCC" style="height: 19.5vh; width:40%;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
  date_default_timezone_set("Asia/Jakarta");
  $time_now =  date('Y-m-d H:i:s');
  $hour_now = date('h');
  $time_start =  date('Y-m-d 07:00:00');
  if ($hour_now < 7) {
    $time_start =  date('Y-m-d 07:00:00', strtotime("-1 days"));
  }

  $q_delivery = "CALL get_delivery_report('$time_start','$time_now')";
  $gc_status = [["y"=>0, "color" => "#0fe287"], ["y"=>100, "color" => "#1E2226"]];
  $gc_text = 0;
  $mg_status = [["y"=>0, "color" => "#0fe287"], ["y"=>100, "color" => "#1E2226"]];
  $mg_text = 0;
  $mcc_status = [["y"=>0, "color" => "#0fe287"], ["y"=>100, "color" => "#1E2226"]];
  $mcc_text = 0;


  foreach ($conn->query($q_delivery) as $dt_delivery) {
    $bg_name = $dt_delivery["bg"];
    $on_time = $dt_delivery["on_time"];
    $not_on_time = $dt_delivery["not_on_time"];
    $total = $on_time + $not_on_time;

    $perc_ontime = number_format($on_time  / $total * 100, 1, ".", "");
    $perc_not_ontime = 100 - $perc_ontime;
    $colour = "#0fe287";
    if ($perc_ontime < 60) {
      $colour = "#e51616";
    }

    if ($bg_name == "GC/OHC") {
      $gc_status = [["y"=> $perc_ontime , "color" => $colour], ["y"=>$perc_not_ontime, "color" => "#1E2226"]];
      $gc_text = $perc_ontime;
    }elseif ($bg_name == "MG") {
      $mg_status = [["y"=> $perc_ontime , "color" => $colour], ["y"=>$perc_not_ontime, "color" => "#1E2226"]];
      $mg_text = $perc_ontime;
    }elseif ($bg_name == "MCC") {
      $mcc_status = [["y"=> $perc_ontime , "color" => $colour], ["y"=>$perc_not_ontime, "color" => "#1E2226"]];
      $mcc_text = $perc_ontime;
    }

    //echo "$bg_name $on_time $not_on_time $colour";
  } 
  /*
  echo json_encode ($gc_status) . " </br>";
  echo json_encode ($mg_status) . " </br>";
  echo json_encode ($mcc_status) . " </br>";
  */
?>
<script>
  var myVar = setInterval(myTimer, 1000); // is this 1000s?
  data = 3 // what does this data mean?

  function myTimer() {
    data -= 1;
    if (data <= 0) {
      data = 3 // This is to update at every interval
      update_map() // update map function
    } 
  }
  var canvas = document.getElementById("myCanvas");
  var ctx = canvas.getContext("2d"); 
  var ctxcoord_marker = canvas.getContext("2d");
  var ctxcoord_green_truck = canvas.getContext("2d"); // this is to render a second object
  var ctxcoord_red_truck = canvas.getContext("2d"); // this is to render a second object
  var ctxcoord_blue_truck = canvas.getContext("2d"); // this is to render a second object

  function update_map(){
    var truck_status_1 = [];
    var truck_status_2 = [];
    var truck_status_3 = [];
    var ref_long = 103.869985; //this is the x-pixel ref
    var x_pixel_scale  = 0.00023097; //B 1 pixel is x degre longitude change
    var ref_lat = 1.204866; // this is the y-pixel ref
    var y_pixel_scale = -0.00020218; //A 1 pixel is y degree latitude change
    var x_cord = 0; //pixel coordinate
    var y_cord = 0;

    var batam_map = new Image();
    var blue_truck_icon = new Image();
    var green_truck_icon = new Image();
    var red_truck_icon = new Image();
    var location_marker_icon = new Image(); 

    batam_map.src = 'map1.png';
    blue_truck_icon.src = 'truck_blue1.png';
    green_truck_icon.src = 'truck_green.png';
    red_truck_icon.src = 'truck_red.png';
    location_marker_icon.src = 'location_green.png'

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    //creating the canvas so that you can draw on the same canvas
    ctx.drawImage(batam_map, 0, 0);

    $.getJSON("http://130.147.66.78/bcc/logistics_tracking/api/truck_position/" , function(data) {  
      $.each(data, function(key, value){
        if (value[3] == 1){
          truck_status_1.push([value[2], value[1]]); //on time trucks - green trucks
        }
        else if (value[3] == 2){
          truck_status_2.push([value[2], value[1]]); // Not on time trucks - red trucks
        }
        else if (value[3] == 3){
          truck_status_3.push([value[2], value[1]]); // Other trucks - On route
        }         
                      
      });
      for (const lat_long of truck_status_1){
        x_cord = lat_long[0];
        y_cord = lat_long[1];
        x_cord = ((x_cord-ref_long)/x_pixel_scale);
        y_cord = ((y_cord-ref_lat)/y_pixel_scale);

        ctxcoord_green_truck.drawImage(green_truck_icon, x_cord, y_cord);
      }
      for (const lat_long of truck_status_2){
        x_cord = lat_long[0];
        y_cord = lat_long[1];
        x_cord = ((x_cord-ref_long)/x_pixel_scale);
        y_cord = ((y_cord-ref_lat)/y_pixel_scale);

        ctxcoord_red_truck.drawImage(red_truck_icon, x_cord, y_cord);
      }
      for (const lat_long of truck_status_3){
        x_cord = lat_long[0];
        y_cord = lat_long[1];
        x_cord = ((x_cord-ref_long)/x_pixel_scale);
        y_cord = ((y_cord-ref_lat)/y_pixel_scale);

        ctxcoord_blue_truck.drawImage(blue_truck_icon, x_cord, y_cord);
      }
    });

    var location_markers = []; //make the list empty again 
        
    location_markers.push(['Teckwah',[104.03690158465923, 1.0577274540154709]]);
    location_markers.push(['Giken',[104.01663688466274, 1.1588404386991973]]);
    location_markers.push(['Yeakin',[104.02891828465926, 1.0647085286649678]]);
    location_markers.push(['Interplex',[104.07273431724083, 1.1107910907310525]]);
    location_markers.push(['Cicor',[104.03925816931849, 1.0560287991051318]]);
    location_markers.push(['GHB',[104.06758112647002, 1.1097284502703124]]);
    location_markers.push(['HLN',[104.0347781693185, 1.058791258271117]]);
    location_markers.push(['Volex',[104.94054356434253, 1.1085274132738554]]);  
    location_markers.push(['Interpak',[104.05534550738527, 1.1136583984899462]]);
    location_markers.push(['Maruho',[104.0316467, 1.0599088981127822]]);
    location_markers.push(['KKS',[104.04465625397776, 1.1108756920291016]]);
    location_markers.push(['Eka Surya',[104.0289140693185, 1.1320394790021477]]);
    location_markers.push(['Amtek Engineering',[104.0457663693185, 1.1155625340397004]]);
    location_markers.push(['Asiatech',[104.03094053996466, 1.0599559772358433]]); 

    for (const lat_long of location_markers){

      x_cord = lat_long[1][0];
      y_cord = lat_long[1][1];
      x_cord = ((x_cord-ref_long)/x_pixel_scale);
      y_cord = ((y_cord-ref_lat)/y_pixel_scale);
      ctxcoord_marker.drawImage(location_marker_icon, x_cord, y_cord);
    }
  }

  function update_map11(){
    ctx.clearRect(0, 0, canvas.width, canvas.height);
  }

  function update_map1(){
    var truck_status_1 = [];
    var truck_status_2 = [];
    var truck_status_3 = [];
    var ref_long = 103.866585; //this is the x-pixel ref
    var x_pixel_scale  = 0.00028147; //B 1 pixel is x degre longitude change
    var ref_lat = 1.165866; // this is the y-pixel ref
    var y_pixel_scale = -0.00021218; //A 1 pixel is y degree latitude change
    var x_cord = 0; //pixel coordinate
    var y_cord = 0;

    var batam_map = new Image();
    var blue_truck_icon = new Image();
    var green_truck_icon = new Image();
    var red_truck_icon = new Image();
    var location_marker_icon = new Image();

    batam_map.src = 'map1.png';
    blue_truck_icon.src = 'truck_blue1.png';
    green_truck_icon.src = 'truck_green.png';
    red_truck_icon.src = 'truck_red.png';
    location_marker_icon.src = 'location_green.png'

    //creating the canvas so that you can draw on the same canvas

    var canvas = document.getElementById("myCanvas");
    var ctx = canvas.getContext("2d"); //this is the first getcontext to execute first fuction
      //var img = document.getElementById("batam_map");
    ctx.drawImage(batam_map, 0, 0);

      //var icon = document.getElementById("blue_truck");

    var ctxcoord_marker = canvas.getContext("2d");
    var ctxcoord_green_truck = canvas.getContext("2d"); // this is to render a second object
    var ctxcoord_red_truck = canvas.getContext("2d"); // this is to render a second object
    var ctxcoord_blue_truck = canvas.getContext("2d"); // this is to render a second object
  } 

  
  var GC_delivery_status = <?php echo json_encode($gc_status); ?>;
  var MG_delivery_status = <?php echo json_encode($mg_status); ?>;
  var MCC_delivery_status = <?php echo json_encode($mcc_status); ?>;

  var gc_text = <?php echo "$gc_text";?>;   
  var mg_text = <?php echo "$mg_text";?>;
  var mcc_text = <?php echo "$mcc_text";?>;

  var chartGC = new CanvasJS.Chart("chartGC", {
    animationEnabled: true,
    backgroundColor: false,
    interactivityEnabled: false,
    title:{
      text: gc_text,
      fontColor: "#ffffff",
      fontSize: 23,
      fontFamily: "Poppins",
      verticalAlign: "center",
      padding: {
        bottom: 16,
      },
    }, 
    subtitles: [{
      text: "(%)",   
      fontColor: "#ffffff",
      verticalAlign: "center",
      fontFamily: "Poppins",
       fontSize: 15,
      padding: {
        top: 34,
      },
    }],
    data: [{
      type: "doughnut",
      startAngle: 270,
      innerRadius: "77%",
      indexLabelFontSize: 17,
      toolTipContent: "<b>{label}:</b> {y} (#percent%)",
      dataPoints: GC_delivery_status
    }]
  });
  chartGC.render();

  var chartMG = new CanvasJS.Chart("chartMG", {
    animationEnabled: true,
    backgroundColor: false,
    interactivityEnabled: false,
    title:{
      text: mg_text,
      fontColor: "#ffffff",
      fontSize: 23,
      fontFamily: "Poppins",
      verticalAlign: "center",
      padding: {
        bottom: 16,
      },
    }, 
    subtitles: [{
      text: "(%)",   
      fontColor: "#ffffff",
      verticalAlign: "center",
      fontFamily: "Poppins",
       fontSize: 15,
      padding: {
        top: 34,
      },
    }],
    data: [{
      type: "doughnut",
      startAngle: 270,
      innerRadius: "77%",
      indexLabelFontSize: 17,
      toolTipContent: "<b>{label}:</b> {y} (#percent%)",
      dataPoints: MG_delivery_status
    }]
  });
  chartMG.render();

  var chartMCC = new CanvasJS.Chart("chartMCC", {
    animationEnabled: true,
    backgroundColor: false,
    interactivityEnabled: false,
    title:{
      text: mcc_text,
      fontColor: "#ffffff",
      fontSize: 23,
      fontFamily: "Poppins",
      verticalAlign: "center",
      padding: {
        bottom: 16,
      },
    }, 
    subtitles: [{
      text: "(%)",   
      fontColor: "#ffffff",
      verticalAlign: "center",
      fontFamily: "Poppins",
       fontSize: 15,
      padding: {
        top: 34,
      },
    }],
    data: [{
      type: "doughnut",
      startAngle: 270,
      innerRadius: "77%",
      indexLabelFontSize: 17,
      toolTipContent: "<b>{label}:</b> {y} (#percent%)",
      dataPoints: MCC_delivery_status
    }]
  });
  chartMCC.render();
</script>

<script>
   function goto_menu(){
    window.location='http://130.147.66.91:8009/#/dashboard/landing2';
   }
    function gotoloading_monitoring(){
      window.location='loading_monitoring.php';
    }
    function gotophilips_historical(){
      window.location='supplier_performance.php';
    }
    function gotodelivery_status(line){
      window.location='runways/?bg=' + line;
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
</script>

 
</body>
</html>
