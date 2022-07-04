<?php

function release_schedule($bg_id)
{
    include "../config/koneksi_pdo.php";
    date_default_timezone_set("Asia/Jakarta");
    $date_time_now = date("Y-m-d H:i:s");
    $date_time_start = date("Y-m-d H:i:s", strtotime("07:00:00"));
    $today_date = date("Y-m-d");
    $timespannow = strtotime("Now");
    $time_to_run = $timespannow - 900;

    $current_hour = date("H");
    if ($current_hour >= "07") {
      $timeStartDay = date("Y-m-d 07:00:00");
      $timeEndDay = date("Y-m-d H:i:s", strtotime("+ 1 days"));
    } else {
      $timeStartDay = date("Y-m-d 07:00:00", strtotime("- 1 days"));
      $timeEndDay = date("Y-m-d 07:00:00");
    }

    
    // create query
    $q_schedule = "SELECT runway_cell.model_id as model_id, runway_cell.runway_name as runway_name, runway_cell.line_model as line_model, schedule.product_12nc as product_12nc, schedule.qty as qty, schedule.po as po, schedule_weekly.time_start_release as time_start_release, schedule_weekly.time_start_actual as time_start_actual,schedule_weekly.remark_description as remark_description, schedule_weekly.remark_id as remark_id, schedule_weekly.timespan_start_release as timespan_start_release, schedule_weekly.timespan_start_actual as timespan_start_actual,
    case when (timespan_start_actual - 900) >= timespan_start_release then 'true' end as late,
    case when time_start_actual is null then 'true' end as delay
    FROM schedule 
    INNER JOIN runway_cell ON schedule.runway_id = runway_cell.runway_id 
    LEFT JOIN schedule_weekly on schedule.po = schedule_weekly.po
    where (time_start_release >= '$timeStartDay' and time_start_release < '$date_time_now' and '$time_to_run' > timespan_start_release ) AND runway_cell.bg_id = '$bg_id' ORDER BY time_start_release, seq";

    foreach ($conn->query($q_schedule) as $dt_schedule) {
        $runaway = $dt_schedule["runway_name"];
        $linemodel = $dt_schedule["line_model"];
        $supplier_name = $boxData["Supplier_name"];
        $vehicle_number = $boxData["Vehicle_number"];
        $current_position_name =$boxData["Location"];
        $release_plan_start = $dt_schedule["time_start_release"];
        $timespan_start_release = $dt_schedule["timespan_start_release"];
        $timespan_start_actual = $dt_schedule["timespan_start_actual"];
        // $release_plan_start = $dt_schedule["time_start_release"];
        $release_plan_start = date("d-M H:i", strtotime($release_plan_start));
        // $release_plan_start = date("Y-m-d H:i:s", strtotime($release_plan_start));
        $actual_start = $dt_schedule["time_start_actual"];
        if ($actual_start == "") {
            $actual_start = $dt_schedule["time_start_actual"];
        } else {
            $actual_start = date("d-M H:i", strtotime($actual_start));
        }
        $remark_show = $boxData['Remark'];
        $remarks_id = $dt_schedule["remark_id"];
        $runway_name = $linemodel . " " . $runaway;
        $Remark =$boxData['Scheduled'];
        $status_class = "class='bg-success'";
        // if ($remarks != "" OR $actual_start == "") {
        //     $status = "DELAY";
        //     $status_class = "class='bg-warning'";
        // }
        // if ($remarks != "" and $remarks_id != 100 OR $actual_start == "") {
        //     $status = "DELAY";
        //     $status_class = "class='bg-warning'";
        // }

        if ((($timespan_start_actual - 900) > $timespan_start_release) OR $actual_start == "") {
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
