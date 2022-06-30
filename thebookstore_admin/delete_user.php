<?php
require_once "pdo.php";
session_start();

//checks if user is actually logged in
if($_SESSION['admin'] === false){
  $_SESSION['error'] = 'You shall not pass!';
  header('Location: index.php');
  exit;
}

//statement deleting the user
if ( isset($_POST['delete']) && isset($_POST['Account_ID']) ) {
  $sql = "DELETE FROM Accounts WHERE Account_ID like :zip";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':zip' => $_POST['Account_ID']));
  $_SESSION['success'] = 'Record deleted';
  header( 'Location: account_management.php' ) ;
  exit;
}

// Guardian: Make sure that Account_ID is present
if ( ! isset($_GET['Account_ID']) ) {
  $_SESSION['error'] = "Missing Account_ID";
  header('Location: account_management.php');
  exit;
}

//query getting data for the paragraph and the form
$stmt = $pdo->prepare("SELECT First_Name, Last_Name, Account_ID FROM Accounts WHERE Account_ID = :xyz");
$stmt->execute(array(":xyz" => $_GET['Account_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
  $_SESSION['error'] = 'Bad value for Account_ID';
  header( 'Location: index.php' ) ;
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
          <li><a href="account_management.php">Account Management</a></li>
          <li><a href="library_management.php">Library Management</a></li>
          <li><a href="rentals.php">Rentals</a></li>
          <li><a href="index.php">Log Out</a></li>   
      </ul>
    </nav>
<section class="tables">
  <p>Confirm: Deleting <?= htmlentities($row['First_Name']) . " " . htmlentities($row['Last_Name'])?></p>

  <form method="post">
  <input type="hidden" name="Account_ID" value="<?= $row['Account_ID'] ?>">
  <p class="buttons">
    <input type="submit" value="Delete" name="delete">
    <a class="button" href="account_management.php">Cancel</a>
  </p>  
  </form>
</section>
</body>
</html>

