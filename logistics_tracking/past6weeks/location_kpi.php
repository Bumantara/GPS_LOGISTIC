<?php
  include "config/koneksi pdo.php";
  date_default_timezone_set("Asia/Jakarta");
  $filter= "";  
  if (isset($_GET['filter'])) {    
    $filter=$_GET['filter'];
  }
  $filter_detail= "";  
  if (isset($_GET['filter_detail'])) {    
    $filter_detail=$_GET['filter_detail'];
  }
  $time_frame= "week";  
  if (isset($_GET['time_frame'])) {     
    $time_frame=$_GET['time_frame'];
  }
  
  //ilham
  
 
  $past6week = date("W",strtotime("-6 weeks"));
  $data_points = array();
  if($time_frame == "week"){
  $week_search = date("yW", strtotime("-13 weeks"));
  $week_current = date("yW", strtotime("Now"));
  $filter_selection = "";
  if ($filter == 'supplier') {
        $filter_selection = " AND supplier_id = '$filter_detail'";
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
      FROM records_checkpoints 
      WHERE week_code >= '$week_search' AND week_code < '$week_current' $filter_selection ) 
      graph_tabel GROUP BY week_code"; // adding filter selection - filter by a specific supplier
    //echo "$q_perf </br>";

  //melissa program
     $q_find_count_WA_within_KPI = "SELECT `geofence_checkpoint_description`,
      COUNT(*) AS total,
      SUM(CASE WHEN duration_actual > 180  then 1 else 0 end) AS 'location_base_KPI', 
      SUM(CASE WHEN duration_actual <= 300 AND duration_actual > 180 then 1 else 0 end) AS 'location_KPI_withWA'
      FROM records_checkpoints
      WHERE WEEK(time_entrance) >= " . $past6week . " AND remark <> 'System Issue' AND geofence_checkpoint_id = 'P0000001'AND supplier_id <>'PHI000001' AND supplier_id <> 'P00000001' and supplier_id <> 'LSP000001'
      GROUP BY geofence_checkpoint_id;" ;

    $count_WA_within_KPI = $conn->query($q_find_count_WA_within_KPI);
    $data = array();

      foreach ($count_WA_within_KPI as $row){
        $data[] = $row;
      } 

      $location_kpi_values = array();

      $json_encoded = json_encode($data);
      $decoded = json_decode($json_encoded);

      foreach($decoded as $row){
       $geofence_checkpoint_description = $row->geofence_checkpoint_description;
       $location_base_KPI = $row->location_base_KPI;
       $location_KPI_withWA = $row->location_KPI_withWA;
       $location_KPI = ($location_KPI_withWA/$location_base_KPI)*100;
       $location_kpi_values[] = array($geofence_checkpoint_description, $location_KPI);
     }

  $q_location_within_KPI = "SELECT `geofence_checkpoint_description`,
      COUNT(*) AS total,
      SUM(CASE WHEN duration_actual > 180  then 1 else 0 end) AS 'location_base_KPI', 
      SUM(CASE WHEN duration_actual <= duration_target AND duration_actual > 180 then 1 else 0 end) AS 'location_KPI_withoutWA'
      FROM records_checkpoints
      WHERE WEEK(time_entrance) >= " . $past6week . " AND geofence_checkpoint_id LIKE 'P%' AND remark <> 'System Issue' AND  geofence_checkpoint_id <> 'P0000001'AND geofence_checkpoint_id <> 'P0000006' and geofence_checkpoint_id <> 'P0000007' AND supplier_id <>'PHI000001' AND supplier_id <> 'P00000001' and supplier_id <> 'LSP000001'
      GROUP BY geofence_checkpoint_id;";

    $result = $conn->query($q_location_within_KPI);
    $data = array();

    foreach ($result as $row){
      $data[] = $row;
    } 

    $json_encoded = json_encode($data);
    $decoded = json_decode($json_encoded);

    foreach($decoded as $row){
       $geofence_checkpoint_description = $row->geofence_checkpoint_description;
       $location_base_KPI = $row->location_base_KPI;
       $location_KPI_withoutWA = $row->location_KPI_withoutWA;
       $location_KPI = ($location_KPI_withoutWA/$location_base_KPI)*100;

       $location_kpi_values[] = array($geofence_checkpoint_description, $location_KPI);
     }
}
  echo json_encode($location_kpi_values);
?>

