<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['Account_ID']) ) {
    $sql = "DELETE FROM Accounts WHERE Account_ID = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['Account_ID']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that Account_ID is present
if ( ! isset($_GET['Account_ID']) ) {
  $_SESSION['error'] = "Missing Account_ID";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT name, Account_ID FROM Accounts WHERE Account_ID = :xyz");
$stmt->execute(array(":xyz" => $_GET['Account_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for Account_ID';
    header( 'Location: index.php' ) ;
    return;
}

?>
<p>Confirm: Deleting <?= htmlentities($row['name']) ?></p>

<form method="post">
<input type="hidden" name="Account_ID" value="<?= $row['Account_ID'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="account_management.php">Cancel</a>
</form>
