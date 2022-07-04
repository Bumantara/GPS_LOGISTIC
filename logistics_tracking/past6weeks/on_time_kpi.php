<?php
  include "config/koneksi pdo.php";
  header('Content-Type: application/json');
  $datetime = date("Y-m-d H:i:s");
  $pastdatetime = date("Y-m-d H:i:s",strtotime("-49 days"));
  $past6week = date("W",strtotime("-6 weeks"));
  $week1 = date("W",strtotime("-6 weeks"));
  $week2 = date("W",strtotime("-5 weeks"));
  $week3 = date("W",strtotime("-4 weeks"));
  $week4 = date("W",strtotime("-3 weeks"));
  $week5 = date("W",strtotime("-2 weeks"));
  $week6 = date("W",strtotime("-1 weeks"));
  header('Content-Type: application/json');

              // Finding on time delivery -- will need to only count those at the waiting area in the future 
  $q_on_time_KPI = "SELECT WEEK(`time_entrance`) AS week_name,
      COUNT(*) AS total,
      SUM(CASE WHEN `on_time` = 'On Time' AND duration_actual > 30 then 1 else 0 end) AS 'on_time_by_week', 
      SUM(CASE WHEN duration_actual > 30 then 1 else 0 end) AS 'ontime_base_KPI'
      FROM records_checkpoints
      WHERE WEEK(`time_entrance`) >= " . $past6week . " AND geofence_checkpoint_id LIKE 'P%' AND remark <> 'System Issue' AND  geofence_checkpoint_id <> 'P0000001' AND  geofence_checkpoint_id <> 'P0000006'AND  geofence_checkpoint_id <> 'P0000007' AND supplier_id <> 'PHI000001' AND supplier_id <> 'P00000001' and supplier_id <> 'LSP000001'
      GROUP BY week_name;";

  $result = $conn->query($q_on_time_KPI); 
  $data = array();

  foreach ($result as $row) {

    $data[] = $row;
  } 

  $ontime_KPI_values = array();

  $json_encoded = json_encode($data);
  $decoded = json_decode($json_encoded);

  foreach($decoded as $row){
     $week_name = $row->week_name;
     $ontime_base_KPI = $row->ontime_base_KPI;
     $on_time_by_week = $row->on_time_by_week;
     $ontime_KPI = ($on_time_by_week/$ontime_base_KPI)*100;
     //do something with it
     
    $ontime_KPI_values[] = array($week_name, $ontime_KPI);
       //echo "$supplier_name $supplier_KPI"; 
  }

  echo json_encode($ontime_KPI_values);
?>

