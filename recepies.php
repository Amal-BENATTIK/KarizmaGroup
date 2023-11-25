<!-- recipes.php -->
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "karizma";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch recipes from the database
$sql = "SELECT * FROM recipes";
$result = $conn->query($sql);




$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Recipe Cards</title>
</head>
<body>

<div class="container">
    <h2 class="my-4">Recipe Cards</h2>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-4 mb-4">
                <div class="card" style="width: 18rem;">
                <img src="./assets/img/recepies/<?php echo $row['recipe_image']; ?>" alt="Old Recipe Image" style="max-width: 100px; max-height: 100px;" class="img-fluid">

                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['recipe_name']; ?></h5>
                        <p class="card-text"><?php echo $row['recipe_duration']; ?> minutes</p>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recipeModal<?php echo $row['recipe_id']; ?>">
                            View Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recipe Modal -->
            <div class="modal fade" id="recipeModal<?php echo $row['recipe_id']; ?>" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="recipeModalLabel"><?php echo $row['recipe_name']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="<?php echo $row['recipe_image']; ?>" class="img-fluid mb-3" alt="Recipe Image">
                            <p><strong>Duration:</strong> <?php echo $row['recipe_duration']; ?> minutes</p>
                            <h5>Ingredients:</h5>
                            <ul>
                                <?php
                                // Fetch ingredients for the current recipe
                                $recipeId = $row['recipe_id'];
                                $ingredientSql = "SELECT ingredient_name FROM ingredients WHERE recipe_id = $recipeId";
                                $ingredientResult = $conn->query($ingredientSql);

                                if ($ingredientResult->num_rows > 0) {
                                    while ($ingredient = $ingredientResult->fetch_assoc()) {
                                        echo "<li>" . $ingredient['ingredient_name'] . "</li>";
                                    }
                                } else {
                                    echo "<li>No ingredients found.</li>";
                                }
                                ?>
                            </ul>
                            <h5>Steps:</h5>
                            <p><?php echo $row['steps']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
