
<?php
echo "";
include_once 'guid.php'; 
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // get posted data
  $data = json_decode(file_get_contents("php://input"));
  date_default_timezone_set("Asia/Jakarta");
  $datetime =  date('Y-m-d H:i:s');
  $guid = GUID();

  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    $responseAPI=
      [
        "statusCode" => "401",
        "statusMessage" => "Unauthorized: Missing or invalid authentication token.",
        "timestamp" => "$datetime",
        "data" => "no data",
        "locationNAme" =>"",
        "IoAvailability" =>"",
        "IotTotal" => ""
      ];
  } elseif ($_SERVER['PHP_AUTH_USER'] == "user" && $_SERVER['PHP_AUTH_PW'] == "user") {
    $responseAPI=
      [
        "statusCode" => "200",
        "statusMessage" => "succeed.",
        "timestamp" => "$datetime",
        "guid" => "$guid",
        "data" => [],
      ];
    $data_detail = [
      'Business_Group' => 'GC',
      'Count_on_time' => '30',
      'Count_late' => '10',
      ];
    array_push( $responseAPI["data"],  $data_detail);

     $data_detail = [
    'Business_Group' => 'MG',
      'Count_on_time' => '30',
      'Count_late' => '10',
      ];
    array_push( $responseAPI["data"],  $data_detail);

     $data_detail = [
      'Business_Group' => 'MCC',
      'Count_on_time' => '30',
      'Count_late' => '10',
      ];
    array_push( $responseAPI["data"],  $data_detail);

     $data_detail = [
      'Business_Group' => 'OHC',
      'Count_on_time' => '30',
      'Count_late' => '10',
      ];
    array_push( $responseAPI["data"],  $data_detail);

  }else{
    $responseAPI=
      [
        "statusCode" => "401",
        "statusMessage" => "Unauthorized: invalid username.",
        "timestamp" => "$datetime",
        "data" => "no data",
         "locationNAme" =>"",
        "IoAvailability" =>"",
        "IotTotal" => ""
      ];
  }
  

  $data = json_encode($responseAPI);
  echo $data
?>
