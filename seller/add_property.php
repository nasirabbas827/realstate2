<?php
session_start();

// Check if the user is logged in and is a Seller-Agent
if (!isset($_SESSION["id"]) || $_SESSION["user_type"] != "Seller-Agent") {
    header("location: login.php");
    exit;
}

include('config.php');

// Initialize variables
$title = $description = $location = $price = $property_type = $bedrooms = $amenities = $latitude = $longitude = "";
$status = "Active"; // Default status
$errors = [];
$success_msg = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate inputs
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $location = trim($_POST["location"]);
    $price = trim($_POST["price"]);
    $property_type = trim($_POST["property_type"]);
    $bedrooms = trim($_POST["bedrooms"]);
    $amenities = trim($_POST["amenities"]);
    $latitude = trim($_POST["latitude"]);
    $longitude = trim($_POST["longitude"]);

    // Basic validation
    if (empty($title)) $errors[] = "Title is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if (empty($location)) $errors[] = "Location is required.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Valid price is required.";
    if (empty($property_type)) $errors[] = "Property type is required.";
    if (!is_numeric($bedrooms) || $bedrooms <= 0) $errors[] = "Valid number of bedrooms is required.";
    if (!is_numeric($latitude) || !is_numeric($longitude)) $errors[] = "Valid latitude and longitude are required.";

    // Image upload handling
    $image = "";
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
            $errors[] = "File is not a valid image.";
        } elseif ($_FILES["image"]["size"] > 500000) {
            $errors[] = "File size too large.";
        } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            $errors[] = "Failed to upload image.";
        }
    }

    // Insert into database if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO properties (user_id, title, description, location, price, property_type, bedrooms, amenities, latitude, longitude, image, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssss", $_SESSION["id"], $title, $description, $location, $price, $property_type, $bedrooms, $amenities, $latitude, $longitude, $image, $status);

        if ($stmt->execute()) {
            $success_msg = "Property added successfully.";
        } else {
            $errors[] = "Error adding property.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Add Property</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="text-center">Add Property</h2>

            <!-- Display errors -->
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Display success message -->
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter property title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter property description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" placeholder="Enter property location" required>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" placeholder="Enter property price" required>
                </div>
                <div class="form-group">
                    <label>Property Type</label>
                    <select name="property_type" class="form-control" required>
                        <option value="">Select</option>
                        <option value="rent">Rent</option>
                        <option value="sale">Sale</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Number of Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control" placeholder="Enter number of bedrooms" required>
                </div>
                <div class="form-group">
                    <label>Amenities</label>
                    <textarea name="amenities" class="form-control" placeholder="Enter property amenities (comma separated)" required></textarea>
                </div>
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" class="form-control" placeholder="Enter property latitude" required>
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" class="form-control" placeholder="Enter property longitude" required>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control-file">
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Add Property">
                    <a class="btn btn-outline-dark" href="view_properties.php">View Properties</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
