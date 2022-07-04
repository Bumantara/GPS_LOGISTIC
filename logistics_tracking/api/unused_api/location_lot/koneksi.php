<?php

  //echo "$machine_id";
  $servername = "localhost";
  $username = "gps_user";
  $password = "gps_user@123";
  $database = "incoming_lorry_gps";

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     //echo "Connected successfully"; 
      }
  catch(PDOException $e)
      {
      //echo "Connection failed: " . $e->getMessage();
      }

?>