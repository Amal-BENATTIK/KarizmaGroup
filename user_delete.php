<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}
// Assuming you have a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "karizma";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the recipe ID is provided
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Delete recipe from the database
    $deleteuserIdSql = "DELETE FROM utilisateur WHERE id_utilisateur = $userId";

    if ($conn->query($deleteuserIdSql)) {
        // Recipe deleted successfully, redirect to the recipe list page
        header("Location: user.php");
        exit;
    } else {
        echo "Error deleting user: " . $conn->error;
    }
} else {
    echo "User ID not provided.";
}

$conn->close();
?>
