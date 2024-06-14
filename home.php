<?php
session_start();
include("connect.php");

// Lấy danh sách phim sắp chiếu
$sql_upcoming = "SELECT * FROM Movie WHERE Status = 'Upcoming' ";
$result_upcoming = $conn->query($sql_upcoming);

// Lấy danh sách phim đã chiếu
$sql_now_showing = "SELECT * FROM Movie WHERE Status = 'NowShowing' ";
$result_now_showing = $conn->query($sql_now_showing);

// Lấy danh sách suất chiếu sớm
$sql_early_showing = "SELECT * FROM Movie WHERE Status = 'EarlyScreening' ";
$result_early_showing = $conn->query($sql_early_showing);
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bootstrap demo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    .navbar-custom {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 10;
      background: rgba(0, 0, 0, 0.1);
    }

    .card {
      border: none;
    }

    .nav-link {
      color: white !important;
      font-size: larger;
      font-weight: 500;
      font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.html">
        <img style="width: 20%;height: 20%; margin-left: 35%;" src="https://www.bhdstar.vn/wp-content/uploads/2023/08/logo.png" alt="">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
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
        } else {
        ?>
          <ul class="navbar-nav ms-auto me-5">
            <li class="nav-item">
              <a class="nav-link" href="/register.php">Register</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/login.php">Login</a>
            </li>
          </ul>
        <?php
        }
        ?>


      </div>
    </div>
  </nav>


  <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active" data-bs-interval="1000">
        <img src="https://www.bhdstar.vn/wp-content/uploads/2024/04/WEB-LED-COMBO-LY-DOI-MAU-KO-GIA.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5>First slide label</h5>
          <p>Some representative placeholder content for the first slide.</p>
        </div>
      </div>
      <div class="carousel-item" data-bs-interval="1000">
        <img src="https://www.bhdstar.vn/wp-content/uploads/2024/05/referenceSchemeHeadOfficeallowPlaceHoldertrueheight1069ldapp-10.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5>Second slide label</h5>
          <p>Some representative placeholder content for the second slide.</p>
        </div>
      </div>
      <div class="carousel-item" data-bs-interval="1000">
        <img src="https://www.bhdstar.vn/wp-content/uploads/2024/03/duoi-13-t-va-duoi-16t.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5>Third slide label</h5>
          <p>Some representative placeholder content for the third slide.</p>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCapt
tions" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>


  <!-- body -->
  <div class="carousel-inner text-center">
    <div class="carousel-item active">
      <h2>Movie is showing</h2>
      <div class="row justify-content-center text-start">
        <?php
        if ($result_now_showing->num_rows > 0) {
          while ($row_now_showing = $result_now_showing->fetch_assoc()) {
        ?>
            <div class="col-md-2 m-2">
              <div class="card">
                <div class="card-body">
                  <img src="<?php echo $row_now_showing['Image']; ?>" class="card-img-top mb-2" alt="...">
                  <h5 class="card-title"><?php echo $row_now_showing['Name']; ?></h5>
                  <p class="card-subtitle">Category: <?php echo $row_now_showing['Category']; ?></p>
                  <p class="card-text">Time: <?php echo $row_now_showing['Time']; ?> minute</p>
                  <a href="/muave.php?movieid=<?php echo $row_now_showing['MovieID']; ?>">
                    <button type="button" class="btn btn-primary" style="width: 100%;">
                      Buy ticket
                    </button>
                  </a>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo "Không có phim đang chiếu.";
        }
        ?>
      </div>
    </div>
  </div>

  <div class="carousel-inner text-center">
    <div class="carousel-item active">
      <h2>Early screenings</h2>
      <div class="row justify-content-center text-start">
        <?php
        if ($result_early_showing->num_rows > 0) {
          while ($row_upcoming = $result_early_showing->fetch_assoc()) {
        ?>
            <div class="col-md-2 m-2">
              <div class="card">
                <div class="card-body">
                  <img src="<?php echo $row_upcoming['Image']; ?>" class="card-img-top mb-2" alt="...">
                  <h5 class="card-title"><?php echo $row_upcoming['Name']; ?></h5>
                  <p class="card-subtitle">Category: <?php echo $row_upcoming['Category']; ?></p>
                  <p class="card-text">Time: <?php echo $row_upcoming['Time']; ?> minute</p>
                  <a href="/muave.php?movieid=<?php echo $row_upcoming['MovieID']; ?>">
                    <button type="button" class="btn btn-primary" style="width: 100%;">
                      Buy ticket
                    </button>
                  </a>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo "Không có suất chiếu sớm.";
        }
        ?>
      </div>
    </div>
  </div>

  <div class="carousel-inner text-center">
    <div class="carousel-item active">
      <h2>Movie coming soon</h2>
      <div class="row justify-content-center text-start">
        <?php
        if ($result_upcoming->num_rows > 0) {
          while ($row_upcoming = $result_upcoming->fetch_assoc()) {
        ?>
            <div class="col-md-2 m-2">
              <div class="card">
                <div class="card-body">
                  <img src="<?php echo $row_upcoming['Image']; ?>" class="card-img-top mb-2" alt="...">
                  <h5 class="card-title"><?php echo $row_upcoming['Name']; ?></h5>
                  <p class="card-subtitle">Category: <?php echo $row_upcoming['Category']; ?></p>
                  <p class="card-text">Time: <?php echo $row_upcoming['Time']; ?> minute</p>
                  <a href="/information.php?movieid=<?php echo $row_upcoming['MovieID']; ?>">
                    <button type="button" class="btn btn-success" style="width: 100%;">
                      Information
                    </button>
                  </a>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo "Không có phim sắp chiếu.";
        }
        ?>
      </div>
    </div>
  </div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>