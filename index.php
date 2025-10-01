
<?php
session_start();

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="el">
	<head>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="styles/mycss.css">
		<title>Αρχική Erasmus Portal</title>
	</head>
	<body>

	<?php if ($isAdmin): ?>
	<?php
	$conn = new mysqli("localhost", "root", "admin123", "my_db");
	if ($conn->connect_error) {
		die("Σφάλμα σύνδεσης: " . $conn->connect_error);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['set_period'])) {
		$start = $_POST['start_date'];
		$end = $_POST['end_date'];

		$conn->query("DELETE FROM application_period");
		$stmt = $conn->prepare("INSERT INTO application_period (start_date, end_date) VALUES (?, ?)");
		$stmt->bind_param("ss", $start, $end);
		$stmt->execute();
		echo "<script>alert('Η περίοδος ορίστηκε επιτυχώς');</script>";
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_selected'])) {
		if (!empty($_POST['accepted_ids'])) {
			$ids = $_POST['accepted_ids'];
			foreach ($ids as $id) {
				$conn->query("UPDATE applications SET accepted = TRUE WHERE id = " . intval($id));
			}
			echo "<script>alert('Οι επιλεγμένες αιτήσεις σημειώθηκαν ως δεκτές.');</script>";
		}
	}

	$universities = [];
	$uniQuery = "SELECT university_name FROM universities";
	$uniResult = $conn->query($uniQuery);
	if ($uniResult && $uniResult->num_rows > 0) {
		while ($row = $uniResult->fetch_assoc()) {
			$universities[] = $row['university_name'];
		}
	}

	$minPercent = $_GET['min_percent'] ?? 0;
	$selectedUniversity = $_GET['university'] ?? '';
	$order = "ORDER BY avg_grade DESC";

	$where = "WHERE passed_percent >= " . floatval($minPercent);
	if (!empty($selectedUniversity)) {
		$where .= " AND ('$selectedUniversity' IN (university1, university2, university3))";
	}

	$sql = "SELECT * FROM applications $where $order";
	$result = $conn->query($sql);
	?>

	<div class="row header">
		<div class="col-12"
			<h1>Καλωσήρθες διαχειριστή!</h1>
		</div>
	</div>
		<div class="row">
				<div class="col-12 col-md-3 col-lg-3 menu">
					<ul>
						<li><a href="login.php">Σύνδεση</a></li>
						<li><a href="index.php?logout=1">Αποσύνδεση</a></li>
					</ul>
				</div>
			<div class="col-12 col-md-6 col-lg-6 main">
				<h2>Πίνακας Διαχειριστή</h2>
			</div>
			<div class="col-12 col-md-6 col-lg-6">
				<div class="form-box">
					<form method="post">
						<label for="start_date">Έναρξη:</label>
						<input type="date" name="start_date" required><br><br>
						<label for="end_date">Λήξη:</label>
						<input type="date" name="end_date" required><br><br>
						<button type="submit" name="set_period">Ορισμός Περιόδου</button>
					</form>
				</div>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-6 main">
			<hr>
			<h3>Αιτήσεις</h3>
			<form method="GET">
				<label>Ελάχιστο Ποσοστό «περασμένων μαθημάτων»:</label>
				<input type="number" name="min_percent" min="0" max="100" step="0.01" value="<?= htmlspecialchars($minPercent) ?>">
				<label>Πανεπιστήμιο:</label>
				<select name="university">
					<option value="">-- Όλα --</option>
					<?php foreach ($universities as $uni): ?>
						<option value="<?= htmlspecialchars($uni) ?>" <?= $selectedUniversity == $uni ? 'selected' : '' ?>>
							<?= htmlspecialchars($uni) ?>
						</option>
					<?php endforeach; ?>
				</select>
				<button type="submit">Φιλτράρισμα</button>
			</form>

			<form method="POST">
				<table border="1">
					<tr>
						<th>ID</th>
						<th>Όνομα</th>
						<th>Επώνυμο</th>
						<th>AM</th>
						<th>Έτος</th>
						<th>% Επιτυχίας</th>
						<th>Μ.Ο.</th>
						<th>Αγγλικά</th>
						<th>Άλλες Γλώσσες</th>
						<th>Πανεπιστήμια</th>
						<th>Αρχεία</th>
						<th>Δεκτή</th>
					</tr>
					<?php while ($row = $result->fetch_assoc()): ?>
						<tr>
							<td><?= htmlspecialchars($row['id']) ?></td>
							<td><?= htmlspecialchars($row['first_name']) ?></td>
							<td><?= htmlspecialchars($row['last_name']) ?></td>
							<td><?= htmlspecialchars($row['AM']) ?></td>
							<td>
								<?php
								if ((int)$row['study_year'] >= 5) {
									echo htmlspecialchars($row['study_year']) . '+';
								} else {
									echo htmlspecialchars($row['study_year']);
								}
								?>
							</td>
							<td><?= htmlspecialchars($row['passed_percent']) ?>%</td>
							<td><?= htmlspecialchars($row['avg_grade']) ?></td>
							<td><?= htmlspecialchars($row['english_level']) ?></td>
							<td>
								<?php
								if ($row['extra_langs']) {
									echo 'Ναι';
								} else {
									echo 'Όχι';
								}
								?>
							</td>
							<td>
								<?= htmlspecialchars($row['university1']) ?><br>
								<?= htmlspecialchars($row['university2']) ?><br>
								<?= htmlspecialchars($row['university3']) ?>
							</td>
							<td>
								<a href="uploads/<?= $row['grade_file'] ?>" target="_blank">Βαθμολογία</a><br>
								<a href="uploads/<?= $row['english_cert_file'] ?>" target="_blank">Αγγλικά</a><br>
								<?php if ($row['extra_langs']): ?>
									<a href="uploads/<?= $row['other_langs_file'] ?>" target="_blank">Άλλες Γλώσσες</a>
								<?php endif; ?>
							</td>
							<td>
								<input type="checkbox" name="accepted_ids[]" value="<?= $row['id'] ?>"
									<?php
									if ($row['accepted']) {
										echo 'checked';
									}
									?>
								>
							</td>
						</tr>
					<?php endwhile; ?>
				</table>
				<button type="submit" name="accept_selected">Αποδοχή Επιλεγμένων</button>
			</form>
		<div>
	<?php else: ?>
		<div class="row header">
			<div class="col-12">
				<h1>Καλωσήρθες στο Erasmus Portal</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-6 col-md-3 col-lg-3 menu">
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
			<div class = "main">
				<div class="col-12 col-md-6 col-lg-6">
						<h2>Τι είναι το Erasmus;</h2>
						<p>Το erasmus είναι ένα πρόγραμμα κινητικότητας φοιτητών, μέσω του οποίου ενδιαφερόμενοι μπορούν να φοιτήσουν για ένα εξάμηνο σε ένα αντίστοιχο τμήμα εκτός Ελλάδος. Οι ενδιαφερόμενοι χρειάζεται να πληρούν τα κρητήρια αίτησης, να αιτηθούν και αν γίνουν δεκτοί να επιλέξουν στην πορεία κατά σειρά προτίμησης συνεργαζόμενα πανεπιστημιακά τμήματα του εξωτερικού.</p>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
					<img src="media/erasmus.png" alt="erasmus" width="100%">
					<br><p>Οι φοιτητές που τους δόθηκε η ευκαιρία μέσω του προγράμματος να φοιτήσουν εκτός συνόρων έχουν μοιραστεί αμέτρητες εμπειρίες και ιστορίες ζωής με διδάκτορες τους αλλά και τους συναδέλφους του τμήματος. Ακόμη, τους συνιστούν ανεπιφύλακτα να αιτηθούν για το πρόγραμμα.</p>
				</div>
			</div>
		</div>
	<?php endif; ?>
	</body>
</html>