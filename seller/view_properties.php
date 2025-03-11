<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

include('config.php');

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $sql = "DELETE FROM Properties WHERE property_id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $delete_id, $_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $property_type = $_POST['property_type'];
    $amenities = $_POST['amenities'];

    // Check if a new image is uploaded
    if ($_FILES['image']['name']) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        $image = $_POST['existing_image'];
    }

    $sql = "UPDATE Properties SET title=?, description=?, location=?, price=?, property_type=?, amenities=?, image=? WHERE property_id=? AND user_id=?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssdsssii", $title, $description, $location, $price, $property_type, $amenities, $image, $edit_id, $_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch user properties
$sql = "SELECT property_id, title, description, location, price, property_type, amenities, image FROM Properties WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Properties</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2 class="text-center">Your Properties</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Amenities</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                        <td><?php echo htmlspecialchars($property['description']); ?></td>
                        <td><?php echo htmlspecialchars($property['location']); ?></td>
                        <td><?php echo htmlspecialchars($property['price']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td><?php echo htmlspecialchars($property['amenities']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" width="100"></td>
                        <td>
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?php echo $property['property_id']; ?>">
                                Edit
                            </button>
                            <!-- Delete Form -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display:inline-block;">
                                <input type="hidden" name="delete_id" value="<?php echo $property['property_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $property['property_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $property['property_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?php echo $property['property_id']; ?>">Edit Property</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="edit_id" value="<?php echo $property['property_id']; ?>">
                                        <input type="hidden" name="existing_image" value="<?php echo $property['image']; ?>">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control" value="<?php echo $property['title']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <input type="text" name="description" class="form-control" value="<?php echo $property['description']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Location</label>
                                            <input type="text" name="location" class="form-control" value="<?php echo $property['location']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" name="price" class="form-control" value="<?php echo $property['price']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Property Type</label>
                                            <select name="property_type" class="form-control" required>
                                                <option value="rent" <?php if ($property['property_type'] == 'rent') echo 'selected'; ?>>Rent</option>
                                                <option value="sale" <?php if ($property['property_type'] == 'sale') echo 'selected'; ?>>Sale</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Amenities</label>
                                            <input type="text" name="amenities" class="form-control" value="<?php echo $property['amenities']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input type="file" name="image" class="form-control">
                                            <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" width="100" class="mt-2">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($properties)) : ?>
                    <tr>
                        <td colspan="8" class="text-center">No properties found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
