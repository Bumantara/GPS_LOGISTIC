<!DOCTYPE html>
<html lang='en'>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
  <meta charset='UTF-8'>
  <title>LOGISTICS TRACKING</title> 

  <link rel='shortcut icon' href='img/'/>
  <link rel='shortcut icon' href='img/philips_logo_icon.ico'/>
  <link href='asset/bootstrap/css/bootstrap.css' rel='stylesheet' />
  <link href='asset/css/supplier_performance1.css' rel='stylesheet' />  
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
              Supplier Performance
            </div>
          </div>
        </div>
        <div class="navbar_right1">
          
          <div class="title_selecbar">
             Filter by :
          </div>
          <div class="selecbar">
            <div class="selectbar_timeframe">
              <div class="timeframe">
                <div class="box-Delivery">
                  <select name="filter" id="filter_select">
                    <option value="all">All</option>
                    <option value="supplier">Supplier</option>
                    <option value="location">Location</option>
                  </select> 
                </div>
              </div>
            </div>
          </div>
          <div class="selecbar">
            <div class="selectbar_timeframe">
              <div class="timeframe">
                <div class="box-Delivery" id="filter_detail_box">
                </div>
              </div>
            </div>
          </div>
          <div class="title_selecbar">
            Timeframe :
          </div>
          <div class="selecbar">
            <div class="selectbar_timeframe">
               <div class="timeframe">
                <div class="box-Delivery">
                  <select name="time_frame" id="time_frame_select">
                    <option value="week">Past 12 Week</option>
                    <option value="month">Past 12 Months</option>
                    <option value="quarter">Past 6 Quarter</option>
                    <option value="year">Past 6 year</option>
                  </select> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
       <div class="navbar3">
        <div class="box1">
          <div class="title_box">On Time Delivery %</div>
          <div class="box_puss_btm">
            <div class="puss_btm"onclick="gotoontime_delivery();">View detailed</div>
          </div>
          <div class="chart_column"> <div id="chartContainer" style="height: 63vh; width: 99%;"></div></div>
        </div>
        <div class="box2">
          <div class="title_box">Waiting/Unloading Duration meeting KPI %</div>
          <div class="box_puss_btm">
            <div class="puss_btm"onclick="gotoduration_kpi();">View detailed</div>
          </div>
          <div class="chart_column"><div id="chartContainer1" style="height: 63vh; width: 99%;"></div></div>
          <div class="legend">
            <div class="legend1"></div>
            <div class="legend2">Philips Area < Target Duration</div>
            <div class="legend3"></div>
            <div class="legend2">Waiting Area < 5 Min</div>
            <div class="legend5"></div>
            <div class="legend2">Target</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="filter" value="all">
