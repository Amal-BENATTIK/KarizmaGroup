<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Recipe</title>
</head>
<body>

<div class="container">
    <h2 class="my-4">Edit Recipe</h2>

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

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get values from the form
        $recipeId = $_POST['recipeId'];
        $recipeName = $_POST['recipeName'];
        $recipeDuration = $_POST['recipeDuration'];
        $recipeImage = isset($_FILES['recipeImage']) ? $_FILES['recipeImage']['name'] : ''; // Check if a new image is provided
        $steps = $_POST['steps'];
        $ingredients = $_POST['ingredients'];
        $removedIngredients = isset($_POST['removedIngredients']) ? $_POST['removedIngredients'] : [];

        // Update the recipe in the recipes table
        $sql = "UPDATE recipes 
                SET recipe_name = '$recipeName', recipe_duration = $recipeDuration, steps = '$steps'";

        // Check if a new image is provided
        if ($recipeImage !== '') {
            $sql .= ", recipe_image = '$recipeImage'";
        }

        $sql .= " WHERE recipe_id = $recipeId";

        if ($conn->query($sql) === TRUE) {
            // Check if a new image is provided and save it to the project folder
            if ($recipeImage !== '') {
                $targetDir = "./assets/img/recepies/"; // Adjust the path as needed
                $targetFile = $targetDir . basename($recipeImage);
                move_uploaded_file($_FILES["recipeImage"]["tmp_name"], $targetFile);
            }

            // Delete existing ingredients for the recipe that were removed
            foreach ($removedIngredients as $removedIngredientId) {
                $conn->query("DELETE FROM ingredients WHERE ingredient_id = $removedIngredientId");
            }

            // Insert new ingredients into the ingredients table
            foreach ($ingredients as $ingredient) {
                $ingredientName = $ingredient;
                $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name) VALUES ($recipeId, '$ingredientName')");
            }

            // Redirect to the recipe list
            header("Location:  recepies_list.php");
            exit;
        } else {
            echo "Error updating recipe: " . $conn->error;
        }
    }

    // Check if the recipe ID is provided in the URL
    if (isset($_GET['id'])) {
        $recipeId = $_GET['id'];

        // Retrieve recipe details from the database
        $recipeSql = "SELECT * FROM recipes WHERE recipe_id = $recipeId";
        $recipeResult = $conn->query($recipeSql);

        if ($recipeResult->num_rows > 0) {
            $recipe = $recipeResult->fetch_assoc();

            // Retrieve ingredients for the recipe
            $ingredientsSql = "SELECT ingredient_id, ingredient_name FROM ingredients WHERE recipe_id = $recipeId";
            $ingredientsResult = $conn->query($ingredientsSql);
            $ingredientArray = array();

            while ($ingredient = $ingredientsResult->fetch_assoc()) {
                $ingredientArray[] = $ingredient;
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="recipeId" value="<?php echo $recipe['recipe_id']; ?>">
                <div class="mb-3">
                    <label for="recipeName" class="form-label">Recipe Name</label>
                    <input type="text" class="form-control" id="recipeName" name="recipeName" value="<?php echo $recipe['recipe_name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="recipeDuration" class="form-label">Recipe Duration (in minutes)</label>
                    <input type="number" class="form-control" id="recipeDuration" name="recipeDuration" value="<?php echo $recipe['recipe_duration']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="oldRecipeImage" class="form-label">Old Recipe Image</label>
                    <img src="./assets/img/recepies/<?php echo $recipe['recipe_image']; ?>" alt="Old Recipe Image" style="max-width: 100px; max-height: 100px;" class="img-fluid">
                </div>
                <div class="mb-3">
                    <label for="recipeImage" class="form-label">Recipe Image</label>
                    <input type="file" class="form-control" id="recipeImage" name="recipeImage" accept="image/*">
                </div>
                <div id="ingredientInputs">
                    <?php foreach ($ingredientArray as $index => $ingredient) { ?>
                        <div class="mb-3">
                            <label for="ingredient<?php echo $ingredient['ingredient_id']; ?>" class="form-label">Ingredient <?php echo $index + 1; ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ingredient<?php echo $ingredient['ingredient_id']; ?>" name="ingredients[]" value="<?php echo $ingredient['ingredient_name']; ?>" required>
                                <button type="button" class="btn btn-danger" onclick="removeIngredientInput(<?php echo $ingredient['ingredient_id']; ?>)">Remove</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addIngredientInput()">Add Ingredient</button>
                <div class="mb-3">
                    <label for="steps" class="form-label">Steps to Prepare</label>
                    <textarea class="form-control" id="steps" name="steps" rows="5" required><?php echo $recipe['steps']; ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Update Recipe</button>
            </form>
            <?php
        } else {
            echo "Recipe not found.";
        }
    } else {
        echo "Recipe ID not provided.";
    }

    $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let ingredientCount = <?php echo count($ingredientArray); ?>;

        function addIngredientInput() {
            ingredientCount++;
            const ingredientInputs = document.getElementById("ingredientInputs");
            const newIngredientInput = document.createElement("div");
            newIngredientInput.classList.add("mb-3");
            newIngredientInput.innerHTML = `
                <label for="ingredient${ingredientCount}" class="form-label">Ingredient ${ingredientCount}</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="ingredient${ingredientCount}" name="ingredients[]" required>
                    <button type="button" class="btn btn-danger" onclick="removeIngredientInput(${ingredientCount})">Remove</button>
                </div>
            `;
            ingredientInputs.appendChild(newIngredientInput);
        }

        function removeIngredientInput(ingredientId) {
            const ingredientInputs = document.getElementById("ingredientInputs");
            const ingredientToRemove = document.getElementById("ingredient" + ingredientId).closest('.mb-3');
            ingredientInputs.removeChild(ingredientToRemove);

            // Add the removed ingredient ID to a hidden input for processing in the backend
            const removedIngredientsInput = document.createElement("input");
            removedIngredientsInput.type = "hidden";
            removedIngredientsInput.name = "removedIngredients[]";
            removedIngredientsInput.value = ingredientId;
            document.getElementById("ingredientInputs").appendChild(removedIngredientsInput);
            removedIngredientsInput.parentNode.removeChild(removedIngredientsInput);

        }
    </script>
</div>

</body>
</html>
