<?php
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["user_type"] != "Buyer") {
    header("location: login.php");
    exit;
}

include('config.php');
require_once('../stripe-php-master/init.php');

$stripe_secret_key = "YOUR_OWN_API_KEY";
\Stripe\Stripe::setApiKey($stripe_secret_key);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $buyer_id = $_SESSION["id"];
    $property_id = $_POST['property_id'];
    $amount = $_POST['amount'] * 100; // Convert to cents
    $token = $_POST['stripeToken'];

    try {
        // Charge the customer
        $charge = \Stripe\Charge::create([
            "amount" => $amount,
            "currency" => "usd",
            "description" => "Payment for property ID: $property_id",
            "source" => $token
        ]);

        if ($charge->status == 'succeeded') {
            $transaction_id = $charge->id;
            $payment_status = 'Succeeded'; // Assign status first

            // Insert payment record into the Payments table
            $sql = "INSERT INTO Payments (buyer_id, property_id, amount, transaction_id, payment_status) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $amountInDollars = $amount / 100; // Convert amount back to dollars
                mysqli_stmt_bind_param($stmt, "iidss", $buyer_id, $property_id, $amountInDollars, $transaction_id, $payment_status);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Update property status to "Sold"
            $sql = "UPDATE Properties SET status = 'Sold' WHERE property_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $property_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Redirect to buyer dashboard
            header("Location: buyer_dashboard.php");
            exit;
        }
    } catch (Exception $e) {
        echo "Payment failed: " . $e->getMessage();

        // Insert failed payment attempt
        $sql = "INSERT INTO Payments (buyer_id, property_id, amount, transaction_id, payment_status) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            $transaction_id = "N/A"; // No valid transaction
            $payment_status = "Failed"; // Assign failed status
            $amountInDollars = $amount / 100;

            mysqli_stmt_bind_param($stmt, "iidss", $buyer_id, $property_id, $amountInDollars, $transaction_id, $payment_status);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($conn);

?>

