<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$database = "karizma";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userId = $_POST["userId"];
    $username = $_POST["updateUsername"];
    $password = $_POST["updatePassword"];

    // Check if a new password is provided
    if (!empty($password)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE utilisateur SET mot_de_pass='$hashedPassword', nom_utilisateur='$username' WHERE id_utilisateur='$userId'";

        if ($conn->query($sql) === TRUE) {
            header("Location: user.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {

        $sql = "UPDATE utilisateur SET nom_utilisateur='$username' WHERE id_utilisateur='$userId'";

            if ($conn->query($sql) === TRUE) {
                header("Location: user.php");
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
    }
}

// Assuming $userId is the user's ID you want to edit
// Modify this according to your actual user identification method
$userId = isset($_GET["id"]) ? $_GET["id"] : null;

// Retrieve existing user data
if (!empty($userId)) {
    $sql = "SELECT id_utilisateur, nom_utilisateur FROM utilisateur WHERE id_utilisateur = '$userId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $userData = $result->fetch_assoc();

        // Assign user data to variables
        $existingUserId = $userData["id_utilisateur"];
        $existingUsername = $userData["nom_utilisateur"];
    } else {
        // Handle case where user ID doesn't exist
        echo "User not found!";
        exit;
    }
} else {
    // Handle case where user ID is not provided
    echo "User ID not provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>User CRUD Interface</title>
    <style>
        body {
            padding: 20px;
        }
        .user-table {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4">Edit User Interface</h2>

    <div>
        <h4>Edit User</h4>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Hidden input field for user ID -->
            <input type="hidden" name="userId" value="<?php echo $existingUserId; ?>">

            <div class="mb-3">
                <label for="updateUsername" class="form-label">Username</label>
                <input type="text" class="form-control" id="updateUsername" name="updateUsername" value="<?php echo $existingUsername; ?>" required>
            </div>
            <div class="mb-3">
                <label for="updatePassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="updatePassword" name="updatePassword" >
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
