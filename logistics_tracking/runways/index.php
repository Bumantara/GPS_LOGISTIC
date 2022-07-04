
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="shortcut icon" href="../img/philips_logo_icon.ico">
  <link rel="stylesheet" href="../asset/libs/datatables/DataTables-1.11.3/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="../asset/css/style3.css">
  <link rel="stylesheet" href="../asset/libs/fontawesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


  <!-- <link rel="stylesheet" href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"> -->
  <script src="../asset/js/jquery.min.js"></script>
  <script src="../asset/libs/datatables/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <!-- <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script> -->



  <title>Dashboard @ Philips</title>
</head>

<body>
  <div class="container">
    <div class="right-content">

      <div class="header">
        <div class="header-airport">
          <a href="http://130.147.66.91:8009/#/dashboard/landing2"><i class="fa fa-bars" aria-hidden="true" style="color:white;"></i></a>
          <div class="title-airport">LOGISTICS TRACKING</div>
        </div>
        <div class="header-time">
          <div id="timeshow" class="header_title"></div>
        </div>
      </div>
      <div class="header-departure">
        <div class="back-page"><img onclick="location.href='javascript:history.go(-1)'" src="../asset/font/back.svg" alt="" style="height: 5vh;"></div>
      <?php
        if (isset($_GET["bg"])) {
          $bg_id = $_GET["bg"];
        }
      ?>
       <div class="title_departure">Delivery Status - <?php  echo "$bg_id"; ?></div>

      </div>

      <div class="content">
        <div class="affected-runways">
          <div class="affected-runways-header">
            <div class="header-runways-title"></div>
            <div class="header-runways-title"></div>
            <div class="header-currentday">
              <button class="button">Current Day</button>
            </div>
          </div>

          <div class="top-effected-content">
            <table id="myTable" class="display" style="width: 100%;">
              <thead>
                <tr>                  
                  <th>Supplier Name</th>
                  <th>Vehicle No</th>
                  <th>Area</th>
                  <th>Schedule</th>
                  <th>Actual</th>
                  <th>Remarks</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>   
                <?php
                  include "../config/koneksi pdo.php"; 
                  date_default_timezone_set("Asia/Jakarta");
                  $time_now =  date('Y-m-d H:i:s');

                  $hour_now = date('H');
                  $time_start =  date('Y-m-d 07:00:00');
                  if ($hour_now < '07') {
                    $time_start =  date('Y-m-d 07:00:00', strtotime("-1 days"));
                  }

                  $q_delivery = "CALL get_delivery_data('$time_start','$time_now', '$bg_id')";
                  
                  foreach ($conn->query($q_delivery) as $dt_delivery) {
                    $supplier_name = $dt_delivery["supplier_name"];
                    $vehicle_number = $dt_delivery["vehicle_number"];
                    $area = $dt_delivery["area"];
                    $time_start_delivery = $dt_delivery["time_start_delivery"];
                    $time_end_delivery = $dt_delivery["time_end_delivery"];                    
                    $time_entrance = $dt_delivery["time_entrance"];
                    $remark = $dt_delivery["remark"];
                    $on_time = $dt_delivery["on_time"];

                    $plan_start = substr($time_start_delivery, 0, 5);
                    $plan_end = substr($time_end_delivery, 0, 5);
                    $time_actual = date("d-M H:i", strtotime($time_entrance));
                    

                    $color_ontime = "#03B12F";
                    if ($on_time == "Not On Time") {
                      $color_ontime = "#F12648";
                      $status_ontime = "LATE";
                    }else{
                      $status_ontime = "ON TIME";
                    }

                    echo "
                      <tr>
                        <th>$supplier_name</th>
                        <th>$vehicle_number</th>
                        <th>$area</th>
                        <th>$plan_start - $plan_end</th>
                        <th>$time_actual</th>
                        <th>$remark</th>
                        <th style='background-color: $color_ontime;'>$status_ontime</th>
                      </tr>
                    ";
                    
                  } 
                ?>             
                  
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Testing Canvas js -->
  <!-- Canvas JS Container 1 -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('#myTable').DataTable({
        "ordering": true,
        pageLength: 8,
        initComplete: function() {
          this.api().columns([0, 3, 6]).every(function(d) {
            var column = this;
            var theadname = $('#myTable th').eq([d]).text();
            var select = $('<select class="mx-1"><option value="">' + theadname + '</option></select>')
              .appendTo($(column.header()).empty())
              .on('change', function() {
                var val = $.fn.dataTable.util.escapeRegex(
                  $(this).val()
                );

                column
                  .search(val ? '^' + val + '$' : '', true, false)
                  .draw();
              });

            column.data().unique().sort().each(function(d, j) {
              var val = $('<div/>').html(d).text();
              select.append('<option value="' + val + '">' + val + '</option>')
            });
          });
        }
      });
    });
    
    var myVar = setInterval(myTimer, 400);
    var number = 1;
    var reload_counter = 1;
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var datetime = new Date();
    initial_hour = datetime.getHours();
    var data_output = ' ';

    if (initial_hour < 10) {
      initial_hour = '0' + initial_hour
    };

    function myTimer() {
      var d = new Date();
      var day = days[d.getDay()];
      var date = d.getDate();
      var month = months[d.getMonth()];
      var year = d.getFullYear();
      hour = d.getHours();
      minute = d.getMinutes();
      seconds = d.getSeconds();

      if (day < 10) {
        day = '0' + day
      }
      if (hour < 10) {
        hour = '0' + hour
      }
      if (minute < 10) {
        minute = '0' + minute
      }
      if (seconds < 10) {
        seconds = '0' + seconds
      }

      var timeshow = hour + ':' + minute + ':' + seconds;
      dateshow = day + ', ' + date + ' ' + month + ' '; // + ' ' + month + ' ' + d.getFullYear();

      document.getElementById('timeshow').innerHTML = dateshow + ' ' + timeshow + ' ' + data_output;
    }
  </script>
</body>

</html>