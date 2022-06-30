<?php
require_once "pdo.php";
session_start();

//checks if user is actually logged in
if($_SESSION['admin'] === false){
  $_SESSION['error'] = 'You shall not pass!';
  header('Location: index.php');
  exit;
}

if ( isset($_POST['delete']) && isset($_POST['Genre_ID']) ) {
  $sql = 
  "DELETE FROM genres_books WHERE Genre_FK = :zip;
  DELETE FROM genres WHERE Genre_ID = :zip;";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':zip' => $_POST['Genre_ID']));
  $_SESSION['success'] = $_POST['Genre'].' deleted from Genres';
  header( 'Location: library_management.php' ) ;
  exit;
}

// Guardian: Make sure that Genre_ID is present
if ( ! isset($_GET['Genre_ID']) ) {
  $_SESSION['error'] = "Missing Genre_ID";
  header('Location: library_management.php');
  exit;
}

$stmt = $pdo->prepare("SELECT Genre, Genre_ID FROM Genres WHERE Genre_ID = :xyz");
$stmt->execute(array(":xyz" => $_GET['Genre_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
  $_SESSION['error'] = 'Bad value for Genre_ID';
  header( 'Location: library_management.php' ) ;
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/libstyle.css">
  <title>TB | Delete User</title>
</head>
<body>

  <nav id="navbar" class="nav">
      <ul class="nav-list">
          <li><a href="library_management.php">Genre Management</a></li>
          <li><a href="library_management.php">Library Management</a></li>
          <li><a href="rentals.php">Rentals</a></li>
          <li><a href="index.php">Log Out</a></li>   
      </ul>
    </nav>

<section class="tables">
  <p>Confirm: Deleting <?= htmlentities($row['Genre'])?></p>

  <form method="post">
  <input type="hidden" name="Genre_ID" value="<?= $row['Genre_ID'] ?>">
  <p class="buttons">
    <input type="submit" value="Delete" name="delete">
    <a class="button" href="library_management.php">Cancel</a>
  </p>  
</form>
</section>  
</body>
</html>

