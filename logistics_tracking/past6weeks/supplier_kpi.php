<?php
  include "config/koneksi pdo.php";
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


  $q_find_supplier_perf = "SELECT `supplier_name`,supplier_id, COUNT(*) AS total, 
  SUM(CASE WHEN duration_actual > 180 then 1 else 0 end) AS 'supplier_base_KPI', 

  SUM(CASE WHEN duration_actual <= duration_target AND duration_actual > 180 AND geofence_checkpoint_id <> 'P0000001' then 1 else 0 end) AS 'supplier_KPI_withoutWA', 

  SUM(CASE WHEN duration_actual <= 300 AND duration_actual > 180 AND geofence_checkpoint_id = 'P0000001' then 1 else 0 end) AS 'supplier_KPI_withtWA' 
          FROM records_checkpoints
          WHERE WEEK(time_entrance) >=" . $past6week . " AND geofence_checkpoint_id LIKE 'P%' AND remark <> 'System Issue'
          GROUP BY supplier_name";

  $result = $conn->query($q_find_supplier_perf); 
  $data = array();

  foreach ($result as $row) {

    $data[] = $row;
  } 
  $supplier_KPI_values = array();

  $json_encoded = json_encode($data);
  $decoded = json_decode($json_encoded);
  foreach($decoded as $row){
     $supplier_name = $row->supplier_name;
     $supplier_base_KPI = $row->supplier_base_KPI;
     $supplier_KPI_withoutWA = $row->supplier_KPI_withoutWA;
     $supplier_KPI_withtWA = $row->supplier_KPI_withtWA;
     $supplier_KPI = (($supplier_KPI_withoutWA+$supplier_KPI_withtWA)/$supplier_base_KPI)*100;
     //do something with it
     if (($supplier_name == 'Philips')| ($supplier_name == 'PT. Philips')| ($supplier_name == 'LSP')){

     }
     else{
          $supplier_KPI_values[] = array($supplier_name, $supplier_KPI);
     }
       //echo "$supplier_name $supplier_KPI"; 
  }
  echo json_encode($supplier_KPI_values);
?>

