<?php
session_start();

// Check if the user is logged in and is a Buyer
if (!isset($_SESSION["id"]) || $_SESSION["user_type"] != "Buyer") {
    header("location: login.php");
    exit;
}

include('config.php');
require_once('../stripe-php-master/init.php');

// Stripe API keys
$stripe_public_key = 'pk_test_51PQinLRrUKhdzOsDnpHkYJbi0HZIsF9xOVIcPZtsAr4nbH5h1p3o1jblMCPoB0glvFG3o1pbxQZLSiKRHgvuZRMt009qg1bTkq';
$stripe_secret_key = "YOUR_OWN_API_KEY";

\Stripe\Stripe::setApiKey($stripe_secret_key);

// Check if a property ID is provided
if (!isset($_GET['property_id'])) {
    die("Invalid request.");
}

$property_id = $_GET['property_id'];

// Fetch property details
$sql = "SELECT title, price FROM Properties WHERE property_id = ?";
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
<html>
<head>
    <title>Purchase Property</title>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Purchase Property</h2>
        <p><strong>Property:</strong> <?php echo htmlspecialchars($property['title']); ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($property['price'], 2)); ?></p>

        <form action="stripe_payment.php" method="POST">
            <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $property['price']; ?>">
            <script
                src="https://checkout.stripe.com/checkout.js"
                class="stripe-button"
                data-key="<?php echo $stripe_public_key; ?>"
                data-amount="<?php echo $property['price'] * 100; ?>"
                data-name="Real Estate Purchase"
                data-description="Payment for <?php echo htmlspecialchars($property['title']); ?>"
                data-currency="usd">
            </script>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
