<!DOCTYPE html>
<html>
<head>
	<title>Ship Online</title>
	<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
    <nav class="light-green lighten-2" role="navigation">
      <div class="nav-wrapper container">
        <ul>
          <li><a href="../../index.php">Return to blog</a></li>
        </ul>
      </div>
    </nav>
    <div class="card responsive-img">
    	<div class="card-image">
			<img src="images/courier-services.jpg">
	    	<span class="card-title center-align">
	    		<h1>ShipOnline</h1>
	    		<h5>Delivering to your doorstep</h5>
	    	</span>    	    			
    	</div>
    </div>
    
    <div class="container">
		<div class="row center">
			<br><br>

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
						<button class="btn waves-effect waves-light blue darken-4" type="submit" name="action">
							Login
						</button>
					</p>
				</fieldset>
			</form>			
		</div>
		<a href="register.php" class="btn waves-effect waves-light green darken-1" title="Registration">Registration</a>
		<a href="admin.php" class="btn waves-effect waves-light green darken-1" title="Administration">Administration</a>	
		<br><br><br><br>
		
    </div>
    

    <footer class="page-footer green darken-4">
    	<div class="container">
	    	<h5 class="white-text">Company Bio</h5>
	    	<p class="white-text">We pick stuff up and deliver it where you want. Quick and easy, no questions asked (unless you want us to transport a dead body)</p>
	    	<br>    		
    	</div>

    </footer>

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