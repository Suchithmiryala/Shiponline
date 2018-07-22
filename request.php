<!DOCTYPE html>
<html>
<head>
	<title>ShipOnline</title>
	<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
	 <!--Import Google Icon Font-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		
</head>
<body>
	<div class="container">
		<h2 class="header center green-text">ShipOnline Request Page</h2>

		<?php
		$cust_id = 0;
		if (isset($_GET['name'])) {
			$name = $_GET['name'];
			$cust_id = $_GET['cust_id'];
			echo "<p><em>Welcome $name</em></p>";
		}
		else {
			$name = "anon";
			$cust_id = 0;
		}
		?>		

		<form>
			<fieldset>
				<legend><strong>Item Information</strong></legend>
				<input type="hidden" name="name" value= <?php echo "'".$name."'"; ?> />					
				<input type="hidden" name="cust_id" value= <?php echo "'".$cust_id."'"; ?> />
				<p>
					<label for="item_desc">Description</label>
					<input type="text" name="item_desc" required="required" size="35" />
				</p>
				<p>
					<label for="weight">Weight</label>
					<select name="weight" required="required">
						<option value="">Please select</option>
						<option value="2">2kg or under</option>
						<?php
							for ($i=3; $i < 21; $i++) { 
								echo "<option value='". $i. "'>".$i."kg</option>";
							}
						?>
					</select>
				</p>		
			</fieldset>
			<fieldset>
				<legend><strong>Pickup Information</strong></legend>
				<p>
					<label for="pickup_addr">Address</label>
					<input type="text" name="pickup_addr" required="required" size="50" />
				</p>
				<p>
					<label for="pickup_suburb">Suburb</label>
					<input type="text" name="pickup_suburb" required="required">
				</p>
				<p>
					<label for="pickup_date">Preferred date</label>
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
							echo "<option value='".$inputYear."'>".$inputYear."</option>";
							echo "<option value='".$nextYear."'>".date("Y", $nextYear)."</option>";
						?>
					</select>
				</p>
				<p>
					<label for="pickup_time">Preferred time</label>
					<select name="hour" required="required">
						<option value="">Hour</option>
						<?php 
							for ($i=7; $i < 21; $i++) { 
								echo "<option value='".$i."'>".$i."</option>";
							}
						?>
					</select>
					<label for="minute">Minute</label>
					<input type="text" name="minute" size="5" pattern="[0-9]{2}" title="invalid time format" />
				</p>
				<p><small><font color="#808080">If no minute value entered, it will be assumed that the pickup time will be on the exact hour</font></small></p>
			</fieldset>
			<fieldset>
				<legend><strong>Delivery Information</strong></legend>
				<p>
					<label for="receiver_name">Receiver Name</label>
					<input type="text" name="receiver_name" required="required" size="35" />
				</p>
				<p>
					<label for="del_addr">Address</label>
					<input type="text" name="del_addr" required="required" size="50" />
				</p>
				<p>
					<label for="del_suburb">Suburb</label>
					<input type="text" name="del_suburb" required="required" size="20" />
				</p>
				<p>
					<label for="del_state">State</label>
					<select name="del_state" required="required">
						<option value="">Select State</option>
						<option value="VIC">VIC</option>
						<option value="NSW">NSW</option>
						<option value="QLD">QLD</option>
						<option value="NT">NT</option>
						<option value="WA">WA</option>
						<option value="SA">SA</option>
						<option value="TAS">TAS</option>
						<option value="ACT">ACT</option>
					</select>
				</p>
			</fieldset>
			
			<p>
				<button class="btn waves-effect waves-light blue darken-4" type="submit" name="action">
					Submit
				</button>
			</p>
		</form>
		<p><a href="shiponline.php">Home</a></p>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>	
	<script type="text/javascript" src="js/materialize.min.js"></script>
	<script>
		$(document).ready(function() {
			$('select').formSelect();
			});
	</script>
	

	<?php
	require_once("settings.php");	//connection info
	$conn = @mysqli_connect($host,
		$user,
		$pwd,
		$sql_db
	);
	$errMsg = "";	//error message to display
	$minute;


	/**
	* Removes unncessary characters 
	*/
	function sanitise_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	/**
	* Validate the pickup date to check that it is within the rules
	*/
	function validateDateTime($day, $month, $year, $hour, $minute) {
		global $errMsg;
		global $minute;
		$valid = true;
		if ($minute == "") {
			$minute = "00";
		}

		//check if the date is a valid date on the calendar
		if (!checkdate($month, $day, $year)) {
			$errMsg .= "<p>The preferred date is invalid</p>";
			$valid = false;
		}

		//check if before 7:30
		if ($hour == 7 && $minute < 30) {
			$errMsg .= "<p>The preferred pickup time needs to be between 7:30 and 20:30</p>";
			$valid = false;
		}

		//check if after 20:30
		if ($hour == 20 && $minute > 30) {
			$errMsg .= "<p>The preferred pickup time needs to be between 7:30 and 20:30</p>";
			$valid = false;
		}

		//check if time is valid
		$now = new DateTime();
		$now = date_timestamp_get($now); 	//get unix timestamp
		$pickup_time = strtotime($year."-".$month."-".$day." ".$hour.":".$minute.":00");
		$diff = $pickup_time - $now;	//get difference between now and the pickup time

		if ($diff < 86400) { 	//24 hours in seconds
			$errMsg .= "<p>The preferred time needs to be at least 24 hours from request time<p>";
			$valid = false;
		}

		return $valid;
	}

	/**
	* Calculate the price of the order
	*/
	function calcPrice($weight) {
		$additional_wt = $weight - 2;
		$cost = 10 + ($additional_wt * 2);
		return number_format((float)$cost, 2, '.', '');
	}

	/**
	* Creates the table customers in the database if one does not already exist
	*/
	function createTable() {
		global $conn;
		global $errMsg;
		if (!$conn) {
			$errMsg .= "<p>Database connection failure</p>";
		} 
		else {
			$sql_table = "request";
			$query = "CREATE TABLE IF NOT EXISTS $sql_table (
					cust_id INT NOT NULL,
					request_num INT AUTO_INCREMENT PRIMARY KEY,
					request_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					item_desc VARCHAR(50) NOT NULL,
					weight INT NOT NULL,
					pickup_addr VARCHAR(50) NOT NULL,
					pickup_suburb VARCHAR(20) NULL NULL,
					pickup_date_time VARCHAR(20) NOT NULL,
					receiver_name VARCHAR(30) NOT NULL,
					del_addr VARCHAR(50) NOT NULL,
					del_suburb VARCHAR(20) NOT NULL,
					del_state VARCHAR(10) NOT NULL
				)";
			$result = mysqli_query($conn, $query);
			if (!$result) {
				$errMsg .= "<p>There was an error with the query</p>";
			}
		}		
	}

	/**
	* Save data to table on database
	*/
	function saveData($cust_id, $item_desc, $weight, $pickup_addr, $pickup_suburb, $pickup_date_time, $receiver_name, $del_addr, $del_suburb, $del_state) {
		global $conn;
		global $errMsg;
		if (!$conn) {
			echo "<p>Database connection failure</p>";
		}
		else {
			$sql_table = "request";
			$query = "INSERT INTO $sql_table (
				cust_id,
				item_desc,
				weight,
				pickup_addr,
				pickup_suburb,
				pickup_date_time,
				receiver_name,
				del_addr,
				del_suburb,
				del_state
			) VALUES (
				'$cust_id',
				'$item_desc',
				'$weight',
				'$pickup_addr',
				'$pickup_suburb',
				'$pickup_date_time',
				'$receiver_name',
				'$del_addr',
				'$del_suburb',
				'$del_state'
			)";

			$result = mysqli_query($conn, $query);
			if ($result) {
				$request_num = mysqli_insert_id($conn);
			}
			else {
				$errMsg .= "<p>There was an error with saving data</p>";
			}
		}
		return $request_num;
	}

	function getEmail($cust_id) {
		global $conn;
		global $errMsg;
		if (!$conn) {
			echo "<p>Database connection failure</p>";
		}
		else {
			$sql_table = "customers";
			$query = "SELECT email FROM customers WHERE cust_id = $cust_id";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_assoc($result);
			$email = $row['email'];
			return $email;
		}
	}

	function emailCustomer($name, $email, $cost, $pickup_date_time, $request_num) {
		$to = $email;
		$subject = "Shipping request with ShipOnline";
		$message = "
		<html>
		<head>
			<title>Email</title>
		</head>
		<body>
			<p>Dear $name</p>
			<br>
			<p>Thank you for using ShipOnline!</p>
			<p>Your request number is $request_num</p>
			<p>The cost is $cost</p>
			<p>We will pick up the item at $pickup_date_time</p>
			<br>
			<p>Kind Regards,</p>
			<p>ShipOnline team</p>
		</body>
		</html>
		";
		$headers = "MIME-Version: 1.0". "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8"."\r\n";

		if (mail($to, $subject, $message, $headers, "-r 101335460@student.swin.edu.au")) {
			$emailDispatch = true;
		} 
		else {
			$emailDispatch = false;
		}

		return $emailDispatch;
	}


	function main() {
		global $errMsg;
		global $minute;
		$attempted = false;		//check if form has been attempted

		if (isset($_GET["item_desc"])) {
			$item_desc = $_GET["item_desc"];
			$name = $_GET["name"];
			$cust_id = $_GET["cust_id"];
			$weight = $_GET["weight"];
			$pickup_addr = $_GET["pickup_addr"];
			$pickup_suburb = $_GET["pickup_suburb"];
			$day = $_GET["day"];
			$month = $_GET["month"];
			$year = $_GET["year"];
			$hour = $_GET["hour"];
			$minute = $_GET["minute"];
			$receiver_name = $_GET["receiver_name"];
			$del_addr = $_GET["del_addr"];
			$del_suburb = $_GET["del_suburb"];
			$del_state = $_GET["del_state"];
			$attempted = true;
		}

		if ($attempted && $cust_id != 0) {
			//sanitise input of vulnerable input fields
			$item_desc = sanitise_input($item_desc);
			$pickup_addr = sanitise_input($pickup_addr);
			$pickup_suburb = sanitise_input($pickup_suburb);
			$receiver_name = sanitise_input($receiver_name); 
			$del_addr = sanitise_input($del_addr);
			$del_suburb = sanitise_input($del_suburb);

			//calculate cost
			$cost = calcPrice($weight);

			//validate data
			if (validateDateTime($day, $month, $year, $hour, $minute)) {
				//format the date for database
				if ($day < 10) {
					$day = str_pad($day, 2, "0", STR_PAD_LEFT);
				}
				if ($month < 10) {
					$month = str_pad($month, 2, "0", STR_PAD_LEFT);
				}
				$pickup_date_time = $year. "-". $month. "-". $day. ",". $hour. ":". $minute;
				createTable();
				//save data if data is valid
				$request_num = saveData($cust_id, $item_desc, $weight, $pickup_addr, $pickup_suburb, $pickup_date_time, $receiver_name, $del_addr, $del_suburb, $del_state);
				echo "<font color='blue'>The request number is ". $request_num. ". We will pick up the item at ". $hour. ":". $minute. " on ". $day. "/". $month. "/". $year. ". The cost is $".$cost. ".</font>";
				//get email address
				$email = getEmail($cust_id);
				if (emailCustomer($name, $email, $cost, $pickup_date_time, $request_num)) {
					
				}
				else {
					echo "<p>Email could not be sent</p>";
				}
				
			}
			else {
				echo "<font color='red'>".$errMsg."</font>";
			}
		}
		elseif ($attempted && $cust_id == 0) {
			echo "<font color='red'>Please log in first</font>";
		}
		else {

		}

	}

	main();
	mysqli_close($conn);
	?>
	</div>

</body>
</html>