<?php

function release_schedule($bg_id)
{
    include "../../config/koneksi_pdo.php";
    date_default_timezone_set("Asia/Jakarta");
    $date_time_now = date("Y-m-d H:i:s");
    $date_time_start = date("Y-m-d H:i:s", strtotime("07:00:00"));
    $today_date = date("Y-m-d");

    $current_hour = date("H");
    if ($current_hour >= "07") {
      $timeStartDay = date("Y-m-d 07:00:00");
      $timeEndDay = date("Y-m-d 07:00:00", strtotime("+ 1 days"));
    } else {
      $timeStartDay = date("Y-m-d 07:00:00", strtotime("- 1 days"));
      $timeEndDay = date("Y-m-d 07:00:00");
    }

    
    // create query
    $q_schedule = "SELECT runway_cell.model_id as model_id, runway_cell.runway_name as runway_name, runway_cell.line_model as line_model, schedule.product_12nc as product_12nc, schedule.qty as qty, schedule.po as po, schedule_weekly.time_start_release as time_start_release, schedule_weekly.time_start_actual as time_start_actual,schedule_weekly.remark_description as remark_description,
    case when (timespan_start_actual - 900) >= timespan_start_release then 'true' end as late,
    case when time_start_actual is null then 'true' end as delay
    FROM schedule 
    INNER JOIN runway_cell ON schedule.runway_id = runway_cell.runway_id 
    LEFT JOIN schedule_weekly on schedule.po = schedule_weekly.po
    where (time_start_release >= '$date_time_start'  and time_start_release < '$date_time_now') AND runway_cell.bg_id = '$bg_id' having delay is not null or late is not null ORDER BY time_start_release, seq";

    foreach ($conn->query($q_schedule) as $dt_schedule) {
        $runaway = $dt_schedule["runway_name"];
        $linemodel = $dt_schedule["line_model"];
        $prod12nc = $dt_schedule["product_12nc"];
        $qty = $dt_schedule["qty"];
        $po = $dt_schedule["po"];
        $release_plan_start = $dt_schedule["time_start_release"];
        $release_plan_start = $dt_schedule["time_start_release"];
        $release_plan_start = date("d-M H:i", strtotime($release_plan_start));
        // $release_plan_start = date("Y-m-d H:i:s", strtotime($release_plan_start));
        $actual_start = $dt_schedule["time_start_actual"];
        if ($actual_start == "") {
            $actual_start = $dt_schedule["time_start_actual"];
        } else {
            $actual_start = date("d-M H:i", strtotime($actual_start));
        }
        $remarks = $dt_schedule["remark_description"];
        $runway_name = $linemodel . " " . $runaway;
        $status = "LANDED";
        $status_class = "class='bg-success'";
        if ($remarks != "" || $actual_start != "") {
            $status = "DELAY";
            $status_class = "class='bg-warning'";
        }
        

        echo "
        <tr>
            <td>$runway_name</td>
            <td>$prod12nc</td>
            <td>$qty</td>
            <td>$po</td>
            <td>$release_plan_start</td>
            <td>$actual_start</td>
            <td>$remarks</td>
            <td $status_class>$status</td>
        </tr>
        ";
    }
    // extract data
    // fetch data to table

}
