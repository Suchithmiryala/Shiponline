<!--Web Application Development COS80021 - Assignment1 - Phu Dao 101335460 -->

<!DOCTYPE html>
<html>
<head>
	<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
	 <!--Import Google Icon Font-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<title>ShipOnline</title>
</head>
<body>
	<div class="container">
		<h2 class="header center green-text">ShipOnline Admin Page</h2>

		<form>
			<fieldset>
				<p>
					<label for="retrieve_date">Date to Retrieve</label>
					<select name="day" required="required">
						<option value="">Day</option>
<?php
for ($i=1; $i < 32; $i++) { 
	echo "<option value='". $i. "'>".$i."</option>";
}
?>	
				</select>
				<select name="month" required="required">
					<option value="">Month</option>
					<option value="1">January</option>
					<option value="2">February</option>
					<option value="3">March</option>
					<option value="4">April</option>
					<option value="5">May</option>
					<option value="6">June</option>
					<option value="7">July</option>
					<option value="8">August</option>
					<option value="9">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select>
				<select name="year" required="required">
					<option value="">Year</option>
<?php 
$inputYear = date("Y");
$nextYear = strtotime("next year");
$nextYear = date("Y", $nextYear);
echo "<option value='".$inputYear."'>".$inputYear."</option>";
echo "<option value='".$nextYear."'>".$nextYear."</option>";
?>
				</select>
			</p>
			<p>
				<input type="radio" name="retrieve_type" class="choice" value="request_date" required="required" checked="checked" />Request Date
				<input type="radio" name="retrieve_type" class="choice" value="pickup_date" required="required" checked="checked" />
				Pick-up Date
			</p>
			<p>
				<input type="submit" value="Show" />
			</p>
			
		</fieldset>
	</form>
	<br>

<?php
require_once("settings.php");	//connection info
$conn = @mysqli_connect($host,
	$user,
	$pwd,
	$sql_db
);
$errMsg = "";

function validateDate($day, $month, $year) {
	global $errMsg;
	$valid = true;

	//check if the date is a valid date on the calendar
	if (!checkdate($month, $day, $year)) {
		$errMsg .= "<p>The date is invalid</p>";
		$valid = false;
	}

	return $valid;
}

function displayRequest($date) {
	global $conn;
	if (!$conn) {
		echo "<p>Database connection failure</p>";
	}
	else {
		$query = "SELECT * FROM request WHERE date(request_date) = '$date'" ;
		$result = mysqli_query($conn, $query);
		$row_cnt = $result -> num_rows;

		if ($row_cnt == 0) {
			echo "<p>There are no orders on the selected date</p>";
		}
		else {
			$total_weight = 0;	//get total weight of orders

			echo "<p><strong>Orders for Request Date $date</strong></p>";
			echo "<table width='100%' border='1'>";
			echo "<tr>\n"
				."<th scope=\"col\">Customer ID</th>\n"
				."<th scope=\"col\">Request Number</th>\n"
				."<th scope=\"col\">Item Description</th>\n"
				."<th scope=\"col\">Weight</th>\n"
				."<th scope=\"col\">Pick-up Suburb</th>\n"
				."<th scope=\"col\">Pick-up Date</th>\n"
				."<th scope=\"col\">Delivery Suburb</th>\n"
				."<th scope=\"col\">Delivery State</th>\n"
				."</tr>\n";

			while($row = mysqli_fetch_assoc($result)) {
					echo "<tr>\n";
					echo "<td>", $row["cust_id"],"</td>\n";
					echo "<td>", $row["request_num"],"</td>\n";
					echo "<td>", $row["item_desc"],"</td>\n";
					echo "<td>", $row["weight"],"</td>\n";
					$total_weight += $row["weight"];
					echo "<td>", $row["pickup_suburb"],"</td>\n";
					$pickup_date = explode(",", $row["pickup_date_time"]);
					echo "<td>", $pickup_date[0], "</td>\n";
					echo "<td>", $row["del_suburb"], "</td>\n";
					echo "<td>", $row["del_state"],"</td>\n";
					echo "</tr>\n";
				}
				echo "</table>\n";
				echo "<p>Number of requests: $row_cnt</p>";
				$total_cost = ($row_cnt * 10) + (2 * ($total_weight - $row_cnt * 2));
				$total_cost = number_format((float)$total_cost, 2, '.', '');
				echo "<p>Total revenue: $". $total_cost;
				mysqli_free_result($result);
			
		}
	}
}