<input type="hidden" id="filter_detail" value="">
<input type="hidden" id="time_frame" value="week">

  <script>
    function goto_menu(){
    window.location='http://130.147.66.91:8009/#/dashboard/landing2';
   }
   function gotolanding_page()
    {
      window.location='index.php';
    }
    function gotoontime_delivery()
    {
       window.location='on_time_kpi.php';
    }
    function gotoduration_kpi()
    {
       window.location='duration_meeting_kpi.php';
    }
  </script>
 
  <script>
    $(function () {
      $('#filter_select').on('change', function() {   
        filter = $("#filter_select option:selected").val();
        $('#filter').val(filter);
        if (filter == "all"){
          $("#filter_detail_box").html("");
          update_graph()
        }else{ 
          $("#filter_detail_box").html("");            
          $("#filter_detail_box").load("get_filter_detail.php", {
            filter: $(this).val()
          });       
        }
      });


      $('#time_frame_select').on('change', function() {   
        time_frame_select = $("#time_frame_select option:selected").val();
        $('#time_frame').val(time_frame_select);
        update_graph();
      });
    });

    function get_update_filter_detail(){
      console.log('get_update_filter_detail');
      filter_detail_select = $("#filter_detail_select option:selected").val();
      $('#filter_detail').val(filter_detail_select); 
      console.log(filter_detail_select +  " - " + $('#filter_detail').val());
      update_graph(); 
    }
    // this is to activate the function upon loading
    $(function() {
        update_graph(); 
    });

    function update_graph(){
      filter =  $('#filter').val();
      filter_detail =  $('#filter_detail').val();  
      time_frame =  $('#time_frame').val();
      //console.log("123213filter=" + filter +"&filter_detail=" + filter_detail +"&time_frame="+ time_frame);
      // not filter != all or not filter detail == " "
      // means filter should be either == something or filter should not be == all
      if (!(filter != "all" && filter_detail == "")) {
        console.log('enter this loop');
        duration_target = 90;
        ontime_target = 70;
        var duration_waiting_actual = [];
        var duration_waiting_target = [];
        var duration_philips_actual = [];
        var ontime_data = [];
        var ontime_data_target = [];
        // var json var = 'supplier = ' option1: all == ' ', option2: specific filter == 'Teckwah' + 'location = ' + 'timeframe'
        var jsonvar = "filter=" + filter +"&filter_detail=" + filter_detail +"&time_frame="+ time_frame
        console.log(jsonvar);
        $.getJSON("get_kpi_graph.php?" + jsonvar, 
          function(data) {  
          $.each(data, function(key, value){         
            //console.log(value);
            if (value[0] == 0) {
              ontime_data.push({x: value[1], y: value[2], label: value[3]});
              ontime_data_target.push({x: value[1], y: ontime_target, label: value[3]});
            }else if(value[0] == 1) {
              duration_waiting_actual.push({x: value[1], y: value[2], label: value[3]});
              duration_waiting_target.push({x: value[1], y: duration_target, label: value[3]});
            }else if(value[0] == 2) {
              duration_philips_actual.push({x: value[1], y: value[2], label: value[3]});
            }
          });

          var chart_ontime = new CanvasJS.Chart("chartContainer", {
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
              interval:10,
              titleFontColor: "#ffffff",
              labelFontColor: "#ffffff",
              tickColor: "#ffffff",
              gridThickness: 0.8,
              lineThickness: false,
              labelFontSize: 18
            },
            axisY2:{
              maximum: 100,
              minimum:0,
              interval:10,
              gridThickness: 0.8,
              lineThickness: true
            },
            axisX: {
              title: "Time",
              titleFontSize: 18,
              labelFontColor: "#ffffff",
              titleFontColor: "#ffffff",
              gridColor: "ffffff",
              labelFontSize: 18,
              interval: 1
            },
            dataPointMaxWidth: 30,
            data: [{
              type: "column",
              color:"#0092FF",
              yValueFormatString: "#,##0.0#\"%\"",
              dataPoints: ontime_data
            },
            {
              type: "line",
              markerType: "non",
              lineThickness: 3,
              color:"#ff0",
              dataPoints: ontime_data_target
            }]
          });
          chart_ontime.render();

          var chart_duration = new CanvasJS.Chart("chartContainer1", {
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
              interval:10,
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
              labelFontSize: 18,
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
           dataPointMaxWidth: 30,
            data: [{
              type: "column", 
              name: "Philips Area < Target Duration",
              legendText: "Philips Area < Target Duration",
              showInLegend: false,
              color:"#11CDEF",
              dataPoints:duration_philips_actual
            },
            {
              type: "column",
              name: "On Time Delivery %",
              legendText: "Waiting Area < 5 Min",
              showInLegend: false, 
              color:"#0092FF",
              dataPoints: duration_waiting_actual
            },
             {
              type: "line",
              name: "Target",
              legendText: "Target",
              showInLegend: false,
              markerType: "non",
              lineThickness: 3,
              color:"#ff0",
              dataPoints: duration_waiting_target

            }]
          });
          
          chart_duration.render();
         
        }); 
      }
    };
  </script>
</body>
</html>
