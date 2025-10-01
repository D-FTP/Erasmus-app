<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Submit_data"])) {
    $servername = "localhost";
    $username = "root";
    $password = "admin123";
    $dbname = "my_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Σφάλμα σύνδεσης: " . $conn->connect_error);
    }

    $first_name = $conn->real_escape_string($_POST["first_name"]);
    $last_name = $conn->real_escape_string($_POST["last_name"]);
    $AM = $conn->real_escape_string($_POST["AM"]);
    $phone = $conn->real_escape_string($_POST["phone"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $username_input = $conn->real_escape_string($_POST["username"]);
    $password_input = $conn->real_escape_string($_POST["password"]);

    $check_am = $conn->query("SELECT id FROM users WHERE AM = '$AM'");
    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
    $check_username = $conn->query("SELECT id FROM users WHERE username = '$username_input'");

    if ($check_am->num_rows > 0 || $check_email->num_rows > 0 || $check_username->num_rows > 0) {
        echo "<script>alert('Το ΑΜ, το email ή/και το username χρησιμοποιούνται ήδη. Παρακαλώ δοκιμάστε ξανά.');</script>";
    } else {
        $sql = "INSERT INTO users (first_name, last_name, AM, phone, email, username, password) 
                VALUES ('$first_name', '$last_name', '$AM', '$phone', '$email', '$username_input', '$password_input')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION["username"] = $username_input;
            header("Location: index.php");
			echo "<script>alert('Καλως όρισες " . $_SESSION["username"] . "');</script>";
            exit();
        } else {
            echo "Σφάλμα κατά την εγγραφή: " . $conn->error;
        }
    }

    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="el">
	<head>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="styles/mycss.css">
		<title>Εγγραφή Erasmus Portal</title>
	</head>
	<body>
		<div class="row header">
			<div class="col-12">
				<h1>Εγγραφή λογαριασμού στο Erasmus Portal</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-12 col-md-3 col-lg-3 menu">
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
					<form method="post"  action="sign-up.php" onsubmit="return validateForm()">	
						<label for="first_name">Όνομα:</label><br>
						<input type="text" name="first_name" id="first_name"><br>
						<label for="last_name">Επίθετο:</label><br>
						<input type="text" name="last_name" id="last_name"><br>
						<label for="AM">Αριθμός Μητρώου:</label><br>
						<input type="text" name="AM" id="AM"><br>
						<label for="phone">Τηλέφωνο:</label><br>
						<input type="text" name="phone" id="phone"><br>
						<label for="email">Email:</label><br>
						<input type="email" name="email" id="email"><br>
						<label for="username">Username:</label><br>
						<input type="text" name="username" id="username" maxlength="13"><br>
						<label for="password">Password:</label><br>
						<input type="password" name="password" id="password" maxlength="25"><br>
						<label for="confirm_password">Confirm Password:</label><br>
						<input type="password" name="confirm_password" id="confirm_password" maxlength="25"><br><br>
						<input type="submit" name="Submit_data" value="Εγγραφή">
					</form>
				</div>
			</div>
        </div>
		<script>
			function validateForm() {
				const fname = document.getElementById("first_name").value.trim();
				const lname = document.getElementById("last_name").value.trim();
				const AM = document.getElementById("AM").value.trim();
				const phone = document.getElementById("phone").value.trim();
				const email = document.getElementById("email").value.trim();

				if (hasNumber.test(fname)) {
					alert("Το όνομα δεν πρέπει να περιέχει αριθμούς.");
					return false;
				}

				if (hasNumber.test(lname)) {
					alert("Το επώνυμο δεν πρέπει να περιέχει αριθμούς.");
					return false;
				}

				if (!amPattern.test(AM)) {
					alert("Ο Αριθμός Μητρώου πρέπει να ξεκινά με '2022' και να έχει συνολικά 13 ψηφία.");
					return false;
				}

				if (!phonePattern.test(phone)) {
					alert("Το τηλέφωνο πρέπει να περιέχει ακριβώς 10 ψηφία.");
					return false;
				}

				if (!emailPattern.test(email)) {
					alert("Παρακαλώ δώσε ένα έγκυρο email.");
					return false;
				}
					
				if (password.length < 5) {
					alert("Ο κωδικός πρέπει να έχει τουλάχιστον 5 χαρακτήρες.");
					return false;
				}

				if (!hasSymbol.test(password)) {
					alert("Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα σύμβολο (π.χ. !, #, $).");
				return false;
				}

				if (password !== confirmPassword) {
					alert("Τα πεδία password και confirm password δεν ταιριάζουν.");
				return false;
				}

				return true;
			}
		</script>
	</body>
</html>