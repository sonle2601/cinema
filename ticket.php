<?php
session_start();
include("connect.php");
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
// Fetch food items from the database
$stmt = $conn->prepare("SELECT * FROM food");
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error retrieving food data: " . $conn->error);
}

$foods = array();
while ($row = $result->fetch_assoc()) {
    $foods[] = $row;
}

$movieId = $_SESSION['movieId'];

$stmt = $conn->prepare("SELECT * FROM movie WHERE MovieID = ?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result1 = $stmt->get_result();
$movie = $result1->fetch_assoc();

$totalSeatsPrice = 0;
$result2 = null;
$seatID = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $showTimeId = $_POST['ShowTimeId'];
    $selectedSeatsJson = $_POST['SelectedSeats'];

    // Convert JSON string to PHP array
    $selectedSeats = json_decode($selectedSeatsJson);
    $seatID = $selectedSeats;
    $seatIds = implode(",", $selectedSeats);

    // Fetch selected seats information
    $stmt = $conn->prepare("SELECT * FROM seat WHERE SeatID IN ($seatIds)");
    $stmt->execute();
    $result2 = $stmt->get_result();

    // Calculate total seats price
    $totalSeatsPrice = $result2->num_rows * 50000;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Menu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .quantity {
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <!-- Left column for food menu -->
            <div class="col-md-8">
                <h2>Food list</h2>
                <div class="list-group">
                    <?php foreach ($foods as $food) : ?>
                        <div class="list-group-item">
                            <div class="media">
                                <img src="<?= htmlspecialchars($food['Image']); ?>" class="mr-3" alt="Ảnh đồ ăn" style="width: 100px;">
                                <div class="media-body">
                                    <h5 class="mt-0"><?= htmlspecialchars($food['Name']); ?></h5>
                                    <p>Type: <?= htmlspecialchars($food['Type']); ?></p>
                                    <p>Price: <?= number_format($food['Price'], 0, ',', '.'); ?> VND</p>
                                    <div class="quantity">
                                        Quantity <span id="quantity<?= $food['FoodID']; ?>">0</span>
                                        <button class="btn btn-sm btn-primary" onclick="increaseQuantity(<?= $food['FoodID']; ?>, <?= $food['Price']; ?>)">+</button>
                                        <button class="btn btn-sm btn-primary" onclick="decreaseQuantity(<?= $food['FoodID']; ?>, <?= $food['Price']; ?>)">-</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right column for movie details, selected seats, and payment -->
            <div class="col-md-4">
                <h2>Payment</h2>
                <div class="card">
                    <div class="card-body">
                        <img style="width: 10rem;" src="<?= htmlspecialchars($movie["Image"]) ?>" alt="">
                        <h5 class="mt-3"><?= htmlspecialchars($movie['Name']); ?></h5>
                        <p>Time: <?= htmlspecialchars($movie['Time']); ?> minute</p>
                        <p>Category: <?= htmlspecialchars($movie['Category']); ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mt-3">Selected seats</h5>
                        <!-- <p>Phòng 1</p> -->
                        <?php
                        if ($result2 && $result2->num_rows > 0) {
                            while ($seat = $result2->fetch_assoc()) {
                                echo "<p>Seat name: " . htmlspecialchars($seat['SeatNumber']) . "</p>";
                            }
                        } else {
                            echo "<p>Chưa có ghế nào được chọn</p>";
                        }
                        ?>
                        <p>Total seat price: <?= $totalSeatsPrice ?> VND</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5>Total price: <span id="totalPrice"><?= $totalSeatsPrice ?></span> VND</h5>
                        <form action="payment.php" method="post">
                            <input type="hidden" name="ShowTimeID" value="<?= $showTimeId ?>">
                            <input type="hidden" name="SeatID" value="<?= htmlspecialchars(json_encode($seatID)); ?>">
                            <input type="hidden" name="totalPrice" id="totalPriceInput" value="<?= $totalSeatsPrice ?>">
                            <input type="hidden" name="foodData" id="foodDataInput">

                            <button type="submit" id="totalPrice" name="payment" class="btn btn-primary">Pay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script>
        let quantities = {};
        let prices = {};

        <?php foreach ($foods as $food) : ?>
            quantities[<?= $food['FoodID']; ?>] = 0;
            prices[<?= $food['FoodID']; ?>] = <?= $food['Price']; ?>;
        <?php endforeach; ?>

        function updateTotalPrice() {
            let totalFoodPrice = 0;
            let foodData = {};

            Object.keys(quantities).forEach(function(key) {
                totalFoodPrice += quantities[key] * prices[key];
                if (quantities[key] > 0) {
                    foodData[key] = quantities[key];
                }
            });

            let totalPrice = totalFoodPrice + <?= $totalSeatsPrice ?>;
            document.getElementById('totalPrice').textContent = totalPrice.toLocaleString('vi-VN');
            document.getElementById('totalPriceInput').value = totalPrice;

            document.getElementById('foodDataInput').value = JSON.stringify(foodData);
        }

        function increaseQuantity(foodId, price) {
            quantities[foodId]++;
            document.getElementById('quantity' + foodId).textContent = quantities[foodId];
            updateTotalPrice();
        }

        function decreaseQuantity(foodId, price) {
            if (quantities[foodId] > 0) {
                quantities[foodId]--;
                document.getElementById('quantity' + foodId).textContent = quantities[foodId];
                updateTotalPrice();
            }
        }
    </script>
</body>

</html>