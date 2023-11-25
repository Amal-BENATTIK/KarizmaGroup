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

<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["createUsername"];
    $password = $_POST["createPassword"];

    // Hash the password
    $hashedPassword = md5($password);


   

    
    $mysqli = new mysqli("localhost", "root", "", "karizma");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Perform SQL insertion with hashed password
    $sql = "INSERT INTO utilisateur (nom_utilisateur, mot_de_pass) VALUES ('$username', '$hashedPassword')";

    if ($mysqli->query($sql) === TRUE) {
        header("Location: user.php", true, 301);  
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }

    // Close the connection
    $mysqli->close();
}
?>

<div class="container">
    <h2 class="my-4">Create User Interface</h2>

    <div class="mb-4">
        <h4>Create User</h4>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="createUsername" class="form-label">Username</label>
                <input type="text" class="form-control" id="createUsername" name="createUsername" required>
            </div>
            <div class="mb-3">
                <label for="createPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="createPassword" name="createPassword" required>
            </div>
            <button type="submit" class="btn btn-success">Create User</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
