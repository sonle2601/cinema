<?php
session_start();
include("connect.php");
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
// Lấy các giá trị từ POST
$seatIds = $_POST["SeatID"]; // $seatIds là một mảng các SeatID từ POST
$totalAmount = $_POST["totalPrice"];
$movieId = $_SESSION['movieId'];
$user = $_SESSION['user'];
$showTimeId = $_POST['ShowTimeID'];
$price = 50000;
$selectedSeats = json_decode($seatIds);
// Lấy ngày giờ hiện tại
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$foods = $_POST['foodData']; // Đây là foodID mà bạn cần
$selectedFoods = json_decode($foods);
// $foodQuantity = $_POST['foodQuantity']; //


$sql = "INSERT INTO payment (CustomerID, PaymentTime, PaymentDate, PaymentMethod, TotalAmount)
        VALUES ({$user['CustomerID']}, '{$currentTime}', '{$currentDate}', 'card', {$totalAmount})";

// Thực thi câu lệnh SQL chèn dữ liệu vào bảng payment
if ($conn->query($sql) === TRUE) {
    $paymentID = $conn->insert_id;

    foreach ($selectedSeats as $seatId) {
        $sql = "INSERT INTO ticket (CustomerID, ShowTimeID, SeatID, MovieID, PaymentID, Price, Status)
                VALUES ({$user['CustomerID']}, {$showTimeId}, {$seatId}, {$movieId}, {$paymentID}, {$price}, 0)";

        $updateSeat = "UPDATE seat SET Status=1 WHERE SeatID = $seatId";
        $conn->query($updateSeat);
        // Thực thi câu lệnh SQL chèn dữ liệu vào bảng ticket
        if ($conn->query($sql) !== TRUE) {
            echo "Error inserting ticket record: " . $conn->error;
        }
    }

    foreach ($selectedFoods as $key => $value) {
        $sql = "INSERT INTO order_food (FoodID, PaymentID, Quantity)
                VALUES ({$key},{$paymentID}, {$value})";
        if ($conn->query($sql) !== TRUE) {
            echo "Error inserting ticket record: " . $conn->error;
        }
    }
} else {
    echo "Error inserting payment record: " . $conn->error;
}
header("Location: history.php");
// Đóng kết nối
$conn->close();
