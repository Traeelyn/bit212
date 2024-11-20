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

    // SQL to create the 'orders' table if it does not exist
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS Orders (
            foodID INT AUTO_INCREMENT PRIMARY KEY,
            foodName VARCHAR(255) NOT NULL,
            quantity INT DEFAULT 0,
            price DECIMAL(10, 2) DEFAULT 0.00
        );
    ";
    $conn->query($createTableSQL);

    // Check if rows already exist in the 'orders' table
    $checkRowsSQL = "SELECT COUNT(*) AS rowCount FROM Orders";
    $result = $conn->query($checkRowsSQL);
    $row = $result->fetch_assoc();
    $rowCount = $row['rowCount'];

    // If no rows exist, insert default rows
    if ($rowCount == 0) {
        $insertRowsSQL = "
            INSERT INTO Orders (foodName, quantity, price) VALUES
            ('Sashimi', 0, 9.00),
            ('Chicken Karaage', 0, 7.00),
            ('Sushi', 0, 10.00),
            ('Soba', 0, 8.00),
            ('Sashimi Don', 0, 12.00),
            ('Ramen', 0, 13.00);
        ";
        $conn->query($insertRowsSQL);
    }
    $retrieveSQL = "SELECT foodID, foodName, quantity, price, (quantity * price) AS totalPrice FROM Orders WHERE quantity > 0";
    $result = $conn->query($retrieveSQL);

    $orderHistory = [];
    $totalBill = 0.00;

    // Fetch records
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orderHistory[] = $row;
            $totalBill += $row['totalPrice'];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeFoodID'])) {
        $foodIDToRemove = (int)$_POST['removeFoodID'];
        $resetQuantitySQL = "UPDATE Orders SET quantity = 0 WHERE foodID = ?";
        $stmt = $conn->prepare($resetQuantitySQL);
        $stmt->bind_param("i", $foodIDToRemove);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
        exit;
    }


    $conn->close();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="orderHistory.css" rel="stylesheet"> 
    </head>
    <body>

    <nav>                
        <div class="container">
            <img src="images/logo.png" alt="Anak-Kampung-Logo" class="logo-image">
            <div class="nav-bar" id="navbarNav">
                <ul>
                    <li class="nav-item"><a href="aboutUs.php">About Us</a></li>
                    <li class="nav-item"><a href="menu.php">Menu</a></li>
                    <li class="nav-item"><a href="orderHistory.php">Order History</a></li>
                </ul>
            </div>
        </div>
    </nav>
        
    <table id="orderTable">
        <tr>
            <th>Food ID</th>
            <th>Food Name</th>
            <th>Quantity</th>
            <th>Price per Unit</th>
            <th>Total Price per Dish</th>
            <th></th>
        </tr>
        <?php if (!empty($orderHistory)): ?>
            <?php foreach ($orderHistory as $record): ?>
                <tr>
                <td><?php echo htmlspecialchars($record['foodID']); ?></td>
                <td><?php echo htmlspecialchars($record['foodName']); ?></td>
                <td><?php echo htmlspecialchars($record['quantity']); ?></td>
                <td><?php echo "$" . htmlspecialchars($record['price']); ?></td>
                <td><?php echo "$" . htmlspecialchars(number_format($record['totalPrice'], 2)); ?></td>
                <td><button onclick="removeOrder(<?php echo htmlspecialchars($record['foodID']); ?>)">Remove</button></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Display an empty row if there are no orders -->
            <tr>
                <td colspan="5" style="text-align: center;">No orders found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Separate table for Total Bill -->
    <table>
        <tr class="totalBillTable">
            <td class="totalBillLabel">Total Bill:</td>
            <td>$<?php echo htmlspecialchars($totalBill); ?></td>
        </tr>
    </table>
    <script>
        function removeOrder(foodID) {
            if (confirm('Are you sure you want to remove this order?')) {
                fetch('orderHistory.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({ removeFoodID: foodID })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order removed successfully!');
                        location.reload(); // Reload the page to update the table
                    } else {
                        alert('Failed to remove order.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>

    </body>
</html>
