<?php
	include "config/koneksi pdo.php"; 
  date_default_timezone_set("Asia/Jakarta");
  $filter= "";  
	if (isset($_GET['filter'])) {  // filter
    $filter=$_GET['filter'];
  }
  $filter_detail= "";  
	if (isset($_GET['filter_detail'])) {    
    $filter_detail=$_GET['filter_detail']; //filter detail - by supplier/location
  }
  $time_frame= "week";  
	if (isset($_GET['time_frame'])) {     
    $time_frame=$_GET['time_frame'];
  }

  $data_points = array(); // creating the final array to be retrieved
  if($time_frame == "week"){
  	$number_week = 12;
  	$week_search = date("yW", strtotime("- 13 Weeks")); // start date
  	$week_current = date("yW", strtotime("Now")); // end date
  	$filter_selection = "";
  	if ($filter == 'supplier') {
  		$filter_selection = " AND supplier_id = '$filter_detail'";
  	}elseif ($filter == 'location') {
  		$filter_selection = " AND geofence_checkpoint_id = '$filter_detail'";
  	}elseif ($filter == "") {
      $filter_selection = " ";
    }
  	$q_perf = "SELECT week_code, year, week,
  		sum(number_delivery) as number_delivery,
  		sum(number_on_time) as number_on_time, 
  		sum(waiting_total) as number_at_waiting_total, 
  		sum(waiting_meet) as number_at_waiting_meet_duration,
  		sum(philips_total) as number_at_philips_total, 
  		sum(philips_meet) as number_at_philips_meet_duration
  		FROM (SELECT week_code, year, week, 
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_delivery ELSE 0 END as waiting_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_meet_duration ELSE 0 END as waiting_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_delivery END as philips_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_meet_duration END as philips_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 'WAITING' ELSE 'PHILIPS' END as checkpoint_area,
  		number_delivery, number_on_time
  		FROM historical_performance 
  		WHERE week_code >= '$week_search' AND week_code < '$week_current' $filter_selection ) 
			graph_tabel GROUP BY week_code"; // adding filter selection - filter by a specific supplier - getting the data from historical graph if it exists
  	//echo "$q_perf </br>";
  	$dt_hystory = [];
  	foreach ($conn->query($q_perf) as $dt_perf) {
  		$week_code = $dt_perf["week_code"]; //week code is like 2134
  		$year = $dt_perf["year"];
  		$week = $dt_perf["week"];
  		$number_on_time = $dt_perf["number_on_time"];
  		$number_delivery = $dt_perf["number_delivery"];
  		$number_at_waiting_meet_duration = $dt_perf["number_at_waiting_meet_duration"];
  		$number_at_waiting_total = $dt_perf["number_at_waiting_total"];
  		$number_at_philips_meet_duration = $dt_perf["number_at_philips_meet_duration"];
  		$number_at_philips_total = $dt_perf["number_at_philips_total"];
  		$dt_hystory[$week_code] = [
				"week_code" => $week_code,
				"year" => $year,
				"week" => $week,
				"number_on_time" => $number_on_time,
				"number_delivery" => $number_delivery,
				"number_at_waiting_meet_duration" => $number_at_waiting_meet_duration,
				"number_at_waiting_total" => $number_at_waiting_total,
				"number_at_philips_meet_duration" => $number_at_philips_meet_duration,
				"number_at_philips_total" => $number_at_philips_total,  				
			];
  	}

  	//echo json_encode($dt_hystory, JSON_NUMERIC_CHECK);
  	for ($i= 0; $i <= $number_week -1  ; $i++) { // $number_week = 12 
  		$data_number = $number_week - $i; //data number refers to the changing callback of the W40 number
      // e.g. when i == 2 , datanum = 12-2+2 = 12 -12 week
      // e.g. i == 3, datanum = 12-3+2 =11 -11week  	 
  		$week_number  = "W". date("W", strtotime("- $data_number Weeks")); //W40 format
  		$year_week = date("yW", strtotime("- $data_number Weeks")); // W2143
  		//echo "$year_week </br>";
  		if (array_key_exists($year_week, $dt_hystory)) { //if the yearweek code exists in the week_code of dy_hystory that stores the SQL query
				$number_on_time = $dt_hystory[$year_week]["number_on_time"];
	  		$number_delivery = $dt_hystory[$year_week]["number_delivery"];
	  		$number_at_waiting_meet_duration = $dt_hystory[$year_week]["number_at_waiting_meet_duration"];
	  		$number_at_waiting_total = $dt_hystory[$year_week]["number_at_waiting_total"];
	  		$number_at_philips_meet_duration = $dt_hystory[$year_week]["number_at_philips_meet_duration"];
	  		$number_at_philips_total = $dt_hystory[$year_week]["number_at_philips_total"];
			}else{ // when the array key does not exist - this means historical perf did not exist yet
				//$find_week_date = $data_number + 1;
        $find_week_date = $data_number;

  			$today = date("D");
        if ($today == "Mon") {
        	$find_week_date -= 1 ;
        }

				$start_date = date("Y-m-d", strtotime("last monday - $find_week_date Weeks"));
				
				$number_on_time = 0;
	  		$number_delivery = 0;
	  		$number_at_waiting_meet_duration = 0;
	  		$number_at_waiting_total = 0;
	  		$number_at_philips_meet_duration =  0;
	  		$number_at_philips_total =  0;
	  		//data  

	  		if ($filter == 'all' and $time_frame == 'week') {
	  			for ($j=0; $j < 7; $j++) { // all 7 days
		  			$date_to_search = date("Y-m-d", strtotime("$start_date + $j days"));
		  			$q_transfer = "SELECT supplier_id, geofence_checkpoint_id, count(records_checkpoints_id) as number_delivery,
			  			sum(CASE WHEN on_time = 'On Time' THEN 1 else 0 END) as number_on_time,
			  			sum(CASE 
				  				WHEN geofence_checkpoint_id = 'P0000001'  AND duration_actual <= 600  THEN 1 
				  				WHEN geofence_checkpoint_id != 'P0000001' AND duration_actual < duration_target THEN 1
				  				else 0
				  			END) as number_meet_duration
			  			FROM records_checkpoints
              WHERE time_entrance LIKE '$date_to_search%' AND geofence_checkpoint_id LIKE 'P%' AND remark <> 'System Issue' AND  geofence_checkpoint_id <> 'P0000006'AND  geofence_checkpoint_id <> 'P0000007' AND supplier_id <> 'PHI000001' AND supplier_id <> 'P00000001' and supplier_id <> 'LSP000001'
			  			GROUP BY supplier_id, geofence_checkpoint_id";
              // getting from records checkpoint database to update it into the historical performance graph
			  		foreach ($conn->query($q_transfer) as $dt_transfer) {
			  			$supplier_id = $dt_transfer["supplier_id"];
			  			$geofence_checkpoint_id = $dt_transfer["geofence_checkpoint_id"];
			  			$number_delivery_data = $dt_transfer["number_delivery"];
			  			$number_on_time_data = $dt_transfer["number_on_time"];
			  			$number_meet_duration = $dt_transfer["number_meet_duration"];

			  			$historical_performance_id = date("Ymd", strtotime($date_to_search)) . 
			  				$geofence_checkpoint_id . $supplier_id;
			  			$week = date("W", strtotime($date_to_search));
			  			$month = date("n", strtotime($date_to_search));
			  			$year = date("Y", strtotime($date_to_search));
			  			if ($month <= 3) {
			  				$quarter = 1; // categorising the quarter code
			  			}elseif ($month <= 6) {
			  				$quarter = 2;
			  			}elseif ($month <= 9) {
			  				$quarter = 3;
			  			}elseif ($month <= 12) {
			  				$quarter = 4;
			  			}

			  			$week_code = date("yW", strtotime($start_date)); // W2142
			  			$month_code = date("ym", strtotime($start_date)); // M2108 --> august of 2021
			  			$quarter_code = date("y", strtotime($start_date)) . $quarter;
			  			$q_insert_perf = "INSERT INTO historical_performance 
			  			(historical_performance_id, supplier_id, geofence_checkpoint_id, number_delivery, 
			  			number_on_time, number_meet_duration, date, week, month, quarter, year, 
			  			week_code, month_code, quarter_code)
			  			VALUES('$historical_performance_id', '$supplier_id', '$geofence_checkpoint_id', '$number_delivery_data',
			  			'$number_on_time_data','$number_meet_duration','$date_to_search','$week','$month','$quarter','$year',
			  			'$week_code','$month_code','$quarter_code')";

			  			//echo "$q_insert_perf </br>";	  			
		          $conn->exec($q_insert_perf); 
		          $number_on_time += $number_on_time_data;
				  		$number_delivery += $number_delivery_data;
				  		if ($geofence_checkpoint_id == 'P0000001') {		  			
					  		$number_at_waiting_meet_duration += $number_meet_duration;
					  		$number_at_waiting_total += $number_delivery_data;
				  		}else{
					  		$number_at_philips_meet_duration +=  $number_meet_duration;
					  		$number_at_philips_total +=  $number_delivery_data;	  			
				  		}
			  		}
		  		}
	  		}	  			  		
		}

  		$label = $week_number;
  		$ontime_perc = 0;
  		if ($number_delivery > 0) {
  			$ontime_perc = number_format( $number_on_time / $number_delivery * 100, 0);
  		}

  		$waiting_perc = 0;
  		if ($number_at_waiting_total > 0) {
  			$waiting_perc = number_format( $number_at_waiting_meet_duration / $number_at_waiting_total * 100, 0);
  		}

  		$philips_perc = 0;
  		if ($number_at_philips_total > 0) {
  			$philips_perc = number_format( $number_at_philips_meet_duration / $number_at_philips_total * 100, 0);
  		}

      //$ data points data structure  [graph_selection code,index of value, $percent, week_id]
      // graph selection code -  0 is ontime, 1 is waiting_area_perf, 2 philips_area_perf

  		$point = [0, $i, $ontime_perc ,$label];
  		array_push($data_points, $point);

  		$duration_waiting_perc = rand(10,100);
  		$point = [1, $i, $waiting_perc ,$label];
  		array_push($data_points, $point);

  		$point = [2, $i, $philips_perc ,$label];
  		array_push($data_points, $point);
  	}
  	//data month
  }elseif($time_frame == "month"){
  	$number_month = 12;
  	$month_search = date("ym", strtotime("- 13 month"));
  	$month_current = date("ym", strtotime("Now"));
  	$filter_selection = "";
  	if ($filter == 'supplier') {
  		$filter_selection = " AND supplier_id = '$filter_detail'";
  	}elseif ($filter == 'location') {
  		$filter_selection = " AND geofence_checkpoint_id = '$filter_detail'";
  	}
  	$q_perf = "SELECT month_code, year, month, 
  		sum(number_delivery) as number_delivery,
  		sum(number_on_time) as number_on_time, 
  		sum(waiting_total) as number_at_waiting_total, 
  		sum(waiting_meet) as number_at_waiting_meet_duration,
  		sum(philips_total) as number_at_philips_total, 
  		sum(philips_meet) as number_at_philips_meet_duration
  		FROM (SELECT month_code, year, month, 
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_delivery ELSE 0 END as waiting_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_meet_duration ELSE 0 END as waiting_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_delivery END as philips_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_meet_duration END as philips_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 'WAITING' ELSE 'PHILIPS' END as checkpoint_area,
  		number_delivery, number_on_time
  		FROM historical_performance 
  		WHERE month_code >= '$month_search' AND month_code < '$month_current' $filter_selection ) 
			graph_tabel GROUP BY month_code";

  	$dt_hystory = [];
  	foreach ($conn->query($q_perf) as $dt_perf) {
  		$month_code = $dt_perf["month_code"];
  		$year = $dt_perf["year"];
  		$month = $dt_perf["month"];
  		$number_on_time = $dt_perf["number_on_time"];
  		$number_delivery = $dt_perf["number_delivery"];
  		$number_at_waiting_meet_duration = $dt_perf["number_at_waiting_meet_duration"];
  		$number_at_waiting_total = $dt_perf["number_at_waiting_total"];
  		$number_at_philips_meet_duration = $dt_perf["number_at_philips_meet_duration"];
  		$number_at_philips_total = $dt_perf["number_at_philips_total"];
  		$dt_hystory[$month_code] = [
				"month_code" => $month_code,
				"year" => $year,
				"month" => $month,
				"number_on_time" => $number_on_time,
				"number_delivery" => $number_delivery,
				"number_at_waiting_meet_duration" => $number_at_waiting_meet_duration,
				"number_at_waiting_total" => $number_at_waiting_total,
				"number_at_philips_meet_duration" => $number_at_philips_meet_duration,
				"number_at_philips_total" => $number_at_philips_total,  				
			];
  	}

  	for ($i= 1; $i <= $number_month ; $i++) { 
  		$data_number = $number_month - $i + 1;  		
  		$month_number  = "M". date("m", strtotime("- $data_number months"));
  		$year_month = date("ym", strtotime("- $data_number months"));

  		if (array_key_exists($year_month, $dt_hystory)) {
				$number_on_time = $dt_hystory[$year_month]["number_on_time"];
	  		$number_delivery = $dt_hystory[$year_month]["number_delivery"];
	  		$number_at_waiting_meet_duration = $dt_hystory[$year_month]["number_at_waiting_meet_duration"];
	  		$number_at_waiting_total = $dt_hystory[$year_month]["number_at_waiting_total"];
	  		$number_at_philips_meet_duration = $dt_hystory[$year_month]["number_at_philips_meet_duration"];
	  		$number_at_philips_total = $dt_hystory[$year_month]["number_at_philips_total"];
			}else{
				$number_on_time = 0;
	  		$number_delivery = 0;
	  		$number_at_waiting_meet_duration = 0;
	  		$number_at_waiting_total = 0;
	  		$number_at_philips_meet_duration =  0;
	  		$number_at_philips_total =  0;
			}

  		$label = $month_number;
  		$ontime_perc = 0;
  		if ($number_delivery > 0) {
  			$ontime_perc = number_format( $number_on_time / $number_delivery * 100, 0);
  		}

  		$waiting_perc = 0;
  		if ($number_at_waiting_total > 0) {
  			$waiting_perc = number_format( $number_at_waiting_meet_duration / $number_at_waiting_total * 100, 0);
  		}

  		$philips_perc = 0;
  		if ($number_at_philips_total > 0) {
  			$philips_perc = number_format( $number_at_philips_meet_duration / $number_at_philips_total * 100, 0);
  		}

  		$point = [0, $i, $ontime_perc ,$label];
  		array_push($data_points, $point);

  		$duration_waiting_perc = rand(10,100);
  		$point = [1, $i, $waiting_perc ,$label];
  		array_push($data_points, $point);

  		$point = [2, $i, $philips_perc ,$label];
  		array_push($data_points, $point);
  	}
  	//data quarter 
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
  	if ($filter == 'supplier') {
  		$filter_selection = " AND supplier_id = '$filter_detail'";
  	}elseif ($filter == 'location') {
  		$filter_selection = " AND geofence_checkpoint_id = '$filter_detail'";
  	}

  	$q_perf = "SELECT quarter_code, year, quarter, 
  		sum(number_delivery) as number_delivery,
  		sum(number_on_time) as number_on_time, 
  		sum(waiting_total) as number_at_waiting_total, 
  		sum(waiting_meet) as number_at_waiting_meet_duration,
  		sum(philips_total) as number_at_philips_total, 
  		sum(philips_meet) as number_at_philips_meet_duration
  		FROM (SELECT quarter_code, year, quarter, 
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_delivery ELSE 0 END as waiting_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_meet_duration ELSE 0 END as waiting_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_delivery END as philips_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_meet_duration END as philips_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 'WAITING' ELSE 'PHILIPS' END as checkpoint_area,
  		number_delivery, number_on_time
  		FROM historical_performance 
  		WHERE quarter_code >= '$quarter_start' AND quarter_code <= '$quarter_end' $filter_selection ) 
			graph_tabel GROUP BY quarter_code";

  	$dt_hystory = [];
  	foreach ($conn->query($q_perf) as $dt_perf) {
  		$quarter_code = $dt_perf["quarter_code"];
  		$year = $dt_perf["year"];
  		$quarter = $dt_perf["quarter"];
  		$number_on_time = $dt_perf["number_on_time"];
  		$number_delivery = $dt_perf["number_delivery"];
  		$number_at_waiting_meet_duration = $dt_perf["number_at_waiting_meet_duration"];
  		$number_at_waiting_total = $dt_perf["number_at_waiting_total"];
  		$number_at_philips_meet_duration = $dt_perf["number_at_philips_meet_duration"];
  		$number_at_philips_total = $dt_perf["number_at_philips_total"];
  		$dt_hystory[$quarter_code] = [
				"quarter_code" => $quarter_code,
				"year" => $year,
				"quarter" => $quarter,
				"number_on_time" => $number_on_time,
				"number_delivery" => $number_delivery,
				"number_at_waiting_meet_duration" => $number_at_waiting_meet_duration,
				"number_at_waiting_total" => $number_at_waiting_total,
				"number_at_philips_meet_duration" => $number_at_philips_meet_duration,
				"number_at_philips_total" => $number_at_philips_total,  				
			];
  	}
		for ($i=1; $i <= 6; $i++) { 
		 		$month_multiplication = (6 - $i) * 3 ;
		 		$date_search = date("Y-m-d", strtotime("$date_now - $month_multiplication months"));
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
			 	$quarter_data = $year . $quarter;

  		if (array_key_exists($quarter_data, $dt_hystory)) {
			$number_on_time = $dt_hystory[$quarter_data]["number_on_time"];
	  		$number_delivery = $dt_hystory[$quarter_data]["number_delivery"];
	  		$number_at_waiting_meet_duration = $dt_hystory[$quarter_data]["number_at_waiting_meet_duration"];
	  		$number_at_waiting_total = $dt_hystory[$quarter_data]["number_at_waiting_total"];
	  		$number_at_philips_meet_duration = $dt_hystory[$quarter_data]["number_at_philips_meet_duration"];
	  		$number_at_philips_total = $dt_hystory[$quarter_data]["number_at_philips_total"];
			}else{
			$number_on_time = 0;
	  		$number_delivery = 0;
	  		$number_at_waiting_meet_duration = 0;
	  		$number_at_waiting_total = 0;
	  		$number_at_philips_meet_duration =  0;
	  		$number_at_philips_total =  0;
			}

  		$label = "Q" . $quarter_data;
  		$ontime_perc = 0;
  		if ($number_delivery > 0) {
  			$ontime_perc = number_format( $number_on_time / $number_delivery * 100, 0);
  		}

  		$waiting_perc = 0;
  		if ($number_at_waiting_total > 0) {
  			$waiting_perc = number_format( $number_at_waiting_meet_duration / $number_at_waiting_total * 100, 0);
  		}

  		$philips_perc = 0;
  		if ($number_at_philips_total > 0) {
  			$philips_perc = number_format( $number_at_philips_meet_duration / $number_at_philips_total * 100, 0);
  		}

  		$point = [0, $i, $ontime_perc ,$label];
  		array_push($data_points, $point);

  		$duration_waiting_perc = rand(10,100);
  		$point = [1, $i, $waiting_perc ,$label];
  		array_push($data_points, $point);

  		$point = [2, $i, $philips_perc ,$label];
  		array_push($data_points, $point);
  	}
  	//data quarter 
  }elseif($time_frame == "year"){
  	$year_search = date("Y", strtotime("- 5 years"));
  	$year_current = date("Y", strtotime("Now"));
  	$filter_selection = "";
  	if ($filter == 'supplier') {
  		$filter_selection = " AND supplier_id = '$filter_detail'";
  	}elseif ($filter == 'location') {
  		$filter_selection = " AND geofence_checkpoint_id = '$filter_detail'";
  	}
  	$q_perf = "SELECT year, 
  		sum(number_delivery) as number_delivery,
  		sum(number_on_time) as number_on_time, 
  		sum(waiting_total) as number_at_waiting_total, 
  		sum(waiting_meet) as number_at_waiting_meet_duration,
  		sum(philips_total) as number_at_philips_total, 
  		sum(philips_meet) as number_at_philips_meet_duration
  		FROM (SELECT year,  
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_delivery ELSE 0 END as waiting_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN number_meet_duration ELSE 0 END as waiting_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_delivery END as philips_total,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 0 ELSE number_meet_duration END as philips_meet,
  		CASE WHEN geofence_checkpoint_id = 'P0000001' THEN 'WAITING' ELSE 'PHILIPS' END as checkpoint_area,
  		number_delivery, number_on_time
  		FROM historical_performance 
  		WHERE year >= '$year_search' AND year <= '$year_current' $filter_selection ) 
			graph_tabel GROUP BY year";

  	$dt_hystory = [];
  	foreach ($conn->query($q_perf) as $dt_perf) {
  		$year = $dt_perf["year"];
  		$number_on_time = $dt_perf["number_on_time"];
  		$number_delivery = $dt_perf["number_delivery"];
  		$number_at_waiting_meet_duration = $dt_perf["number_at_waiting_meet_duration"];
  		$number_at_waiting_total = $dt_perf["number_at_waiting_total"];
  		$number_at_philips_meet_duration = $dt_perf["number_at_philips_meet_duration"];
  		$number_at_philips_total = $dt_perf["number_at_philips_total"];
  		$dt_hystory[$year] = [
				"year" => $year,
				"number_on_time" => $number_on_time,
				"number_delivery" => $number_delivery,
				"number_at_waiting_meet_duration" => $number_at_waiting_meet_duration,
				"number_at_waiting_total" => $number_at_waiting_total,
				"number_at_philips_meet_duration" => $number_at_philips_meet_duration,
				"number_at_philips_total" => $number_at_philips_total,  				
			];
  	}

  	for ($i= 1; $i <= 6 ; $i++) { 
  		$data_number = 6 - $i ;  		
  		$year = date("Y", strtotime("- $data_number year"));

  		if (array_key_exists($year, $dt_hystory)) {
				$number_on_time = $dt_hystory[$year]["number_on_time"];
	  		$number_delivery = $dt_hystory[$year]["number_delivery"];
	  		$number_at_waiting_meet_duration = $dt_hystory[$year]["number_at_waiting_meet_duration"];
	  		$number_at_waiting_total = $dt_hystory[$year]["number_at_waiting_total"];
	  		$number_at_philips_meet_duration = $dt_hystory[$year]["number_at_philips_meet_duration"];
	  		$number_at_philips_total = $dt_hystory[$year]["number_at_philips_total"];
			}else{
				$number_on_time = 0;
	  		$number_delivery = 0;
	  		$number_at_waiting_meet_duration = 0;
	  		$number_at_waiting_total = 0;
	  		$number_at_philips_meet_duration =  0;
	  		$number_at_philips_total =  0;
			}

  		$label = $year;
  		$ontime_perc = 0;
  		if ($number_delivery > 0) {
  			$ontime_perc = number_format( $number_on_time / $number_delivery * 100, 0);
  		}

  		$waiting_perc = 0;
  		if ($number_at_waiting_total > 0) {
  			$waiting_perc = number_format( $number_at_waiting_meet_duration / $number_at_waiting_total * 100, 0);
  		}

  		$philips_perc = 0;
  		if ($number_at_philips_total > 0) {
  			$philips_perc = number_format( $number_at_philips_meet_duration / $number_at_philips_total * 100, 0);
  		}

  		$point = [0, $i, $ontime_perc ,$label];
  		array_push($data_points, $point);

  		$duration_waiting_perc = rand(10,100);
  		$point = [1, $i, $waiting_perc ,$label];
  		array_push($data_points, $point);

  		$point = [2, $i, $philips_perc ,$label];
  		array_push($data_points, $point);
  	}
  }	

	echo json_encode($data_points, JSON_NUMERIC_CHECK);

?>