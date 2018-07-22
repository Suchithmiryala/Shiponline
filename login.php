<!--Web Application Development COS80021 - Assignment1 - Phu Dao 101335460 -->

<!DOCTYPE html>
<html>
<head>
	<title>ShipOnline</title>
</head>
<body>
	<h1>ShipOnline System Login Page</h1>

	<form id="login" method="post">
		<fieldset>
			<p>
				<label for="cust_id">Customer Number</label>
				<input type="text" name="cust_id" required="required" pattern="[0-9]{1,10}" title="Must only contain numbers" />
			</p>
			<p>
				<label for="password">Password</label>
				<input type="password" name="password" required="required" />
			</p>
			<p>
				<input type="submit" name="Log in" />
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

	$errMsg = "";	//initialise error message
	$attempted = false; 	//checks if form as been attempted
	$validPassword = false;
	$name = "";

	if (isset($_POST["cust_id"])) {
		$cust_id = $_POST["cust_id"];
		$password = $_POST["password"];
		$attempted = true;
	}

	if($attempted) {
		if ($password == "") {
			$errMsg .= "<p>Please enter your password</p>";
		}
		else {
			$sql_table = "customers";
			$query = "SELECT password, name FROM customers WHERE cust_id = $cust_id";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_assoc($result);
			$dbPassword = $row['password'];
			//check if password is the same
			if (is_null($dbPassword)) {
				$errMsg .= "<p>The customer number does not exist</p>";
			}
			elseif ($password != $dbPassword) {
				$errMsg .= "<p>Password is incorrect</p>";
			}
			else {
				$name = $row['name'];
				$validPassword = true;
			}
			mysqli_free_result($result);
		}
	}
	mysqli_close($conn);

	if ($validPassword) {
		echo "Login success!";
		header("location: request.php?cust_id=$cust_id&name=$name");
	}
	else {
		echo "<font color='red'>".$errMsg."</font>";
	}

?>

</body>
</html>