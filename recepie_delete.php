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
    $recipeId = $_GET['id'];

    // Delete recipe from the database
    $deleteRecipeSql = "DELETE FROM recipes WHERE recipe_id = $recipeId";
    $deleteIngredientsSql = "DELETE FROM ingredients WHERE recipe_id = $recipeId";

    if ($conn->query($deleteRecipeSql) === TRUE && $conn->query($deleteIngredientsSql) === TRUE) {
        // Recipe deleted successfully, redirect to the recipe list page
        header("Location: recepies_list.php");
        exit;
    } else {
        echo "Error deleting recipe: " . $conn->error;
    }
} else {
    echo "Recipe ID not provided.";
}

$conn->close();
?>
