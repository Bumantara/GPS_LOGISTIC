<?php
  include "config/koneksi pdo.php"; 
  date_default_timezone_set("Asia/Jakarta");
  $filter_location= "all";
  if (isset($_GET['filter_location'])) {  // filter location
    $filter_location=$_GET['filter_location'];
  }
  $filter_supplier= "all";
  if (isset($_GET['filter_supplier'])) {    
    $filter_supplier=$_GET['filter_supplier']; //filter supplier
  }
  $time_frame= "week";  
  if (isset($_GET['time_frame'])) {     
    $time_frame=$_GET['time_frame'];
  }

  $data_points = array(); // creating the final array to be retrieved
  if($time_frame == "week"){
    $number_week = 12;
    $week_search = date("yW", strtotime("- 13 Weeks")); // start date
    $week_current = date("yW", strtotime("- Now")); // end date
    $filter_selection = "";
    // Need to do double filter
      if ($filter_location != "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
      // filter on location only
      elseif ($filter_location != "all" and $filter_supplier == "all"){
        $filter_selection = " AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
    // filter on supplier only
      elseif ($filter_location == "all" and $filter_supplier != "all"){
        $filter_selection = "AND historical_performance.supplier_id = '$filter_supplier' ";
      }
      else{
         $filter_selection = " ";
      }

      $data_points = array();
    
      $q_location = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.week,
                    historical_performance.week_code,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    WHERE week_code >= '$week_search' AND week_code < '$week_current'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.geofence_checkpoint_id "; // adding filter selection - filter by a specific supplier - getting the data from historical graph if it exists  
        /*
      $q_location = "SELECT geofence_checkpoint_id,supplier_id,week,
                    week_code,
                    sum(number_delivery) as number_delivery, 
                    sum(number_meet_duration) as number_meet_duration 
                    FROM historical_performance
                    WHERE week_code >= '$week_search' AND week_code < '$week_current'o
                    and geofence_checkpoint_id like 'P%'and geofence_checkpoint_id <> 'P0000006' and geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY geofence_checkpoint_id ";    */         

      //echo "$q_perf </br>";
       $i = 0;
        foreach ($conn->query($q_location) as $dt_loc) {
          $geofence_checkpoint_id = $dt_loc['geofence_checkpoint_id'];
          $geofence_checkpoint_description = $dt_loc['geofence_checkpoint_description'];
          $number_meet_duration = $dt_loc['number_meet_duration'];
          $total_number_delivery = $dt_loc['number_delivery'];
           $location_perc = 0;
            if ($number_meet_duration > 0){
              $location_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
            }
            $i = $i +1;
            $point = [0,$i,$geofence_checkpoint_description,$location_perc];
            array_push($data_points, $point);
        }
      
      $q_supplier = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.week,
                    historical_performance.week_code,supplier_lorry.supplier_name,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE week_code >= '$week_search' AND week_code < '$week_current'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.supplier_id "; // adding filter selection - filter by a specific supplier - getting the data from historical 
        /*
        $q_supplier = "SELECT geofence_checkpoint_id,
                    week_code, supplier_id,
                    sum(number_delivery) as number_delivery, 
                    sum(number_meet_duration) as number_meet_duration 
                    FROM historical_performance 
                    WHERE week_code >= '$week_search' AND week_code < '$week_current'
                    and geofence_checkpoint_id like 'P%'and geofence_checkpoint_id <> 'P0000006' and geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY supplier_id "; */
        $i = 0;      
        foreach ($conn->query($q_supplier) as $dt_sup) {
          $supplier_id = $dt_sup['supplier_id'];
          $supplier_name = $dt_sup['supplier_name'];
          $number_meet_duration = $dt_sup['number_meet_duration'];
          $total_number_delivery = $dt_sup['number_delivery'];

          $supplier_perc =0;
          if ($number_meet_duration > 0){
            $supplier_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
          }
          $i = $i +1;
          $point = [1,$i,$supplier_name,$supplier_perc];
          array_push($data_points, $point);
        }    
    //data month
  }elseif($time_frame == "month"){
    $number_month = 12;
    $month_search = date("ym", strtotime("- 13 month"));
    $month_current = date("ym", strtotime("Now"));
    $filter_selection = "";
    // Need to do double filter
      if ($filter_location != "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
      // filter on location only
      elseif ($filter_location != "all" and $filter_supplier == "all"){
        $filter_selection = " AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
    // filter on supplier only
      elseif ($filter_location == "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' ";
      }

      $data_points = [];
      $q_location = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.month,
                    historical_performance.month_code,supplier_lorry.supplier_name,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE month_code >= '$month_search' AND month_code < '$month_current'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.geofence_checkpoint_id "; // adding filter selection - filter by a specific supplier - getting the data from historical graph if it exists
      //echo "$q_perf </br>";
       $i = 0;
        foreach ($conn->query($q_location) as $dt_loc) {
          $geofence_checkpoint_id = $dt_loc['geofence_checkpoint_id'];
          $geofence_checkpoint_description = $dt_loc['geofence_checkpoint_description'];
          $number_meet_duration = $dt_loc['number_meet_duration'];
          $total_number_delivery = $dt_loc['number_delivery'];
           $location_perc = 0;
            if ($number_meet_duration > 0){
              $location_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
            }
            $i = $i +1;
            $point = [0,$i,$geofence_checkpoint_description,$location_perc];
            array_push($data_points, $point);
        }
      $q_supplier = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.month,
                    historical_performance.month_code,supplier_lorry.supplier_name,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE month_code >= '$month_search' AND month_code < '$month_current'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.supplier_id "; // adding filter selection - filter by a specific supplier - getting the data from historical 

        $i = 0;      
        foreach ($conn->query($q_supplier) as $dt_sup) {
          $supplier_id = $dt_sup['supplier_id'];
          $supplier_name = $dt_sup['supplier_name'];
          $number_meet_duration = $dt_sup['number_meet_duration'];
          $total_number_delivery = $dt_sup['number_delivery'];

          $supplier_perc =0;
          if ($number_meet_duration > 0){
            $supplier_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
          }
          $i = $i +1;
          $point = [1,$i,$supplier_name,$supplier_perc];
          array_push($data_points, $point);
        }    

  }elseif($time_frame == "quarter"){
    $date_now = date("Y-m-d", strtotime("now"));
    $date_search = date("Y-m-d", strtotime("+3 month"));
    $search_month = date("n", strtotime("$date_search"));
    if ($search_month > 9) {
      $year = date("y", strtotime("$date_search"));
      $quarter = 4 ;
    }elseif ($search_month > 6) {
      $year = date("y", strtotime("$date_search"));
      $quarter = 3 ;
    }elseif ($search_month > 3) {
      $year = date("y", strtotime("$date_search"));
      $quarter = 2 ;
    }else{
      $year = date("y", strtotime("$date_search"));
      $quarter = 1 ;
    }
    $quarter_end = $year . $quarter;

    $date_search = date("Y-m-d", strtotime("$date_now - 12 months"));
    $search_month = date("n", strtotime("$date_search"));
    if ($search_month > 9) {
      $year = date("y", strtotime("$date_search"));
      $quarter = 4 ;
    }elseif ($search_month > 6) {
      $year = date("y", strtotime("$date_search"));
      $quarter = 3 ;
    }elseif ($search_month > 3) {
      $year = date("y", strtotime("$date_search"));
      $quarter = 2 ;
    }else{
      $year = date("y", strtotime("$date_search"));
      $quarter = 1 ;
    }
    $quarter_start = $year . $quarter;

    $filter_selection = "";
    // Need to do double filter
      if ($filter_location != "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
      // filter on location only
      elseif ($filter_location != "all" and $filter_supplier == "all"){
        $filter_selection = " AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
    // filter on supplier only
      elseif ($filter_location == "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' ";
      }

      $data_points = [];
      $q_location = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.quarter, historical_performance.quarter_code,supplier_lorry.supplier_name,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE quarter_code >= '$quarter_start' AND quarter_code < '$quarter_end'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.geofence_checkpoint_id "; // adding filter selection - filter by a specific supplier - getting the data from historical graph if it exists
      //echo "$q_perf </br>";
       $i = 0;
        foreach ($conn->query($q_location) as $dt_loc) {
          $geofence_checkpoint_id = $dt_loc['geofence_checkpoint_id'];
          $geofence_checkpoint_description = $dt_loc['geofence_checkpoint_description'];
          $number_meet_duration = $dt_loc['number_meet_duration'];
          $total_number_delivery = $dt_loc['number_delivery'];
           $location_perc = 0;
            if ($number_meet_duration > 0){
              $location_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
            }
            $i = $i +1;
            $point = [0,$i,$geofence_checkpoint_description,$location_perc];
            array_push($data_points, $point);
        }
      $q_supplier = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.quarter,
                    historical_performance.quarter_code,supplier_lorry.supplier_name,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE quarter_code >= '$quarter_start' AND quarter_code <= '$quarter_end'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.supplier_id "; // adding filter selection - filter by a specific supplier - getting the data from historical 

        $i = 0;      
        foreach ($conn->query($q_supplier) as $dt_sup) {
          $supplier_id = $dt_sup['supplier_id'];
          $supplier_name = $dt_sup['supplier_name'];
          $number_meet_duration = $dt_sup['number_meet_duration'];
          $total_number_delivery = $dt_sup['number_delivery'];

          $supplier_perc =0;
          if ($number_meet_duration > 0){
            $supplier_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
          }
          $i = $i +1;
          $point = [1,$i,$supplier_name,$supplier_perc];
          array_push($data_points, $point);
        }    

    //data quarter 
  }elseif($time_frame == "year"){
    $year_search = date("Y", strtotime("- 5 years"));
    $year_current = date("Y", strtotime("Now"));
    $filter_selection = "";
    // Need to do double filter
      if ($filter_location != "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
      // filter on location only
      elseif ($filter_location != "all" and $filter_supplier == "all"){
        $filter_selection = " AND historical_performance.geofence_checkpoint_id = '$filter_location'";
      }
    // filter on supplier only
      elseif ($filter_location == "all" and $filter_supplier != "all"){
        $filter_selection = " AND historical_performance.supplier_id = '$filter_supplier' ";
      }

      $data_points = [];
      $q_location = "SELECT incoming_lorry_gps.historical_performance.geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.year,supplier_lorry.supplier_name,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE year >= '$year_search' AND year <= '$year_current'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.geofence_checkpoint_id "; // adding filter selection - filter by a specific supplier - getting the data from historical graph if it exists
      //echo "$q_perf </br>";
       $i = 0;
        foreach ($conn->query($q_location) as $dt_loc) {
          $geofence_checkpoint_id = $dt_loc['geofence_checkpoint_id'];
          $geofence_checkpoint_description = $dt_loc['geofence_checkpoint_description'];
          $number_meet_duration = $dt_loc['number_meet_duration'];
          $total_number_delivery = $dt_loc['number_delivery'];
           $location_perc = 0;
            if ($number_meet_duration > 0){
              $location_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
            }
            $i = $i +1;
            $point = [0,$i,$geofence_checkpoint_description,$location_perc];
            array_push($data_points, $point);
        }
      $q_supplier = "SELECT incoming_lorry_gps.historical_performance   .geofence_checkpoint_id,historical_performance.supplier_id,historical_performance.year,supplier_lorry.supplier_name,geofence_checkpoint.geofence_checkpoint_description,
                    sum(historical_performance.number_delivery) as number_delivery, 
                    sum(historical_performance.number_meet_duration) as number_meet_duration 
                    FROM incoming_lorry_gps.historical_performance 
                    join incoming_lorry_gps.geofence_checkpoint
                      on incoming_lorry_gps.historical_performance.geofence_checkpoint_id = incoming_lorry_gps.geofence_checkpoint.geofence_checkpoint_id
                    join incoming_lorry_gps.supplier_lorry
                      on incoming_lorry_gps.historical_performance.supplier_id = incoming_lorry_gps.supplier_lorry.supplier_id
                    WHERE year >= '$year_search' AND year <= '$year_current'
                    and historical_performance.geofence_checkpoint_id like 'P%'and historical_performance.geofence_checkpoint_id <> 'P0000006' and historical_performance.geofence_checkpoint_id <> 'P0000007' $filter_selection
                    GROUP BY incoming_lorry_gps.historical_performance.supplier_id "; // adding filter selection - filter by a specific supplier - getting the data from historical 

        $i = 0;      
        foreach ($conn->query($q_supplier) as $dt_sup) {
          $supplier_id = $dt_sup['supplier_id'];
          $supplier_name = $dt_sup['supplier_name'];
          $number_meet_duration = $dt_sup['number_meet_duration'];
          $total_number_delivery = $dt_sup['number_delivery'];

          $supplier_perc =0;
          if ($number_meet_duration > 0){
            $supplier_perc = number_format( $number_meet_duration / $total_number_delivery * 100, 0);
          }
          $i = $i +1;
          $point = [1,$i,$supplier_name,$supplier_perc];
          array_push($data_points, $point);
        }    
  } 

  echo json_encode($data_points, JSON_NUMERIC_CHECK);

?>