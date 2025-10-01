<?php
session_start();

if (!isset($_SESSION["username"])) {
    echo "<script>
        alert('Πρέπει να συνδεθείτε πρώτα.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

$conn = new mysqli("localhost", "root", "admin123", "my_db");
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $AM = $_POST["AM"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, AM=?, phone=?, email=?, password=? WHERE username=?");
    $stmt->bind_param("sssssss", $first_name, $last_name, $AM, $phone, $email, $password, $username);
    $stmt->execute();
    echo "<script>alert('Τα στοιχεία ενημερώθηκαν με επιτυχία.');</script>";
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Επεξεργασία λογαριασμού</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/mycss.css">
</head>
	<body>
		<div class="row header">
			<div class="col-12">
				<h1>Ο λογαριασμός σας</h1>
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
					<form method="post" action="profile.php">
						<label for="first_name">Όνομα:</label><br>
						<input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required><br>

						<label for="last_name">Επίθετο:</label><br>
						<input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required><br>

						<label for="AM">Αριθμός Μητρώου:</label><br>
						<input type="text" name="AM" value="<?= htmlspecialchars($user['AM']) ?>" required><br>

						<label for="phone">Τηλέφωνο:</label><br>
						<input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required><br>

						<label for="email">Email:</label><br>
						<input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

						<label for="username">Username (δεν αλλάζει):</label><br>
						<input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly><br>

						<label for="password">Κωδικός:</label><br>
						<input type="text" name="password" value="<?= htmlspecialchars($user['password']) ?>" required><br><br>

						<input type="submit" value="Αποθήκευση">
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
