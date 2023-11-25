<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Recipe List</title>
    
</head>
<body>

<div class="container">
    <h2 class="my-4">Recipe List</h2>
    <button type="button" id="add_recipe" class="btn btn-success" onclick="add_recipe()">Add Recipe</button>
    <br><br>
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

    // Retrieve recipes from the database
    $sql = "SELECT * FROM recipes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        ?>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Recipe Name</th>
                <th>Duration (minutes)</th>
                <th>Ingredients</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['recipe_id']; ?></td>
                    <td><img src="./assets/img/recepies/<?php echo $row['recipe_image']; ?>" alt="Recipe Image" style="max-width: 100px; max-height: 100px;"></td>
                    <td><?php echo $row['recipe_name']; ?></td>
                    <td><?php echo $row['recipe_duration']; ?></td>
                    <td><?php echo getIngredients($row['recipe_id']); ?></td>
                    <td>
                        <a href="recepie_update.php?id=<?php echo $row['recipe_id']; ?>" class="btn btn-info btn-sm">Edit</a>
                        <button onclick="confirmDelete(<?php echo $row['recipe_id']; ?>)" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    } else {
        echo "No recipes found.";
    }

    function getIngredients($recipeId) {
        global $conn;
        $ingredients = "";
        $sql = "SELECT ingredient_name FROM ingredients WHERE recipe_id = $recipeId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ingredients .= $row['ingredient_name'] . ", ";
            }
        }

        return $ingredients;
    }

    $conn->close();
    ?>

    <script>

        function add_recipe() {
            window.location.href = "recepie_create.php";
        }
        function confirmDelete(recipeId) {
            if (confirm("Are you sure you want to delete this recipe?")) {
                window.location.href = "recepie_delete.php?id=" + recipeId;
            }
        }
    </script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
