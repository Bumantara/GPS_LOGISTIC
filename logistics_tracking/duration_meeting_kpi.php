<!DOCTYPE html>
<html lang='en'>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
  <meta charset='UTF-8'>
  <title>LOGISTICS TRACKING</title> 

  <link rel='shortcut icon' href='img/'/>
  <link rel='shortcut icon' href='img/philips_logo_icon.ico'/>
  <link href='asset/bootstrap/css/bootstrap.css' rel='stylesheet' />
  <link href='asset/css/overall_waiting_historical.css' rel='stylesheet' />  
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="asset/font_awesome/css/font-awesome.min.css">

  <script src='asset/JQuery/jquery.min.js'></script>
  <script src='asset/bootstrap/js/bootstrap.js'></script>
  <script src="asset/JQuery/google_map.js"></script>
  <script src="asset/chart/canvasjs.min.js"></script>
</head>
  <?php
    
    include "config/koneksi pdo.php"; 
  ?>
<body> 
<div class="class">
  <div class="header1_left">
    <div class="navbar_left">
      <div class="text"></div>
    </div>
    <div class="navbar_top">
      <div class="icon_menu"onclick="goto_menu()">
              <i class="fa fa-bars" aria-hidden="true"></i>
          </div>
      <div class="text1">LOGISTICS TRACKING</div>
      <div class='time_information' id='timeshow'></div>
    </div>
  </div>
  <div class="glass">
    <div class="banner">
      <div class="navbar2">
        <div class="navbar_left1">
          <div class="logo_box">
            <div class="logo_back">
              <img src="img/back.svg"onclick="gotolanding_page();">
            </div>
          </div>
          <div class="title_box_left">
            <div class="title_left">
              DELIVERY / UNLOADING
            </div>
          </div>
        </div>
        <div class="navbar_right1">
          
          <div class="title_selecbar">
             Location:
          </div>
          <div class="selecbar">
            <div class="selectbar_timeframe">
              <div class="timeframe">
                <div class="box-Delivery">
                  <select name="filter_location" id="filter_location">
                    <option value="all">All</option>
                      <?php 
                      $q_location = "SELECT DISTINCT geofence_checkpoint_id, geofence_checkpoint_description FROM geofence_checkpoint 
                      WHERE geofence_main_id = 'PHI00001'
                      ORDER BY geofence_checkpoint_id ";
                      foreach ($conn->query($q_location) as $dt_location) {
                          $location_id = $dt_location['geofence_checkpoint_id'];
                          $location_description = $dt_location['geofence_checkpoint_description'];
                          echo "<option value= '$location_id'> $location_description</option>";
                      }
                    ?>
                  </select> 
                </div>
              </div>
            </div>
          </div>
          <div class="title_selecbar">
             Supplier:
          </div>
          <div class="selecbar">
            <div class="selectbar_timeframe">
              <div class="timeframe">
                <div class="box-Delivery">
                  <select name="filter_supplier" id="filter_supplier">
                    <option value="all">All</option>
                      <?php 
                        $q_supplier = "SELECT DISTINCT supplier_id, supplier_name FROM supplier_lorry WHERE supplier_id != 'PHI000001'and supplier_id != 'P00000001' and supplier_id != '100175245'and supplier_id != '100175321' and supplier_id != '100175327'and supplier_id != 'ESS00001'ORDER BY supplier_name ";
                        foreach ($conn->query($q_supplier) as $dt_supplier) {
                          $supplier_id = $dt_supplier['supplier_id'];
                          $supplier_name = $dt_supplier['supplier_name'];

                          echo "<option value='$supplier_id'>$supplier_name</option>";
                        }
                    ?>
                  </select> 
                </div>
              </div>
            </div>
          </div>
          <div class="title_selecbar">
            Timeframe:
          </div>
          <div class="selecbar">
            <div class="selectbar_timeframe">
               <div class="timeframe">
                <div class="box-Delivery">
                  <select name="time_frame" id="time_frame_select">
                    <option value="week">Last Weeks</option>
                    <option value="month">Last Months</option>
                    <option value="quarter">Last Quarters</option>
                  </select> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
       <div class="navbar3">
        <div class="box_chartv1">
          <div class="box1">
            <div class="title_box">Percentage of Duration Meeting KPI by unloading checkpoint</div>
            <div id="chartContainer" style="height: 35vh; width: 99%;"></div>
          </div>
          <div class="box2">
            <div class="title_box">Percentage of Duration Meeting KPI by Supplier Trucks</div>
            <div id="chartContainer1" style="height: 35vh; width: 99%;"></div>
          </div>
        </div>
        <div class="box_chartv2">
          <div class="title_box">Pareto Chart of Reasons duration not meeting KPI</div>
          <div id="chartPareto" style="height: 91.5%; width: 100%;"></div>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="filter_location" value="all">
