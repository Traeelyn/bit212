<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BIT212";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Create the orders table if it doesn't exist
$foodOrders = "CREATE TABLE IF NOT EXISTS orders (
    foodID INT AUTO_INCREMENT PRIMARY KEY,
    foodName TEXT NOT NULL, 
    quantity INT NOT NULL, 
    price DECIMAL(5, 2) NOT NULL
)";

if ($conn->query($foodOrders) !== TRUE) {
    //echo 'Error creating orders table: ' . $conn->error;
} else {
    //echo "Table created successfully or already exists.";
}

// Check if the table already contains data
$result = $conn->query("SELECT COUNT(*) AS count FROM orders");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Insert sample values into the orders table only if it's empty
    $insertValues = "INSERT INTO orders (foodName, quantity, price) VALUES 
                    ('Pan Mee', 0, 9.00),
                    ('Chicken Noodle', 0, 7.00),
                    ('Curry Mee', 0, 10.00),
                    ('Rojak', 0, 8.00),
                    ('Braised Chicken & Rice', 0, 12.00),
                    ('Chicken Rice', 0, 13.00)";
    
    if ($conn->query($insertValues) === TRUE) {
        //echo "Sample data inserted successfully.";
    } else {
       // echo 'Error inserting data: ' . $conn->error;
    }
} else {
   // echo "Table already contains data, skipping sample data insertion.";
}

// Initialize an array to hold the current quantities
$currentQuantities = [];

// Fetch current quantities from the orders table
$result = $conn->query("SELECT foodID, quantity FROM orders");
while ($row = $result->fetch_assoc()) {
    $currentQuantities[$row['foodID']] = $row['quantity'];
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['quantity'] as $foodID => $quantity) {
        $quantity = intval($quantity); // Ensure quantity is an integer
        
        // Fetch the current quantity for this foodID
        $currentQuantityResult = $conn->query("SELECT quantity FROM orders WHERE foodID = $foodID");
        $currentQuantityRow = $currentQuantityResult->fetch_assoc();
        $currentQuantity = $currentQuantityRow['quantity'];

        // Add the new quantity to the current quantity
        $newQuantity = $currentQuantity + $quantity;
        
        // Update the quantity in the database
        $updateQuery = "UPDATE orders SET quantity = $newQuantity WHERE foodID = $foodID";
        if ($conn->query($updateQuery) !== TRUE) {
            echo "Error updating quantity for foodID $foodID: " . $conn->error;
        }
    }
    echo "Order quantities updated successfully.";

}


// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="menu.css" rel="stylesheet">    
</head>
<body>
    <nav>                
        <div class="container">
            <img src="images/logo.png" alt="Sakae-Sushi-Logo" class="logo-image">
            <div class="nav-bar" id="navbarNav">
                <ul>
                    <li class="nav-item"><a href="aboutUs.php">About Us</a></li>
                    <li class="nav-item"><a href="menu.php">Menu</a></li>
                    <li class="nav-item"><a href="orderHistory.php">Order History</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <form class="section-1" method="post" action="" >
            <h2>Our Menu</h2>
            <div class="menu-image">
                <div class="menu-item-div">
                    <img src="images/food-1.jpeg" alt="BestSeller-1" class="menu-image">
                    <p class="order-title">Sashimi<br><span class="description">Fresh sashimi from japan<br>served with wasabi</span><br><br><span class="price">RM9.00</span></p>
                    <?php echo '<div class="button-container">
                        <button class="quantity-btn-minus" type="button">-</button>
                        <input type="text" name="quantity[1]" class="quantity" value="0" readonly>
                        <button class="quantity-btn-plus" type="button">+</button>
                    </div>'; ?>
                </div>
                <div class="menu-item-div">
                    <img src="images/food-2.jpg" alt="BestSeller-2" class="menu-image">
                    <p class="order-title">Chcken Karaage<br><span class="description">Fresh from the kitchen chicken karaage<br>served with mayonnaise and cabbage</span><br><br><span class="price">RM7.00</span></p>
                    <?php echo '<div class="button-container">
                        <button class="quantity-btn-minus" type="button">-</button>
                        <input type="text" name="quantity[2]" class="quantity" value="0" readonly>
                        <button class="quantity-btn-plus" type="button">+</button>
                    </div>'; ?>
                </div>
                <div class="menu-item-div">
                    <img src="images/food-3.webp" alt="BestSeller-3" class="menu-image">
                    <p class="order-title">Sushi<br><span class="description">filled with avacados, cucumber and<br>crabmeat seasoned with sesame seeds</span><br><br><span class="price">RM10.00</span></p>
                    <?php echo '<div class="button-container">
                        <button class="quantity-btn-minus" type="button">-</button>
                        <input type="text" name="quantity[3]" class="quantity" value="0" readonly>
                        <button class="quantity-btn-plus" type="button">+</button>
                    </div>'; ?>
                </div>
            </div>

            <div class="menu-image-2">
                <div class="menu-item-div">
                    <img src="images/food-4.jpeg" alt="BestSeller-1" class="menu-image">
                    <p class="order-title">Soba<br><span class="description">Chilled soba topped with seaweed<br>served with spring onions and wasabi</span><br><br><span class="price">RM8.00</span></p>
                    <?php echo '<div class="button-container">
                        <button class="quantity-btn-minus" type="button">-</button>
                        <input type="text" name="quantity[4]" class="quantity" value="0" readonly>
                        <button class="quantity-btn-plus" type="button">+</button>
                    </div>'; ?>
                </div>
                <div class="menu-item-div">
                    <img src="images/food-6.jpg" alt="BestSeller-2" class="menu-image">
                    <p class="order-title">Sashimi Don<br><span class="description">Served with chopped avacados<br>and cucumbers topped with seaweed</span><br><br><span class="price">RM12.00</span></p>
                    <?php echo '<div class="button-container">
                        <button class="quantity-btn-minus" type="button">-</button>
                        <input type="text" name="quantity[5]" class="quantity" value="0" readonly>
                        <button class="quantity-btn-plus" type="button">+</button>
                    </div>'; ?>
                </div>
                <div class="menu-item-div">
                    <img src="images/food-5.jpg" alt="BestSeller-3" class="menu-image">
                    <p class="order-title">Ramen<br><span class="description">Served with onsen egg, mushrooms and<br>pork topped with spring onions</span><br><br><span class="price">RM13.00</span></p>
                    <?php echo '<div class="button-container">
                        <button class="quantity-btn-minus" type="button">-</button>
                        <input type="text" name="quantity[6]" class="quantity" value="0" readonly>
                        <button class="quantity-btn-plus" type="button">+</button>
                    </div>'; ?>
                </div>
            </div>

            <div class="submit-button-container">
                <button class="submit-btn" type="submit">Submit Order</button>
            </div>
        </form>
    </main>
    <script>
    // JavaScript to handle quantity changes
    document.querySelectorAll('.menu-item-div').forEach((menuItem) => {
        const plusButton = menuItem.querySelector('.quantity-btn-plus');
        const minusButton = menuItem.querySelector('.quantity-btn-minus');
        const quantityInput = menuItem.querySelector('.quantity');

        plusButton.addEventListener('click', (event) => {
            event.preventDefault();
            let quantity = parseInt(quantityInput.value, 10);
            quantityInput.value = quantity + 1;
        });

        minusButton.addEventListener('click', (event) => {
            event.preventDefault();
            let quantity = parseInt(quantityInput.value, 10);
            if (quantity > 0) {
                quantityInput.value = quantity - 1;
            }
        });
    });

    // AJAX form submission to update database without resetting actual table values
    document.querySelector('.section-1').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        
        // Perform AJAX request
        fetch('', { method: 'POST', body: new FormData(this) })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Check response from server
                alert('Order quantities updated successfully.');
                
                // Reset quantity inputs on the frontend without affecting database values
                document.querySelectorAll('.quantity').forEach((input) => {
                    input.value = 0; // Set each quantity input back to 0
                });
            })
            .catch(error => console.error('Error:', error));
    });
</script>

</body>
</html>
