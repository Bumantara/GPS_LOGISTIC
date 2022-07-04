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

  $start_search = date("Y-m-d 00:00:00");
  $end_search = date("Y-m-d 00:00:00");
  if ($time_frame == "week") {
    $today = date("D");
    if ($today == "Mon") {
      $start_search = date("Y-m-d 07:00:00", strtotime("MONDAY -1 weeks"));
      $end_search = date("Y-m-d 07:00:00", strtotime("MONDAY"));
    } else {
      $start_search = date("Y-m-d 07:00:00", strtotime("MONDAY -2 Weeks"));
      $end_search = date("Y-m-d 07:00:00", strtotime("MONDAY -1 Weeks"));
    }
  }elseif($time_frame == "month"){    
    $start_search = date("Y-m-01 07:00:00", strtotime("-1 months"));
    $end_search = date("Y-m-01 07:00:00");
  }elseif($time_frame == "quarter"){ 
    $remain_month = date("n", strtotime("2021-10-01")) % 3;
    $start_month = 3 + $remain_month;
    $start_search = date("Y-m-01 07:00:00", strtotime("-$start_month months"));
    $end_search = date("Y-m-01 07:00:00", strtotime("-$remain_month months"));
  }

  $filter_selection_supplier = "";
  if ($filter_supplier != "all"){
    $filter_selection_supplier = " AND supplier_id = '$filter_supplier'";
  }

  $filter_selection_location = "";
  if ($filter_location != "all"){
    $filter_selection_location = " AND geofence_checkpoint_id = '$filter_location'";
  }
  
  $q_pareto = "SELECT case when remark = '' then 'other' else remark end as remark, 
    count(records_checkpoints_id)  as number_fail 
    from incoming_lorry_gps.records_checkpoints rc 
    where time_entrance BETWEEN '$start_search' AND '$end_search' and on_time = 'Not On Time' 
    $filter_selection_supplier $filter_selection_location group by remark order by number_fail desc";

  /*
   $q_pareto = "SELECT case when remark = '' then 'other' else remark end as remark, 
    count(records_checkpoints_id)  as number_fail 
    from incoming_lorry_gps.records_checkpoints rc 
    where time_entrance BETWEEN '$start_search' AND '$end_search' and on_time = 'Not On Time' 
    and remark <> '' filter_selection_supplier $filter_selection_location group by remark order by number_fail desc";
  */
  $total_fail = 0;
  $x_axis = 0;
  $data_points = array();
  $data_percentage = array();
  foreach ($conn->query($q_pareto) as $dt_pareto) {
    $remark = $dt_pareto["remark"];
    $number_fail = $dt_pareto["number_fail"];
    $detail_point = [0, $x_axis , $number_fail, $remark];
    array_push($data_points, $detail_point);

    $detail_percentage = ["x_axis"=>$x_axis, "number_fail" => $number_fail, "remark" => $remark];
    array_push($data_percentage, $detail_percentage);
    $x_axis +=1;
    $total_fail += $number_fail;
  }

  $checkfail = 0;
  foreach ($data_percentage as $dt_pecentage) {
    $remark = $dt_pecentage["remark"];
    $x_axis = $dt_pecentage["x_axis"];
    $number_fail = $dt_pecentage["number_fail"];
    $checkfail += $number_fail;
    $y_axist = round($checkfail / $total_fail * 100);

    $detail_point = [1, $x_axis , $y_axist, $remark];
    array_push($data_points, $detail_point);
  }
  /*
  $data_points = [
    [0, 1, 40 , "Come Early"],
    [0, 2, 12 , "Come Early1"],
    [0, 3, 8 , "Come Early2"],
    [0, 4, 5 , "Come Early3"],
    [0, 5, 4 , "Come Early4"],
    [0, 6, 1 , "Come Early5"],
    [1, 1, 66 , "Come Early"],
    [1, 2, 80 , "Come Early1"],
    [1, 3, 88 , "Come Early2"],
    [1, 4, 92 , "Come Early3"],
    [1, 5, 95 , "Come Early4"],
    [1, 6, 100 , "Come Early5"]
  ];*/
	echo json_encode($data_points, JSON_NUMERIC_CHECK);

?>