function displayPickup($date) {
	global $conn;
	if (!$conn) {
		echo "<p>Database connection failure</p>";
	}
	else {
		$query = "SELECT c.cust_id, c.name, c.phone, r.request_num, r.item_desc, r.weight, r.pickup_addr, r.pickup_suburb, r.pickup_date_time, r.del_suburb, r.del_state FROM customers c, request r WHERE c.cust_id = r.cust_id and date(pickup_date_time) = '$date' ORDER BY r.pickup_suburb, r.del_state, r.del_suburb"; 
		$result = mysqli_query($conn, $query); 
		$row_cnt = $result -> num_rows;

		if ($row_cnt == 0) {
			echo "<p>There are no orders on the selected date</p>";
		}
		else {
			$total_weight = 0;	//get total weight of orders

			echo "<p><strong>Orders for Pick-up Date $date</strong></p>";
			echo "<table width='100%' border='1'>";
			echo "<tr>\n"
				."<th scope=\"col\">Customer ID</th>\n"
				."<th scope=\"col\">Customer Name</th>\n"
				."<th scope=\"col\">Contact Phone</th>\n"
				."<th scope=\"col\">Request Number</th>\n"
				."<th scope=\"col\">Item Description</th>\n"
				."<th scope=\"col\">Weight</th>\n"
				."<th scope=\"col\">Pick-up Address</th>\n"
				."<th scope=\"col\">Pick-up Suburb</th>\n"
				."<th scope=\"col\">Pick-up Time</th>\n"
				."<th scope=\"col\">Delivery Suburb</th>\n"
				."<th scope=\"col\">Delivery State</th>\n"
				."</tr>\n";

			while($row = mysqli_fetch_assoc($result)) {
					echo "<tr>\n";
					echo "<td>", $row["cust_id"],"</td>\n";
					echo "<td>", $row["name"],"</td>\n";
					echo "<td>", $row["phone"],"</td>\n";
					echo "<td>", $row["request_num"],"</td>\n";
					echo "<td>", $row["item_desc"],"</td>\n";
					echo "<td>", $row["weight"],"</td>\n";
					$total_weight += $row["weight"];
					echo "<td>", $row["pickup_addr"],"</td>\n";
					echo "<td>", $row["pickup_suburb"],"</td>\n";
					$pickup_date_time = explode(",", $row["pickup_date_time"]);
					echo "<td>", $pickup_date_time[1], "</td>\n";
					echo "<td>", $row["del_suburb"], "</td>\n";
					echo "<td>", $row["del_state"],"</td>\n";
					echo "</tr>\n";
				}
				echo "</table>\n";
				echo "<p>Number of requests: $row_cnt</p>";	
				echo "<p>Total weight of requests: ".$total_weight. "kg</p>";									
		}
	}
}

function main() {
	global $errMsg;

	if (isset($_GET["day"])) {
		$day = $_GET["day"];
		$month = $_GET["month"];
		$year = $_GET["year"];
		$retrieve_type = $_GET["retrieve_type"];

		if (!validateDate($day, $month, $year)) {
			echo "<font color='red'>".$errMsg."</font>";
		}
		else {
			//format the date for database
			if ($day < 10) {
				$day = str_pad($day, 2, "0", STR_PAD_LEFT);
			}
			if ($month < 10) {
				$month = str_pad($month, 2, "0", STR_PAD_LEFT);
			}
			$date = $year."-".$month."-".$day;

			if ($retrieve_type == "request_date") {
				displayRequest($date);
			}
			else {
				displayPickup($date);
			}
		}

	}
}

main();

?>

		<p><a href="shiponline.php">Home</a></p>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>	
	<script type="text/javascript" src="js/materialize.min.js"></script>
	<script>
		$(document).ready(function() {
			$('select').formSelect();
			});
	</script>	
</body>
</html>