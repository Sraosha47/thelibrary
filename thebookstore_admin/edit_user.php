<?php
require_once "pdo.php";
session_start();


if ( isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email'])
     && isset($_POST['password']) && isset($_POST['id']) ) {

    // Data validation
    if ( strlen($_POST['fname']) < 1 || strlen($_POST['password']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?Account_ID=".$_POST['Account_ID']);
        return;
    }

    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: edit.php?Account_ID=".$_POST['Account_ID']);
        return;
    }

    $sql = "UPDATE accounts SET 
            First_Name = :fname,
            Last_Name = :lname,
            Email = :email, 
            Password = :password,
            Phone = :phone,
            Street = :address,
            Postal_FK = (Select Postal_ID From postal_codes where Code like :code && Town like :town)
            WHERE Account_ID = :Account_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':fname' => $_POST['fname'],
        ':lname' => $_POST['lname'],
        ':email' => $_POST['email'],
        ':password' => $_POST['password'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'],
        ':town' => $_POST['town'],
        ':code' => $_POST['code'],
        ':Account_ID' => $_POST['id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: account_management.php' );
    return;
}

// Guardian: Make sure that Account_ID is present
if ( ! isset($_GET['Account_ID']) ) {
  $_SESSION['error'] = "Missing Account_ID";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare(
    "SELECT *  
    FROM accounts 
    JOIN postal_codes
        ON postal_codes.Postal_ID = accounts.Postal_FK
    where Account_ID = :ID");
$stmt->execute(array(":ID" => $_GET['Account_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for Account_ID';
    header( 'Location: account_management.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$firstname = htmlentities($row['First_Name']);
$lastname = htmlentities($row['Last_Name']);
$email = htmlentities($row['Email']);
$password = htmlentities($row['Password']);
$phone = htmlentities($row['Phone']);
$address = htmlentities($row['Street']);
$code = htmlentities($row['Code']);
$town = htmlentities($row['Town']);
$account = $row['Account_ID'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Bookstore|Edit Account</title>
</head>
<body>

    <nav id="navbar" class="nav">
        <ul class="nav-list">
            <li><a href="account_management.php">Account Management</a></li>
            <li><a href="library_management.php">Library</a></li>
            <li><a href="rentals.php">Rentals</a></li>
            <li><a href="index.php">Log Out</a></li>   
        </ul>
    </nav>

    <h1>Edit Account</h1>
    <form method="post">
        <input type="hidden" name="id" value="<?= $account ?>">
        <p>First Name:
        <input type="text" name="fname" value="<?= $firstname ?>"></p>
        <p>Last Name:
        <input type="text" name="lname" value="<?= $lastname ?>"></p>
        <p>Email:
        <input type="email" name="email" value="<?= $email ?>"></p>
        <p>Password:
        <input type="text" name="password" value="<?= $password ?>"></p>
        <p>Phone:
        <input type="text" name="phone" value="<?= $phone ?>"></p>
        <p>Address:
        <input type="text" name="address" value="<?= $address ?>"></p>
        <p>Postal Code:
        <input type="text" name="code" value="<?= $code ?>"></p>
        <p>Town:
        <input type="text" name="town" value="<?= $town ?>"></p>
        <p><input type="submit" value="Submit Changes"/>
        <a href="account_management.php">Cancel</a></p>
    </form>
</body>
</html>