<?php	
  date_default_timezone_set("Asia/Jakarta");
  $date_now = "2021-11-17";
  $date_search = $date_now;
  $search_month = date("n", strtotime("$date_search"));
 	if ($search_month > 9) {
 		$year = date("y", strtotime("$date_search"));
 		$quarter = 3 ;
 	}elseif ($search_month > 6) {
 		$year = date("y", strtotime("$date_search"));
 		$quarter = 2 ;
 	}elseif ($search_month > 3) {
 		$year = date("y", strtotime("$date_search"));
 		$quarter = 1 ;
 	}else{
 		$year = date("y", strtotime("$date_search - 3 month"));
 		$quarter = 4 ;
 	}
 	$quarter_end = $year . $quarter;
 	echo "date $date_search year $year quarter $quarter quarter_end $quarter_end </br>";

 	$date_search = date("Y-m-d", strtotime("$date_now - 15 months"));
  $search_month = date("n", strtotime("$date_search"));
 	if ($search_month > 9) {
 		$year = date("y", strtotime("$date_search"));
 		$quarter = 3 ;
 	}elseif ($search_month > 6) {
 		$year = date("y", strtotime("$date_search"));
 		$quarter = 2 ;
 	}elseif ($search_month > 3) {
 		$year = date("y", strtotime("$date_search"));
 		$quarter = 1 ;
 	}else{
 		$year = date("y", strtotime("$date_search - 3 month"));
 		$quarter = 4 ;
 	}
 	$quarter_start = $year . $quarter;
 	echo "date $date_search year $year quarter $quarter quarter_end $quarter_start </br></br>";

 	for ($i=5; $i >= 0; $i--) { 
 		$month_multiplication = $i * 3 ;
 		$date_search = date("Y-m-d", strtotime("$date_now - $month_multiplication months"));
	  $search_month = date("n", strtotime("$date_search"));
	 	if ($search_month > 9) {
	 		$year = date("y", strtotime("$date_search"));
	 		$quarter = 3 ;
	 	}elseif ($search_month > 6) {
	 		$year = date("y", strtotime("$date_search"));
	 		$quarter = 2 ;
	 	}elseif ($search_month > 3) {
	 		$year = date("y", strtotime("$date_search"));
	 		$quarter = 1 ;
	 	}else{
	 		$year = date("y", strtotime("$date_search - 3 month"));
	 		$quarter = 4 ;
	 	}
	 	$quarter_data = $year . $quarter;
	 	echo "date $date_search year $year quarter $quarter quarter_end $quarter_data</br>";
 	}


?>