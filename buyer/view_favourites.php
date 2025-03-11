<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

include('config.php');

// Handle property removal from favorites
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_favorite_id'])) {
    $property_id = $_POST['remove_favorite_id'];
    $user_id = $_SESSION["id"];

    // Delete the favorite property from the database
    $sql = "DELETE FROM Favorites WHERE user_id = ? AND property_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $property_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch favorite properties for the user, including status
$sql = "SELECT p.property_id, p.title, p.description, p.location, p.price, p.property_type, p.amenities, p.image, p.status
        FROM Properties p 
        JOIN Favorites f ON p.property_id = f.property_id
        WHERE f.user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    $user_id = $_SESSION["id"];
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $favorite_properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Fetch purchased properties for the user
$sql = "SELECT p.property_id, p.title, p.description, p.location, p.price, p.property_type, p.amenities, p.image, py.amount, py.transaction_id, py.payment_status, py.payment_date 
        FROM Payments py 
        JOIN Properties p ON py.property_id = p.property_id 
        WHERE py.buyer_id = ? 
        ORDER BY py.payment_date DESC";
if ($stmt = mysqli_prepare($conn, $sql)) {
    $user_id = $_SESSION["id"];
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $purchased_properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Favorite & Purchased Properties</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <h2>Your Favorite Properties</h2>
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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($favorite_properties as $property) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                        <td><?php echo htmlspecialchars($property['description']); ?></td>
                        <td><?php echo htmlspecialchars($property['location']); ?></td>
                        <td>$<?php echo number_format($property['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td><?php echo htmlspecialchars($property['amenities']); ?></td>
                        <td><img src="../seller/<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" width="100"></td>
                        <td>
                            <span class="badge <?php echo ($property['status'] === 'Active') ? 'badge-success' : 'badge-secondary'; ?>">
                                <?php echo htmlspecialchars($property['status']); ?>
                            </span>
                        </td>
                        <td>
                            <!-- Remove from favorites form -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display:inline-block;">
                                <input type="hidden" name="remove_favorite_id" value="<?php echo $property['property_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>

                            <!-- Purchase Now button if status is Active -->
                            <?php if ($property['status'] === 'Active') : ?>
                                <a href="purchase.php?property_id=<?php echo $property['property_id']; ?>" class="btn btn-success btn-sm">Purchase Now</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($favorite_properties)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">No favorite properties found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 class="mt-5 mb-5">Your Purchased Properties</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Amount Paid</th>
                    <th>Transaction ID</th>
                    <th>Payment Status</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchased_properties as $property) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                        <td><?php echo htmlspecialchars($property['location']); ?></td>
                        <td>$<?php echo number_format($property['price'], 2); ?></td>
                        <td><img src="../seller/<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" width="100"></td>
                        <td>$<?php echo number_format($property['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($property['transaction_id']); ?></td>
                        <td>
                            <span class="badge <?php echo ($property['payment_status'] === 'Completed') ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo htmlspecialchars($property['payment_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date("F j, Y, g:i A", strtotime($property['payment_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($purchased_properties)) : ?>
                    <tr>
                        <td colspan="11" class="text-center">No purchased properties found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
