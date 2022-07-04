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
            $url = "http://130.147.66.78/bcc/logistics_tracking/api/";
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url . 'checkpoint_availability/',
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
                 <div  class= 'posisition' onclick=\"gotoloading_monitoring()\">
                  <div class='area'>$location_name</div>
                  <div class='location'>No.Of Trucks</div>
                  <div class='volume $gate_info '>$IotTotal / $IotAvailability </div>                 
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
      <div class="banner_right" >
        <div class="chart_status">
          <div class="status_chart_top">Delivery Status</div>
          <div class="time_chart_dwn"  id='time_window'>
            <script>
              const d = new Date();
              var current_time = d.getHours();
              console.log(current_time)
              var show = '';
              if (current_time >= 0 && current_time < 4){
                show = 'Time window: 00:00:00 - 04:00:00';
              }
              else if (current_time >= 4  &&  current_time < 8) {
                show = 'Time window: 04:00:00 - 08:00:00';
              }
              else if (current_time >= 8  &&  current_time < 12) {
                show = 'Time window: 08:00:00 - 12:00:00';
              }
              else if (current_time >= 12  &&  current_time < 16) {
                show = 'Time window: 12:00:00 - 16:00:00';
              }
              else if (current_time >= 16  &&  current_time < 20) {
                show = 'Time window: 16:00:00 - 20:00:00';
              }
              else if (current_time >= 20  &&  current_time < 23) {
                show = 'Time window: 20:00:00 - 24:00:00';
              }
              console.log(show)
              document.getElementById("time_window").innerHTML = show ;
            </script>
          </div>
        </div>
        <div class="chart">
          <div class="title_chart">GC</div>
          <div id="chartContainer" style="height: 19.5vh; width:40%;"></div></div>
        <div class="chart">
          <div class="title_chart">MG</div>
          <div id="chartContainer2" style="height: 19.5vh; width:40%;"></div>
        </div>
        <div class="chart">
          <div class="title_chart">MCC</div>
          <div id="chartContainer3" style="height: 19.5vh; width:40%;"></div>
        </div>
        <div class="chart">
          <div class="title_chart">OHC</div>
          <div id="chartContainer4" style="height: 19.5vh; width:40%;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>

    window.onload = function () {

    };
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

       $.getJSON("http://130.147.66.78/bcc/logistics_tracking/api/truck_position/" ,  
    function(data) {  
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
      var GC_delivery_status = [];
      var MG_delivery_status = [];
      var OHC_delivery_status = [];
      var MCC_delivery_status = [];

      var gc_text = "1";
      var mg_text = "1";
      var ohc_text = "1";
      var mcc_text = "1";

      $.getJSON("http://130.147.66.78/bcc/logistics_tracking/api/delivery_status/" ,  
        function(data) {  
          $.each(data, function(key, value){
            if(value[0] == "GC"){
              GC_delivery_status.push({y: value[1], color: value[2]});
              GC_delivery_status.push({y: value[3], color: value[4]});
              gc_text = value[5];
            }
            if(value[0] == "MG"){
              MG_delivery_status.push({y: value[1], color: value[2]});
              MG_delivery_status.push({y: value[3], color: value[4]});
              mg_text = value[5];
            }
            if(value[0] == "MCC"){
              MCC_delivery_status.push({y: value[1], color: value[2]});
              MCC_delivery_status.push({y: value[3], color: value[4]});
              mcc_text = value[5];
            }          
            if(value[0] == "OHC"){
              OHC_delivery_status.push({y: value[1], color: value[2]});
              OHC_delivery_status.push({y: value[3], color: value[4]});
              ohc_text = value[5];
            }               
            });

            var chart = new CanvasJS.Chart("chartContainer", {
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

        chart.render();
              var chart = new CanvasJS.Chart("chartContainer2", {
                    animationEnabled: true,
                   backgroundColor: false,
                    interactivityEnabled: false,
                     title:{
                          text: mg_text,
                          fontColor: "#ffffff",
                          fontSize: 23,
                          fontFamily: "Poppins",
                          padding: {
                            bottom: 16,
                          },
                          verticalAlign: "center"
                        }, 
                        subtitles: [{
                          text: "(%)",   
                          fontColor: "#ffffff",
                          fontFamily: "Poppins",
                           fontSize: 15,
                              padding: {
                                top: 34,
                              },
                          verticalAlign: "center"
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
            
        chart.render();

              var chart = new CanvasJS.Chart("chartContainer3", {
                    animationEnabled: true,
                   backgroundColor: false,
                    interactivityEnabled: false,
                     title:{
                          text: mcc_text,
                          fontColor: "#ffffff",
                          fontSize: 23,
                          fontFamily: "Poppins",
                          padding: {
                            bottom: 16,
                          },
                          verticalAlign: "center"
                        },
                      subtitles: [{
                        text: "(%)",   
                        fontColor: "#ffffff",
                        fontFamily: "Poppins",
                           fontSize: 15,
                              padding: {
                                top: 34,
                              },
                          verticalAlign: "center"
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
                  
        chart.render();
              var chart = new CanvasJS.Chart("chartContainer4", {
                    animationEnabled: true,
                    backgroundColor: false,
                     interactivityEnabled: false,
                     title:{
                          text: ohc_text,
                          fontColor: "#ffffff",
                          fontSize: 23,
                          fontFamily: "Poppins",
                          padding: {
                            bottom: 16,
                          },
                          verticalAlign: "center"
                        },
                      subtitles: [{
                        text: "(%)",   
                        fontColor: "#ffffff",
                         fontFamily: "Poppins",
                           fontSize: 15,
                              padding: {
                                top: 34,
                              },
                        verticalAlign: "center"
                      }],
                    data: [{
                    type: "doughnut",
                    innerRadius: "77%",
                    startAngle:270 ,
                    indexLabelFontSize: 17,
                    toolTipContent: "<b>{label}:</b> {y} (#percent%)",
                    dataPoints: OHC_delivery_status
               }]
            });
            
        chart.render();
      });
     
    

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
