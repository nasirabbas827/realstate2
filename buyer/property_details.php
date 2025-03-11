<?php
session_start();
include('config.php');

// Check if property_id is provided
if (!isset($_GET['property_id']) || empty($_GET['property_id'])) {
    die("Invalid Property ID.");
}

$property_id = $_GET['property_id'];

// Fetch property details
$sql = "SELECT * FROM properties WHERE property_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $property = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$property) {
    die("Property not found.");
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - <?php echo htmlspecialchars($property['title']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2><?php echo htmlspecialchars($property['title']); ?></h2>
    <div class="row">
        <div class="col-md-6">
            <img src="../seller/<?php echo htmlspecialchars($property['image']); ?>" class="img-fluid rounded shadow" alt="Property Image">
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th>Location</th>
                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>$<?php echo number_format($property['price']); ?></td>
                </tr>
                <tr>
                    <th>Property Type</th>
                    <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                </tr>
                <tr>
                    <th>Bedrooms</th>
                    <td><?php echo htmlspecialchars($property['bedrooms']); ?></td>
                </tr>
                <tr>
                    <th>Amenities</th>
                    <td><?php echo htmlspecialchars($property['amenities']); ?></td>
                </tr>
                <tr>
                    <th>Latitude</th>
                    <td><?php echo htmlspecialchars($property['latitude']); ?></td>
                </tr>
                <tr>
                    <th>Longitude</th>
                    <td><?php echo htmlspecialchars($property['longitude']); ?></td>
                </tr>
                <tr>
    <th>Status</th>
    <td><?php echo htmlspecialchars($property['status']); ?></td>
</tr>

            </table>
            
            <a href="contact_seller.php?property_id=<?php echo $property['property_id']; ?>" class="btn btn-primary">Contact Seller</a>
            <a href="buyer_dashboard.php" class="btn btn-secondary">Back to Listings</a>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
