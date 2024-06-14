<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
$movieId = $_REQUEST['movieid'];
include("connect.php");
$_SESSION['movieId'] = $_REQUEST['movieid'];
// Lấy thông tin phim
$sql_movie_info = "SELECT *  FROM movie WHERE MovieID = ?";
$stmt_movie_info = $conn->prepare($sql_movie_info);
$stmt_movie_info->bind_param("i", $movieId);
$stmt_movie_info->execute();
$result_movie_info = $stmt_movie_info->get_result();

if ($result_movie_info->num_rows > 0) {
    $movie_info = $result_movie_info->fetch_assoc();
} else {
    $movie_info = null;
}

// Lấy danh sách các ngày chiếu
$sql_dates = "SELECT DISTINCT Date FROM showtime WHERE MovieID = ?";
$stmt_dates = $conn->prepare($sql_dates);
$stmt_dates->bind_param("i", $movieId);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();

// Lấy danh sách lịch chiếu cho từng ngày
$sql_schedule = "SELECT ShowtimeID, Date, StartTime FROM showtime WHERE MovieID = ?";
$stmt_schedule = $conn->prepare($sql_schedule);
$stmt_schedule->bind_param("i", $movieId);
$stmt_schedule->execute();
$result_schedule = $stmt_schedule->get_result();

$schedules = [];
while ($row = $result_schedule->fetch_assoc()) {
    $schedules[$row['Date']][] = ['ShowtimeID' => $row['ShowtimeID'], 'StartTime' => $row['StartTime']];
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giao Diện Mua Vé Xem Phim</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .movie-card {
            display: flex;
            flex-wrap: wrap;
            border: 1px solid #ddd;
            padding: 16px;
            border-radius: 8px;
            margin-top: 100px;
        }

        .movie-info {
            flex: 1;
            margin-right: 16px;
        }

        .movie-info img {
            width: 50%;
            border-radius: 8px;
        }

        .booking-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .booking-info select,
        .booking-info button {
            margin-bottom: 16px;
        }

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
        <div class="movie-card">
            <div class="movie-info">
                <img src="<?php echo $movie_info['Image']; ?>" alt="Movie Poster">
                <h5 class="mt-3"><?php echo $movie_info['Name']; ?></h5>
                <p>Time: <?php echo $movie_info['Time']; ?> minute</p>
                <p>Category: <?php echo $movie_info['Category']; ?> </p>
            </div>
            <div class="booking-info">
                <h5>Select Date</h5>
                <select class="form-control" id="select-date">
                    <option value="">Select Date</option>
                    <?php
                    while ($row = $result_dates->fetch_assoc()) {
                        echo "<option value='" . $row['Date'] . "'>" . $row['Date'] . "</option>";
                    }
                    ?>
                </select>
                <h5>Choose time</h5>
                <select class="form-control" id="select-time" disabled>
                    <option value="">Choose time</option>
                </select>
                <a id="select-seat-link" href="#">
                    <button style="width: 100%;" type="button" class="btn btn-primary" id="select-seat" disabled>Select Seat</button>
                </a>
                <h5>Movie content</h5>
                <p><?= $movie_info['Description'] ?></p>
                <h5>Director</h5>
                <p><?= $movie_info['Director'] ?></p>
                <h5>Language</h5>
                <p><?= $movie_info['Language'] ?></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const schedules = <?php echo json_encode($schedules); ?>;

        document.getElementById('select-date').addEventListener('change', function() {
            const selectedDate = this.value;
            const selectTime = document.getElementById('select-time');
            selectTime.innerHTML = '<option value="">Choose time</option>';

            if (selectedDate && schedules[selectedDate]) {
                schedules[selectedDate].forEach(schedule => {
                    const option = document.createElement('option');
                    option.value = schedule.ShowtimeID;
                    option.textContent = schedule.StartTime;
                    selectTime.appendChild(option);
                });
                selectTime.disabled = false;
            } else {
                selectTime.disabled = true;
            }

            document.getElementById('select-seat').disabled = true;
        });

        document.getElementById('select-time').addEventListener('change', function() {
            const selectSeat = document.getElementById('select-seat');
            selectSeat.disabled = !this.value;

            const selectedShowtimeId = this.value;
            const selectSeatLink = document.getElementById('select-seat-link');
            selectSeatLink.href = `chonghe.php?showtimeid=${selectedShowtimeId}`;
        });
    </script>
</body>

</html>