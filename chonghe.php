    <?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }
    include("connect.php");
    $showTimeId = $_REQUEST['showtimeid'];
    // Lấy danh sách ghế và trạng thái của chúng từ cơ sở dữ liệu
    $sql_seats = "SELECT SeatNumber, Status FROM seat WHERE ShowtimeID = $showTimeId";
    $result_seats = $conn->query($sql_seats);

    $seats = [];
    if ($result_seats->num_rows > 0) {
        while ($row = $result_seats->fetch_assoc()) {
            $seats[$row['SeatNumber']] = $row['Status'];
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chọn Ghế Xem Phim</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f0f0f0;
            }

            .container {
                display: flex;
                border: 1px solid #ccc;
                background: #fff;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow: hidden;
                width: 50%;
            }

            .payment-info {

                height: 100%;
                overflow: auto;
            }

            .seat-selection,
            .payment-info {
                padding: 20px;
            }

            .seat-selection {
                border-right: 1px solid #ccc;
            }

            h2 {
                margin-top: 0;
                font-size: 24px;
            }

            .screen {
                background: #333;
                color: #fff;
                padding: 10px;
                text-align: center;
                margin-bottom: 20px;
                border-radius: 4px;
            }

            .seats {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 1rem;
            }


            .row {
                display: flex;
                justify-content: center;
                gap: 10px;
            }

            .seat {
                background: #444;
                color: #fff;
                width: 40px;
                height: 40px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                border-radius: 4px;
                transition: background 0.3s;
            }

            .seat.selected {
                background: #6c757d;
            }

            .seat.occupied {
                background: #e53935;
                cursor: not-allowed;
            }

            .seat:hover {
                background: #555;
            }

            .payment-info {
                width: 300px;
            }

            .selected-seats ul {
                list-style-type: none;
                padding: 0;
                margin: 10px 0;
            }

            .selected-seats ul li {
                margin: 5px 0;
                padding: 5px 10px;
                background: #f8f9fa;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            .total-price {
                font-size: 18px;
                margin-top: 10px;
            }

            #checkout-button {
                background: #28a745;
                color: #fff;
                padding: 10px 20px;
                border: none;
                cursor: pointer;
                width: 100%;
                font-size: 16px;
                margin-top: 20px;
                border-radius: 4px;
                transition: background 0.3s;
            }

            #checkout-button:hover {
                background: #218838;
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
                            <a class="nav-link" href="#">Thành viên</a>
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
        <div class="container" style="height: 50%;">
            <div class="seat-selection">
                <h2>Select Chair</h2>
                <div class="screen">Screen</div>
                <div class="seats">
                    <?php
                    $rowCount = 0;
                    foreach ($seats as $seatNumber => $status) {
                        $seatClass = ($status == '1') ? 'seat occupied' : 'seat';
                        echo "<div class='$seatClass'>$seatNumber</div>";

                        // Tăng số lượng ghế trên hàng và kiểm tra xem có đủ 8 ghế trên mỗi hàng chưa
                        $rowCount++;
                        if ($rowCount % 8 == 0) {
                            echo '</div><div class="seats">';
                        }
                    }
                    ?>
                </div>
                <ul class="list-unstyled mt-4 ab-b-l    ">
                    <li class="d-flex align-items-center">
                        <div class="rounded-circle mr-2" style="width: 1.5rem; height: 1.5rem; background-color: #e53935;"></div>
                        <span>Seat selected</span>
                    </li>
                    <li class="d-flex align-items-center mt-2">
                        <div class="rounded-circle mr-2" style="width: 1.5rem; height: 1.5rem; background-color: #444;"></div>
                        <span>Seat not yet selected</span>
                    </li>
                    <li class="d-flex align-items-center mt-2">
                        <div class="rounded-circle mr-2" style="width: 1.5rem; height: 1.5rem; background-color: #555;"></div>
                        <span>Chair is being selected</span>
                    </li>
                </ul>



            </div>
            <div class="payment-info">
                <h2>Payment</h2>
                <div class="selected-seats">
                    <h3>Selected seats:</h3>
                    <ul id="selected-seats-list"></ul>
                </div>
                <div class="total-price">
                    <h3>Total: <span id="total-price">0</span> VND</h3>
                </div>
                <form action="ticket.php" method="post" id="payment-form">
                    <input type="hidden" name="ShowTimeId" value="<?php echo $showTimeId; ?>">
                    <input type="hidden" name="SelectedSeats" id="selected-seats-input"> <!-- Thêm input hidden này -->
                    <button type="submit" id="checkout-button">Continue</button>
                </form>


            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const seats = document.querySelectorAll('.seat:not(.occupied)');
                const selectedSeatsList = document.getElementById('selected-seats-list');
                const selectedSeatsInput = document.getElementById('selected-seats-input'); // Lấy input hidden
                const totalPriceElement = document.getElementById('total-price');
                const seatPrice = 50000;
                let selectedSeats = [];

                seats.forEach(seat => {
                    seat.addEventListener('click', () => {
                        if (!seat.classList.contains('selected')) {
                            seat.classList.add('selected');
                            selectedSeats.push(seat.textContent);
                        } else {
                            seat.classList.remove('selected');
                            selectedSeats = selectedSeats.filter(s => s !== seat.textContent);
                        }
                        updateSelectedSeats();
                    });
                });

                function updateSelectedSeats() {
                    selectedSeatsList.innerHTML = '';
                    selectedSeats.forEach(seat => {
                        const li = document.createElement('li');
                        li.textContent = seat;
                        selectedSeatsList.appendChild(li);
                    });
                    totalPriceElement.textContent = (selectedSeats.length * seatPrice).toLocaleString('vi-VN');

                    // Cập nhật giá trị của input hidden
                    selectedSeatsInput.value = JSON.stringify(selectedSeats);
                }
            });
        </script>

    </body>

    </html>