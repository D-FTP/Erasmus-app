<?php
session_start();

if (!isset($_SESSION["username"])) {
    echo "<script>alert('Πρέπει να συνδεθείτε πρώτα.'); window.location.href = 'login.php';</script>";
    exit();
}

$conn = new mysqli("localhost", "root", "admin123", "my_db");
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$sql = "SELECT first_name, last_name, AM FROM users WHERE username = '$username'";
$result = $conn->query($sql);

$userData = ['first_name' => '', 'last_name' => '', 'AM' => ''];
if ($result && $result->num_rows > 0) {
	$userData = $result->fetch_assoc();
}

$uniOptions = '';
$uniResult = $conn->query("SELECT university_name FROM universities");
if ($uniResult && $uniResult->num_rows > 0) {
    while ($row = $uniResult->fetch_assoc()) {
        $uniName = htmlspecialchars($row['university_name']);
        $uniOptions .= "<option value=\"$uniName\">\n";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION["username"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $AM = $_POST["AM"];

    $study_year_input = $_POST["year_list"];
    $study_year_map = [
        "1ο" => "1",
        "2ο" => "2",
        "3ο" => "3",
        "4ο" => "4",
        "μεγαλύτερο" => "5"
    ];
    $study_year = isset($study_year_map[$study_year_input]) ? $study_year_map[$study_year_input] : null;

    if ($study_year === null) {
        echo "<script>alert('Μη έγκυρη τιμή για το έτος σπουδών.'); history.back();</script>";
        exit();
    }

    $passed_percent = $_POST["passed_percent"];
    $avg_grade = $_POST["avg_grade"];
    $english_level = $_POST["english_level"];
	$extra_langs = (isset($_POST["extra_langs"]) && $_POST["extra_langs"] === "ΝΑΙ") ? 1 : 0;
    $university1 = $_POST["uni_list1"];
    $university2 = $_POST["uni_list2"];
    $university3 = $_POST["uni_list3"];

    $uploadDir = "uploads/";

	function uploadFile($fieldName, $prefix, $username) {
		global $uploadDir;
		if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === 0) {
			$ext = pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION);
			$safeExt = strtolower($ext);
			$filename = $prefix . "_" . $username . "_" . date("Ymd_His") . "." . $safeExt;
			$destination = $uploadDir . $filename;
			if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
				return $filename;
			}
		}
		return null;
	}

	$gradeFile = uploadFile("grade_file", "grade", $username);
	$englishCertFile = uploadFile("english_cert_file", "english_cert", $username);
	$otherLangsFile = $extra_langs ? uploadFile("other_langs_file", "other_langs", $username) : null;

    $stmt = $conn->prepare("INSERT INTO applications 
        (username, first_name, last_name, AM, study_year, passed_percent, avg_grade, 
         english_level, extra_langs, university1, university2, university3, 
         grade_file, english_cert_file, other_langs_file) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		 
	$passed_percent = floatval(str_replace(",", ".", $_POST["passed_percent"]));
	$avg_grade = floatval(str_replace(",", ".", $_POST["avg_grade"]));

	$stmt->bind_param("ssssssdssssssss",
		$username, $first_name, $last_name, $AM, $study_year,
		$passed_percent, $avg_grade, $english_level, $extra_langs,
		$university1, $university2, $university3,
		$gradeFile, $englishCertFile, $otherLangsFile
	);

    if ($stmt->execute()) {
        echo "<script>alert('Η αίτηση υποβλήθηκε με επιτυχία!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Σφάλμα κατά την υποβολή της αίτησης.');</script>";
    }

    $stmt->close();
    $conn->close();
}

?>