<input type="hidden" id="filter_supplier" value="all">
<input type="hidden" id="time_frame" value="week">

  <script>
    function goto_menu(){
    window.location='http://idgbthb2a1vw006:8009/#/dashboard/landing2';
   }
   function gotolanding_page()
    {
      window.location='supplier_performance.php';
    }
  </script>
 
  <script>
    $(function () {
      // This is to send the chosen option for location filter value
      $('#filter_location').on('change', function() {   
        location_select = $("#filter_location option:selected").val();
        console.log('filter loc:' ,location_select);
        $('#filter_location').val(location_select);
        update_graph();
      });
      // This is to send the chosen option for supplier filter value
      $('#filter_supplier').on('change', function() {   
        supplier_select = $("#filter_supplier option:selected").val();
        console.log('filter sup:',supplier_select); 
        $('#filter_supplier').val(supplier_select);
        update_graph();
      });     
      // This is to send the chosen option for pareto filter value
      $('#filter_pareto').on('change', function() {   
        supplier_pareto = $("#filter_pareto option:selected").val();
        console.log('filter sup:',supplier_pareto); 
        $('#filter_pareto').val(supplier_pareto);
        update_graph();
      });     
      // This is to filter the timeframe
      $('#time_frame_select').on('change', function() {   
        time_frame_select = $("#time_frame_select option:selected").val();
        $('#time_frame').val(time_frame_select);
        update_graph();
      });
    });

    $( document ).ready(function() {
      update_graph()
    });

    function update_graph(){
      filter_location =  $('#filter_location').val();
      console.log('filter loc2:',filter_location);
      filter_supplier =  $('#filter_supplier').val(); 
      console.log('filter sup2:',filter_supplier); 
      filter_pareto =  $('#filter_pareto').val(); 
      console.log('filter sup2:',filter_pareto); 
      time_frame =  $('#time_frame').val();

      console.log('enter this loop');
      location_target = 90;
      pareto_target = 50;
      var duration_by_checkpoint = [];
      var duration_by_checkpoint_target = [];
      var duration_by_supplier = [];
      var duration_by_supplier_target = [];

      var jsonvar = "filter_location=" + filter_location +"&filter_supplier=" + filter_supplier +"&time_frame="+ time_frame + "&filter_pareto" + filter_pareto
      $.getJSON("get_graph_duration_meeting_kpi.php?" + jsonvar, 
        function(data) {  
        $.each(data, function(key, value){
          console.log(value);
          if (value[0] == 0) {
            console.log(value);
            duration_by_checkpoint.push({x: value[1], y: value[3], label: value[2]});
            duration_by_checkpoint_target.push({x: value[1], y: location_target, label: value[2]});
          }else if(value[0] == 1) {
            console.log(value);
            duration_by_supplier.push({x: value[1], y: value[3], label: value[2]});
            duration_by_supplier_target.push({x: value[1], y: location_target, label: value[2]});
          }
        });

        var chart_duration_by_checkpoint = new CanvasJS.Chart("chartContainer", {
          animationEnabled: true,
          backgroundColor: false,
          
          theme: "light2", // "light1", "light2", "dark1", "dark2"
          title: {
            margin :70,
             fontSize: 30,
             paddingLeft:40,
             fontColor:"#ffffff",
             fontStyle: false,
             fontWeight: "normal",
             fontFamily: "sans-serif"
          },
          axisY: {
            title: "%",
            titleFontSize: 18,
            maximum: 100,
            minimum:0,
            interval:20,
            titleFontColor: "#ffffff",
            labelFontColor: "#ffffff",
            tickColor: "#ffffff",
            gridThickness: 0.8,
            lineThickness: false,
            labelFontSize: 18
          },
          axisX: {
            title: "Location",
            titleFontSize: 18,
            labelFontColor: "#ffffff",
            titleFontColor: "#ffffff",
            gridColor: "ffffff",
            labelFontSize: 18,
            interval: 1
          },
          dataPointMaxWidth: 50,
          data: [{
            type: "column",
            color:"#0FE287",
            yValueFormatString: "#,##0.0#\"%\"",
            dataPoints: duration_by_checkpoint
          },
          {
            type: "line",
            markerType: "non",
            lineThickness: 3,
            color:"#ff0",
            dataPoints: duration_by_checkpoint_target
          }]
        });
        chart_duration_by_checkpoint.render();
        
        var chart_duration_by_supplier = new CanvasJS.Chart("chartContainer1", {
          animationEnabled: true,
          backgroundColor: false,
          
          title:{
            margin :70,
             fontSize: 30,
             fontColor:"#ffffff",
             fontStyle: false,
             fontWeight: "normal",
             fontFamily: "sans-serif",

          },  
          axisY: {
            title: "%",
            maximum: 100,
            minimum:0,
            interval:20,
            titleFontColor: "#ffffff",
            labelFontColor: "#ffffff",
            tickColor: "#ffffff",
            titleFontSize: 18,
            gridThickness: 0.8,
            lineThickness: false,
             labelFontSize: 18
          },
          axisX: {
            title: "Time",
            titleFontSize: 18,
            labelFontColor: "#ffffff",
            titleFontColor: "#ffffff",
            gridColor: "ffffff",
            labelFontSize: 13,
            interval: 1

          },  
          toolTip: {
            shared: true
          },
          
           legend: {
           fontColor: "#ffff",
           fontSize:15,
           cursor:"pointer",
           fontFamily: "sans-serif"
         },
         dataPointMaxWidth: 50,
          data: [{
            name : "Data Persentage",
            type: "column", 
            legendText: "Philips Area < Target Duration",
            showInLegend: false,
            color:"#0FE287",
            dataPoints:duration_by_supplier
          },
          
           {
            name : "Target",
            type: "line",
            legendText: "Target",
            showInLegend: false,
            markerType: "non",
            lineThickness: 3,
            color:"#ff0",
            dataPoints: duration_by_supplier_target

          }]
        });
        chart_duration_by_supplier.render();
      });
      

      var data_pareto = [];
      var data_pareto_percentage = [];
      $.getJSON("get_pareto_duration_meeting_kpi.php?" + jsonvar, 
        function(data) {  
        $.each(data, function(key, value){
          console.log(value);
          if (value[0] == 0) {
            data_pareto.push({x: value[1], y: value[2], label: value[3]});
          }else if(value[0] == 1) {            
            data_pareto_percentage.push({x: value[1], y: value[2], label: value[3]});
          }
        });

        var chartPareto = new CanvasJS.Chart("chartPareto", {
        backgroundColor: false,
        axisY: {
          minimum:0,
          lineColor: "#ffffff",
          tickColor: "#ffffff",
          labelFontColor: "#ffffff"
        },
         axisX: {
          title: "Location",
          titleFontSize: 18,
          labelFontColor: "#ffffff",
          titleFontColor: "#ffffff",
          gridColor: "ffffff",
          labelFontSize: 13,
          interval: 1

        },  
        axisY2: {
          maximum: 100,
          minimum:0,
          interval:10,
          suffix: "%",
          lineColor: "#ffffff",
          tickColor: "#ffffff",
          labelFontColor: "#ffffff"
        },
        dataPointMaxWidth: 50,
        data: [{
          type: "column",
          color:"#0092FF",
          dataPoints: data_pareto
        },{
          type: "line",
          axisYType: "secondary",
          markerType: "non",
          lineThickness: 3,
          color:"#ff0",
          dataPoints: data_pareto_percentage
        }]
      });
      chartPareto.render();
      });      
    };
  </script>
</body>
</html>
