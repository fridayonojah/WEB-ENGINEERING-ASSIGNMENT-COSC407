<?php
$host = 'localhost';
$db   = 'registration_db';
$user = 'root'; 
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $age      = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $gender   = $_POST['gender'] ?? 'Not Specified';
    $country  = $_POST['country'];
    $bio      = htmlspecialchars(trim($_POST['bio']));
    $interests = isset($_POST['interests']) ? implode(", ", $_POST['interests']) : "None";

    // Validation
    if (empty($fullname) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid data submitted.");
    }

    // 3. Database Insertion
    $sql = "INSERT INTO users (fullname, email, age, gender, country, interests, bio) 
            VALUES (:fn, :em, :ag, :ge, :co, :it, :bi)";
    
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':fn' => $fullname, ':em' => $email, ':ag' => $age, 
            ':ge' => $gender, ':co' => $country, ':it' => $interests, ':bi' => $bio
        ]);
        echo "<h1>Success!</h1><p>Registration complete for $fullname.</p>";
    } catch (Exception $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>