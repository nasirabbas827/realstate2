<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete property if requested
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_property'])) {
    $property_id = $_POST['delete_property'];

    // Delete property from the database
    $sql = "DELETE FROM Properties WHERE property_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $property_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch all properties with user details
$sql = "SELECT p.property_id, u.username, p.title,p.status, p.description, p.location, p.price, p.property_type, p.amenities, p.image
        FROM Properties p
        JOIN users u ON p.user_id = u.id";
$result = mysqli_query($conn, $sql);

$properties = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $properties[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Properties</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Price</th>
                <th>Type</th>
                <th>Amenities</th>
                <th>Status</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties as $property) : ?>
                <tr>
                    <td><?php echo $property['property_id']; ?></td>
                    <td><?php echo htmlspecialchars($property['username']); ?></td>
                    <td><?php echo htmlspecialchars($property['title']); ?></td>
                    <td><?php echo htmlspecialchars($property['description']); ?></td>
                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                    <td><?php echo $property['price']; ?></td>
                    <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                    <td><?php echo htmlspecialchars($property['amenities']); ?></td>
                    <td><?php echo htmlspecialchars($property['status']); ?></td>
                    <td><img src="../seller/<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" width="100"></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this property?')">
                            <input type="hidden" name="delete_property" value="<?php echo $property['property_id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
