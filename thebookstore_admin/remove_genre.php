<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['G_B_ID']) ) {
  $sql = "DELETE FROM Genres_Books WHERE Genres_Books_ID like :zip";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':zip' => $_POST['G_B_ID']));
  $_SESSION['success'] = 'Record deleted';
  header( 'Location: library_management.php' ) ;
  return;
}

// Guardian: Make sure that Account_ID is present
if ( ! isset($_GET['G_B_ID']) ) {
  $_SESSION['error'] = "Missing Genres_Books_ID";
  header('Location: library_management.php');
  return;
}

$stmt = $pdo->prepare(
  "SELECT g.genre AS Genre, b.Title AS Book, Genres_Books_ID FROM Genres_Books gb
  JOIN genres g
    ON g.Genre_ID = gb.Genre_FK
  JOIN books b
    ON b.Book_ID = gb.Book_FK
  WHERE Genres_Books_ID = :xyz");
$stmt->execute(array(":xyz" => $_GET['G_B_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
  $_SESSION['error'] = 'Bad value for Genres_Books_ID';
  header( 'Location: library_management.php' ) ;
  return;
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
<section class="tables">
  <nav id="navbar" class="nav">
      <ul class="nav-list">
          <li><a href="account_management.php">Account Management</a></li>
          <li><a href="library_management.php">Library Management</a></li>
          <li><a href="rentals.php">Rentals</a></li>
          <li><a href="index.php">Log Out</a></li>   
      </ul>
    </nav>

  <p>Confirm: Removing <?= htmlentities($row['Book']) . " from " . htmlentities($row['Genre'])?></p>

  <form method="post">
  <input type="hidden" name="G_B_ID" value="<?= $row['Genres_Books_ID'] ?>">
  <input type="submit" value="Delete" name="delete">
  <a class="button" href="library_management.php">Cancel</a>
  </form>
</section>
</body>
</html>

