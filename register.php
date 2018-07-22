<!DOCTYPE html>
<html>
<head>
	<title>Ship Online</title>
	<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
	<div class="section">
		<h2 class="header center green-text">ShipOnline Registration Page</h2>
	</div>
    <div class="container">
		<form id="register" method="post" >		
			<fieldset>
				<p>
					<label for="name">Name</label>
					<input type="text" name="name" id="name" required="required" pattern="^[a-zA-Z]+(\s[a-zA-Z]+)?${1,30}" title="must contain only alpha characters" />
				</p>
				<p>
					<label for="password">Password</label>
					<input type="password" name="password" required="required">
				</p>
				<p>
					<label for="retype_password">Retype password</label>
					<input type="password" name="retype_password" required="required" />
				</p>
				<p>
					<label for="email">Email</label>
					<input type="email" name="email" id="email" required="required" />
				</p>
				<p>
					<label for="phone">Phone number</label>
					<input type="tel" name="phone" required="required" pattern="[0-9]{1,10}" title="must contain only numbers" />
				</p>
				<p>
					<button class="btn waves-effect waves-light blue darken-4" type="submit" name="action">
						Submit
					</button>
				</p>
			</fieldset>
			<p><a href="shiponline.php">Home</a></p>
		</form>    	
	
	<?php
	require_once ("settings.php"); //connection info
	$conn = @mysqli_connect($host,
			$user,
			$pwd,
			$sql_db
		);
	if (!$conn) {
		echo "Error connecting to MySQL";
		echo "Debugging errno: ". mysqli_connect_errno();
		echo "Debbuging error: ". mysqli_connect_error();
	}


	//initialise errMsg
	$errMsg = "";

	/**
	Creates the table customers in the database if one does not already exist
	*/
	function createTable() {
		global $conn;
		global $errMsg;
		if (!$conn) {
			$errMsg .= "<p>Database connection failure</p>";
		} 
		else {
			$sql_table = "customers";
			$query = "CREATE TABLE IF NOT EXISTS $sql_table (
					cust_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					name VARCHAR(30) NOT NULL,
					password VARCHAR(20) NOT NULL,
					email VARCHAR(30) NOT NULL,
					phone VARCHAR(12) NOT NULL
				)";
			$result = mysqli_query($conn, $query);
			if (!$result) {
				$errMsg .= "<p>There was an error with the query</p>";
			}
		}		
	}

	/**
	* Gets rid of special unncessary characters
	*/
	function sanitise_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	function validateEmail($email) {
		$valid = true;
		global $conn;
		global $errMsg;

		//check if email address exists
		if (!$conn) {
			$errMsg .= "<p>Database connection failure</p>";

		}
		else {
			$query = "SELECT name FROM customers WHERE email = '$email'";
			$result = mysqli_query($conn, $query);
			$row_cnt = $result -> num_rows;
			if ($row_cnt != 0) {
				//email exists
				$valid = false;
			}
		}
		mysqli_free_result($result);
		return $valid;
	}

	/**
	* Checks if data is valid
	*/
	function validateData($name, $password, $rePassword, $email, $phone) {
		$valid = true;
		global $errMsg;

		//check name
		if ($name == "") {
			$errMsg .= "<p>You must enter your name</p>";
			$valid = false;
		}
		else if(!preg_match("/^[a-zA-Z .\-_]*$/", $name)) {
			$errMsg .= "<p>Only alpha letters allowed in your name</p>";
			$valid = false;
		}

		//check password
		if ($password == "") {
			$errMsg .= "<p>You must enter a password</p>";
			$valid = false;
		}
		elseif ($password != $rePassword) {
			$errMsg .= "<p>Password not the same as retyped password</p>";
			$valid = false;
		}

		//check email
		if(!filter_var($email, FILTER_VALIDATE_EMAIL) || $email == "") {
			$errMsg .= "<p>You must enter a valid email address</p>";
			$valid = false;
		}
		elseif (!validateEmail($email)) {
			$errMsg .= "<p>The email address already exists!</p>";
			$valid = false;
		}

		//check phone number
		if(!preg_match("/^[0-9]{8,12}$/", $phone)) {
			$errMsg .= "<p>You must enter a valid phone number</p>";
			$valid = false;
		}

		return $valid;
	}

	function saveData($name, $password, $email, $phone) {
		global $conn;
		global $errMsg;
		$cust_id = 0;
		if (!$conn) {
			echo "<p>Database connection failure</p>";
		}
		else {
			$sql_table = "customers";
			$query = "INSERT INTO $sql_table (
				name,
				password,
				email,
				phone
			) VALUES (
				'$name',
				'$password',
				'$email',
				'$phone'
			)";

			$result = mysqli_query($conn, $query);
			if ($result) {
				$cust_id = mysqli_insert_id($conn);
			}
			else {
				$errMsg .= "<p>There was an error with saving data</p>";
			}
		}
		return $cust_id;
	}

	/**
	* Main function
	*/
	function main() {
		global $errMsg;
		$attempted = false; // check if form has been attempted
		//get data
		if (isset($_POST["name"])) {
			$name = $_POST["name"];
			$password = $_POST["password"];
			$rePassword = $_POST["retype_password"];
			$email = $_POST["email"];
			$phone = $_POST["phone"];
			$attempted = true;
		}

		if ($attempted) {
			//sanitise values
			$name = sanitise_input($name);
			$password = sanitise_input($password);
			$email = sanitise_input($email);
			$phone = sanitise_input($phone);

			//check if values valid
			if (validateData($name, $password, $rePassword, $email, $phone)) {

				createTable(); //checks if tables exists and creates one if it doesn't

				$cust_id = saveData($name, $password, $email, $phone); //save data and return the customer id
				if ($cust_id != 0) {
					echo "Dear $name, you are successfully registered into ShipOnline. Your customer id is $cust_id, which will be used to login to the system.";
				}
				
			}
			else {
				echo "<font color='red'>".$errMsg."</font>";
			}
		}	

	}

	main();
	mysqli_close($conn);

	?>

	</div>
	<br><br><br>

</body>
</html>