<!DOCTYPE html>
<html lang="el">
   <head>
      <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="styles/mycss.css">
      <title>Αίτηση Erasmus Portal</title>
   </head>
   <body>
		<div class="row header">
			<div class="col-12">
				<h1>Αίτηση συμμετοχής στο Erasmus+</h1>
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
			<div class="col-9 col-md-6 col-lg-6">
				<div class="form-box">
					<form method="POST" enctype="multipart/form-data">
						Όνομα:
						<br>
						<input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" readonly>
						<br>
						Επίθετο:
						<br>
						<input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" readonly>
						<br>
						Αριθμός Μητρώου:
						<br>
						<input type="text" id="am" name="AM" value="<?php echo htmlspecialchars($userData['AM']); ?>" readonly>

						<br>
						<label for="year_list">Έτος σπουδών:</label><br>
						<input list="A" name="year_list" id="year_list" required><br><br>
						<datalist id="A">
							<option value="1ο">
							<option value="2ο">
							<option value="3ο">
							<option value="4ο">
							<option value="μεγαλύτερο">
						</datalist>
						<label for="passed_percent" title="έως και το προηγούμενο έτος">Ποσοστό «περασμένων μαθημάτων»:</label><br>
						<input type="number" name="passed_percent" id="passed_percent" min="0" max="100" step="0.01" placeholder="%" required><br><br>
						<label for="avg_grade" title="έως και το προηγούμενο έτος">Μέσος όρος των «περασμένων μαθημάτων»:</label><br>
						<input type="number" name="avg_grade" id="avg_grade" min="5" max="10" step="0.01" placeholder="5-10" required><br><br>
						
						Πιστοποιητικό γνώσης της αγγλικής γλώσσας:
						<br>
						<input type="radio" id="A1" name="english_level" value="A1" required>
						<label for="A1">A1</label><br>
						<input type="radio" id="A2" name="english_level" value="A2">
						<label for="A2">A2</label><br>
						<input type="radio" id="B1" name="english_level" value="B1">
						<label for="B1">B1</label><br>
						<input type="radio" id="B2" name="english_level" value="B2">
						<label for="B2">B2</label><br>
						<input type="radio" id="C1" name="english_level" value="C1">
						<label for="C1">C1</label><br>
						<input type="radio" id="C2" name="english_level" value="C2">
						<label for="C2">C2</label><br><br>
						
						Γνώση επιπλέον ξένων γλωσσών:
						<br>
						<input type="radio" id="yes" name="extra_langs" value="ΝΑΙ">
						<label for="yes">ΝΑΙ</label><br>
						<input type="radio" id="no" name="extra_langs" value="ΟΧΙ">
						<label for="no">ΟΧΙ</label><br><br>
						
						<datalist id="C">
							<?php echo $uniOptions; ?>
						</datalist>
						<label for="uni_list1">Πανεπιστήμιο - 1η επιλογή:</label><br>
						<input list="C" name="uni_list1" id="uni_list1" required><br><br>
						<label for="uni_list2">Πανεπιστήμιο - 2η επιλογή:</label><br>
						<input list="C" name="uni_list2" id="uni_list2"><br><br>
						<label for="uni_list3">Πανεπιστήμιο - 3η επιλογή:</label><br>
						<input list="C" name="uni_list3" id="uni_list3"><br><br>
						
						<br>
						<label for="grade_file">Αναλυτική βαθμολογία:</label>
						<input type="file" name="grade_file" required><br><br>
						<label for="english_cert_file">Πτυχίο αγγλικής γλώσσας:</label>
						<input type="file" name="english_cert_file" required><br><br>
						<div id="otherLangsUpload" style="display: none;">
							<label for="other_langs_file">Πτυχία άλλων ξένων γλωσσών:</label>
							<input type="file" name="other_langs_file" id="other_langs_file" multiple>
							<br>
						</div>
						<br><br>
						<input type="checkbox" id="terms" name="terms" required>
						<label for="terms">Αποδέχομαι τους όρους συμμετοχής στο πρόγραμμα Erasmus+</label><br>
						<input type="submit" value="Υποβολή">
					</form>
				</div>
			</div>
		</div>
		<script>
			document.getElementById("yes").addEventListener("change", function() {
				if (this.checked) {
					document.getElementById("otherLangsUpload").style.display = "block";
				}
			});
			
			document.querySelector("form").addEventListener("submit", function(e) {
				var uni1 = document.getElementById("uni_list1").value.trim();
				var uni2 = document.getElementById("uni_list2").value.trim();
				var uni3 = document.getElementById("uni_list3").value.trim();

				if ((uni1 !== "" && uni1 === uni2) || 
					(uni1 !== "" && uni1 === uni3) || 
					(uni2 !== "" && uni2 === uni3)) {
					alert("Μην επιλέξετε το ίδιο πανεπιστήμιο παραπάνω από μία φορές.");
					e.preventDefault();
				}
			});
		</script>
	</body>
</html>