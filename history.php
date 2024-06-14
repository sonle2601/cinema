<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
include("connect.php");
$user = $_SESSION['user'];
$userID = $user['CustomerID'];
$sql = "SELECT * FROM payment WHERE CustomerID = $userID";
$payment = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua vé</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
            background: rgba(0, 0, 0, 0.9);
        }

        .navbar-brand img {
            width: 50px;
            height: auto;
        }

        .container {
            margin-top: 80px;
            /* Thêm khoảng cách từ top để không bị overlapped bởi navbar */
        }

        .modal-body {
            max-height: calc(100vh - 200px);
            /* Đảm bảo nội dung modal không bị overflow khi dài */
            overflow-y: auto;
            /* Cho phép scroll nếu nội dung dài hơn */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="https://www.bhdstar.vn/wp-content/uploads/2023/08/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Showtimes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">Ticket purchase history</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Member</a>
                    </li>
                </ul>
                <?php
                if (isset($_SESSION['user'])) {
                    $user = $_SESSION['user'];
                ?>
                    <ul class="navbar-nav ms-auto me-5">
                        <li class="nav-item">
                            <span class="nav-link"><?php echo $user['Name']; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout.php">Logout</a>
                        </li>
                    </ul>
                <?php
                }
                ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Ticket purchase history</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Total price</th>
                        <th scope="col">See</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    while ($row = $payment->fetch_assoc()) {
                        echo '<tr>
                            <td>' . $row['PaymentDate'] . '</td>
                            <td>' . $row['PaymentTime'] . '</td>
                            <td>' . number_format($row['TotalAmount']) . ' VND</td>
                            <td><button class="btn btn-primary" data-toggle="modal" data-target="#detailModal' . $counter . '">Detail</button></td>
                        </tr>';
                        $counter++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $payment->data_seek(0); // Reset result set pointer
    $counter = 1;
    while ($row = $payment->fetch_assoc()) {
        $paymentID = $row['PaymentID'];
        $ticketSql = "SELECT * FROM ticket 
        JOIN movie ON ticket.MovieID = movie.MovieID  
        JOIN seat ON ticket.SeatID = seat.SeatID
        JOIN showtime ON showtime.ShowTimeID  = seat.ShowTimeID
         WHERE PaymentId = $paymentID";
        $tickets = $conn->query($ticketSql);

        $foodSql = "SELECT * FROM order_food JOIN food ON order_food.FoodID = food.FoodID  WHERE PaymentID = $paymentID";
        $foods = $conn->query($foodSql);

        echo '<div class="modal fade" id="detailModal' . $counter . '" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel' . $counter . '" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel' . $counter . '">Ticket purchase details - Transaction ' . $counter . '</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6>Purchased tickets:</h6>
                        <ul>';
        while ($ticket = $tickets->fetch_assoc()) {
            echo '<li>Movie name: ' . htmlspecialchars($ticket['Name']) . ', Row of seats: ' . htmlspecialchars($ticket['SeatNumber']) . ', Fare: 50.000 VND, Showtime: ' . substr($ticket['StartTime'], 0, 5) . ', Show date: ' . htmlspecialchars($ticket['Date']) . '</li>';
        }
        echo '</ul>
                        <h6>Food purchased:</h6>
                        <ul>';
        while ($food = $foods->fetch_assoc()) {
            echo '<li>' . htmlspecialchars($food['Name']) . ': ' . number_format($food['Price']) . ' VND x ' . $food['Quantity'] . '</li>';
        }
        echo '</ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>';
        $counter++;
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>