<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Submit_data"])) {
    $servername = "localhost";
    $username = "root";
    $password = "admin123";
    $dbname = "my_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

	$conn = new mysqli("localhost", "root", "admin123", "my_db");
	if ($conn->connect_error) {
		die("Σφάλμα σύνδεσης: " . $conn->connect_error);
	}

    $username_input = $conn->real_escape_string($_POST["username"]);
    $password_input = $conn->real_escape_string($_POST["password"]);

    $sql = "SELECT * FROM users WHERE username = '$username_input' AND password = '$password_input'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $row["username"];
        $_SESSION["admin"] = $row["admin"];
        header("Location: index.php");
		echo "<script>alert('Καλως όρισες " . $_SESSION["username"] . "');</script>";
        exit;
    } else {
        echo "<script>alert('Λάθος όνομα χρήστη ή κωδικός.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="el">
	<head>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="styles/mycss.css">
		<title>Σύνδεση Erasmus Portal</title>
	</head>
	<body>
		<div class="row header">
			<div class="col-12">
				<h1>Σύνδεση με τον λογαριασμό σας στο Erasmus Portal</h1>
			</div>
		</div>
		<div class="row">
		<div class="col-12 col-md-3  col-lg-3 menu">
		<ul>
			<li><a href="index.php">Αρχική</a></li>
			<li><a href="more.html">Περισσότερα</a></li>
			<li><a href="reqs.html">Απαιτήσεις</a></li>
			<li><a href="sign-up.php">Εγγραφή</a></li>
			<li><a href="login.php">Σύνδεση</a></li>
			<b>Ο λογαριασμός μου:</b>
			<ul>
			  <li><a href="application.php">Αίτηση</a></li>
			  <li><a href="profile.php">Profile</a></li>
			</ul>
		</ul>
    </div>
    <div class="col-12 col-md-6 col-lg-6">
        <div class="form-box">
			<form action="login.php" method="post">
               <label for="login_username">Username:</label><br>
               <input type="text" name="username" id="login_username" maxlength="13"><br>
               <label for="login_password">Password:</label><br>
               <input type="password" name="password" id="login_password" maxlength="25"><br><br>
               <input type="submit" name="Submit_data" value="Είσοδος">
            </form>
            </div>
         </div>
      </div>
   </body>
</html>