<?php
session_start();
include('config.php');

		// Fetch properties from the database
        $sqlmap = "SELECT p.property_id, p.title, p.status, p.description, p.location, p.price, 
        p.property_type, p.bedrooms, p.amenities, p.image, p.latitude, p.longitude 
        FROM Properties p";

$resultmap = mysqli_query($conn, $sqlmap);
$propertiesmap = mysqli_fetch_all($resultmap, MYSQLI_ASSOC);


// Initialize search filters
$search_query = "";
$location_filter = "";
$price_min = "";
$price_max = "";
$bedrooms_filter = "";
$amenities_filter = "";

// Handle search filtering
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search_query = $_GET['search'] ?? "";
    $location_filter = $_GET['location'] ?? "";
    $price_min = $_GET['price_min'] ?? "";
    $price_max = $_GET['price_max'] ?? "";
    $bedrooms_filter = $_GET['bedrooms'] ?? "";
    $amenities_filter = $_GET['amenities'] ?? "";
}

$sql = "SELECT p.property_id, p.title, p.status, p.description, p.location, p.price, 
        p.property_type, p.bedrooms, p.amenities, p.image 
        FROM Properties p WHERE 1=1"; // Ensures WHERE always exists

$param_types = "";
$param_values = [];

// Search by title or type
if (!empty($search_query)) {
    $sql .= " AND (p.title LIKE ? OR p.property_type LIKE ?)";
    $param_types .= "ss";
    $param_values[] = "%$search_query%";
    $param_values[] = "%$search_query%";
}

// Location filter
if (!empty($location_filter)) {
    $sql .= " AND p.location LIKE ?";
    $param_types .= "s";
    $param_values[] = "%$location_filter%";
}

// Price range filter
if (!empty($price_min) && !empty($price_max)) {
    $sql .= " AND p.price BETWEEN ? AND ?";
    $param_types .= "ii";
    $param_values[] = $price_min;
    $param_values[] = $price_max;
}

// Bedrooms filter
if (!empty($bedrooms_filter)) {
    $sql .= " AND p.bedrooms = ?";
    $param_types .= "i";
    $param_values[] = $bedrooms_filter;
}

// Amenities filter
if (!empty($amenities_filter)) {
    $sql .= " AND p.amenities LIKE ?";
    $param_types .= "s";
    $param_values[] = "%$amenities_filter%";
}

// Prepare and execute the query
if ($stmt = mysqli_prepare($conn, $sql)) {
    if (!empty($param_values)) {
        mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    die("Error preparing SQL statement: " . mysqli_error($conn));
}

mysqli_close($conn);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Real Estate Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

 <style>
.jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }
        .card-img-top{
            height: 200px;
        }
        #map {
            height: 500px;
            width: 100%;
            margin-bottom: 30px;
        }
		
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<div class="jumbotron text-center">
    <h1>Welcome to Real Estate Management System</h1>
    <p>Discover Your Perfect Home and List Your Property For Sale</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Explore</a>
</div>

<div class="container">
<h2 class="text-center mb-4">About Our Project</h2>
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary shadow">
            <div class="card-body">
                <h5 class="card-title">Advanced Search</h5>
                <p class="card-text">Find properties easily with location-based filtering and price range selection.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success shadow">
            <div class="card-body">
                <h5 class="card-title">Interactive Maps</h5>
                <p class="card-text">View property locations on an interactive map to make informed decisions.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white bg-danger shadow">
            <div class="card-body">
                <h5 class="card-title">Secure Transactions</h5>
                <p class="card-text">Ensure safe property dealings with our secure payment gateway integration.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning shadow">
            <div class="card-body">
                <h5 class="card-title">User-Friendly Dashboard</h5>
                <p class="card-text">Easily manage your listed properties and track buyer inquiries.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info shadow">
            <div class="card-body">
                <h5 class="card-title">Real-Time Updates</h5>
                <p class="card-text">Get instant notifications for new listings, price changes, and buyer messages.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-white bg-dark shadow">
            <div class="card-body">
                <h5 class="card-title">24/7 Support</h5>
                <p class="card-text">Our support team is available around the clock to assist with your needs.</p>
            </div>
        </div>
    </div>
</div>

</div>


<div class="container mt-5">
        <h2>Listed Properties</h2>
        <p>Here you can browse listings and make purchases.</p>
        
    <!-- Search Filters -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="form-row mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search by title or type" value="<?php echo htmlspecialchars($search_query); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="location" class="form-control" placeholder="Location" value="<?php echo htmlspecialchars($location_filter); ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="price_min" class="form-control" placeholder="Min Price" value="<?php echo htmlspecialchars($price_min); ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="price_max" class="form-control" placeholder="Max Price" value="<?php echo htmlspecialchars($price_max); ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="bedrooms" class="form-control" placeholder="Bedrooms" value="<?php echo htmlspecialchars($bedrooms_filter); ?>">
        </div>
        <div class="col-md-3 mt-2">
            <input type="text" name="amenities" class="form-control" placeholder="Amenities (e.g., Pool, Garage)" value="<?php echo htmlspecialchars($amenities_filter); ?>">
        </div>
        <div class="col-md-2 mt-2">
            <button type="submit" class="btn btn-outline-success">Search</button>
        </div>
    </form>

<!-- Properties Display -->
<div class="row">
    <?php foreach ($properties as $property) : ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 d-flex flex-column">
                <img src="./seller/<?php echo htmlspecialchars($property['image']); ?>" class="card-img-top" alt="Property Image">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($property['description']); ?></p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></li>
                        <li class="list-group-item"><strong>Price:</strong> $<?php echo number_format($property['price']); ?></li>
                        <li class="list-group-item"><strong>Type:</strong> <?php echo htmlspecialchars($property['property_type']); ?></li>
                        <li class="list-group-item">
                            <strong>Status:</strong> 
                            <span class="badge <?php echo ($property['status'] == 'Sold') ? 'badge-danger' : 'badge-success'; ?>">
                                <?php echo htmlspecialchars($property['status']); ?>
                            </span>
                        </li>
                        
                    </ul>
                </div>
                <div class="card-footer bg-white">
                    <a href="login.php" class="btn btn-info btn-sm">View Details</a>
                    <a href="login.php" class="btn btn-primary btn-sm">Contact Seller</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($properties)) : ?>
        <div class="col-12">
            <p class="text-center">No properties found.</p>
        </div>
    <?php endif; ?>
</div>
</div>

<div class="container">
    <!-- Map -->
<div id="map"></div>
</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2025 Real Estate Management System. All rights reserved.</p>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var map = L.map('map').setView([31.5204, 74.3587], 10); // Default center (Lahore)

        // Load OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var properties = <?php echo json_encode($propertiesmap); ?>; // Fetch properties from PHP

        properties.forEach(function(property) {
            var lat = parseFloat(property.latitude);
            var lng = parseFloat(property.longitude);

            if (!isNaN(lat) && !isNaN(lng)) {
                var marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(`<h6>${property.title}</h6>
                                <p>${property.location}</p>
                                <p>Price: $${property.price}</p>`);
            }
        });
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
