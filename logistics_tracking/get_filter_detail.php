  <?php
		include 'config/koneksi pdo.php';

		if(!empty($_POST["filter"])) {
			$filter = $_POST["filter"];
			if ($filter == "supplier") {
			 	$q_supplier = "SELECT DISTINCT supplier_name, supplier_id FROM supplier_lorry WHERE supplier_id != 'PHI000001'and supplier_id != 'P00000001' and supplier_id != '100175245'and supplier_id != '100175321' and supplier_id != '100175327'and supplier_id != 'ESS00001' order by supplier_name";

			 	echo "
			 		<select id='filter_detail_select' onchange='get_update_filter_detail()'>
			 			<option value=''>Select Supplier</option>
			 	";

	      foreach ($conn->query($q_supplier) as $dt_supplier) {
	        $supplier_name  = $dt_supplier['supplier_name'];
	        $supplier_id = $dt_supplier['supplier_id'];

	        echo "<option value='$supplier_id'>$supplier_name</option>";
	      }
	      echo "</select>";
			}elseif ($filter == "location") {
				$q_supplier = "SELECT * FROM geofence_checkpoint WHERE geofence_checkpoint_id != 'P0000007' and geofence_main_type = 'Philips'" ;

			 	echo "
			 		<select id='filter_detail_select' onchange='get_update_filter_detail()'>
			 			<option value=''>Select Location</option>
			 	";
	      foreach ($conn->query($q_supplier) as $dt_supplier) {
	        $geofence_checkpoint_id  = $dt_supplier['geofence_checkpoint_id'];
	        $geofence_checkpoint_description = $dt_supplier['geofence_checkpoint_description'];

	        echo "<option value='$geofence_checkpoint_id'>$geofence_checkpoint_description</option>";
	      }
	      echo "</select>";
			}		  
		}
	?>
 
