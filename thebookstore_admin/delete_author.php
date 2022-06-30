<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['Author_ID']) ) {
  $sql = 
  "DELETE FROM authors_books WHERE Author_FK = :zip;
  DELETE FROM authors WHERE Author_ID = :zip;";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':zip' => $_POST['Author_ID']));
  $_SESSION['success'] = $_POST['First_Name']."".$_POST['Last_Name'].' deleted from Authors';
  header( 'Location: library_management.php' ) ;
  exit;
}

// Guardian: Make sure that Author_ID is present
if ( ! isset($_GET['Author_ID']) ) {
  $_SESSION['error'] = "Missing Author_ID";
  header('Location: library_management.php');
  exit;
}

$stmt = $pdo->prepare("SELECT First_Name, Last_Name, Author_ID FROM Authors WHERE Author_ID = :xyz");
$stmt->execute(array(":xyz" => $_GET['Author_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
  $_SESSION['error'] = 'Bad value for Author_ID';
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
          <li><a href="library_management.php">Author Management</a></li>
          <li><a href="library_management.php">Library Management</a></li>
          <li><a href="rentals.php">Rentals</a></li>
          <li><a href="index.php">Log Out</a></li>   
      </ul>
    </nav>

  <p>Confirm: Deleting <?= htmlentities($row['First_Name']) . " " . htmlentities($row['Last_Name'])?></p>

  <form method="post">
  <input type="hidden" name="Author_ID" value="<?= $row['Author_ID'] ?>">
  <input type="submit" value="Delete" name="delete">
  <a href="library_management.php">Cancel</a>
  </form>

</body>
</html>

