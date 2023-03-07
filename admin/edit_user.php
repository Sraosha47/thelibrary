<?php
require_once "pdo.php";
session_start();

//checks if user is actually logged in
if($_SESSION['admin'] === false){
    $_SESSION['error'] = 'You shall not pass!';
    header('Location: index.php');
    exit;
}

// Guardian: Make sure that Account_ID is present
if ( ! isset($_GET['Account_ID']) ) {
    $_SESSION['error'] = "Missing Account_ID";
    header('Location: account_management.php');
    exit;
}

if ( isset($_POST['return']) && htmlentities($_POST['rDate']) !== '0000-00-00'){
    $_SESSION['error'] = 'Book already returned';
    header( 'Location: edit_user.php?Account_ID='.$_POST['Account'].'#rentals' ) ;
    exit;   
}

//statement returning the book
elseif ( isset($_POST['return']) && isset($_POST['Account'])) {
 
    $sql = 
    "UPDATE books
    SET available = 1
    WHERE Book_ID = :book;
    UPDATE rentals
    SET Return_Date = CURRENT_TIMESTAMP
    WHERE Rental_ID = :rental;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':rental' => $_POST['Rental'],
        ':book' => $_POST['Book']
    ));
    $_SESSION['success'] = 'Book Returned';
    header( 'Location: edit_user.php?Account_ID='.$_POST['Account'].'#rentals' ) ;
    exit;
  }

//statement for updates to the account
elseif ( isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) 
&& isset($_POST['password']) && isset($_POST['id']) && isset($_POST['changes']) ) {

 // Data validation
 if ( strlen($_POST['fname']) < 1 || strlen($_POST['password']) < 1) {
     $_SESSION['error'] = 'Missing data';
     header("Location: edit_user.php?Account_ID=".$_POST['id']);
     exit;
 }

 if ( strpos($_POST['email'],'@') === false ) {
     $_SESSION['error'] = 'Bad data';
     header("Location: edit_user.php?Account_ID=".$_POST['id']);
     exit;
 }

 if ( $_POST['password'] !== $_POST['check']){
    $_SESSION['error'] = 'Passwords do not match';
    header("Location: edit_user.php?Account_ID=".$_POST['id']);
    exit;
 }

 $sql = "UPDATE accounts SET 
         First_Name = :fname,
         Last_Name = :lname,
         Email = :email, 
         Password = :password,
         Phone = :phone,
         Street = :address,
         Postal_FK = (Select Postal_ID From postal_codes where Code like :code && Town like :town)
         WHERE Account_ID = :Account_ID;";
 $stmt = $pdo->prepare($sql);
 $stmt->execute(array(
     ':fname' => $_POST['fname'],
     ':lname' => $_POST['lname'],
     ':email' => $_POST['email'],
     ':password' => hash('sha256',$_POST['password']),
     ':phone' => $_POST['phone'],
     ':address' => $_POST['address'],
     ':town' => $_POST['town'],
     ':code' => $_POST['code'],
     ':Account_ID' => $_POST['id']));
 $_SESSION['success'] = 'Record updated';
 header("Location: edit.php?Account_ID=".$_POST['id']);
 exit;
}

//getting all the info on the account
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
    exit;
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/libstyle.css">
    <title>TL | Edit Account</title>
</head>
<body>

    <nav id="navbar" class="nav">
        <ul class="nav-list">
            <li><a href="account_management.php">Account Management</a></li>
            <li><a href="library_management.php">Library Management</a></li>
            <li><a href="rentals.php">Rentals</a></li>
            <li><a href="index.php">Log Out</a></li>   
        </ul>    
        <ul class="modnav"> 
                <li><a href="#info">Account Information</a></li>
                <li><a href="#rentals">Rentals</a></li> 
        </ul>
    </nav>
    <section class="tables" id="info">
    <h1>Account Information</h1>
<?php  
    if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }
?>
    <form method="post">
        <input type="hidden" name="id" value="<?= $account ?>">
        <p>First Name:
        <input type="text" name="fname" value="<?= $firstname ?>"></p>
        <p>Last Name:
        <input type="text" name="lname" value="<?= $lastname ?>"></p>
        <p>Email:
        <input type="email" name="email" value="<?= $email ?>"></p>
        <p>Passwort: Enter again:</p>
        <p>
        <input class="password" type="password" name="password" value="">
        <input class="password" type="password" name="check" value="">
        </p>
        <p>Phone:
        <input type="text" name="phone" value="<?= $phone ?>"></p>
        <p>Address:
        <input type="text" name="address" value="<?= $address ?>"></p>
        <p>Postal Code:
        <input type="text" name="code" value="<?= $code ?>"></p>
        <p>Town:
        <input type="text" name="town" value="<?= $town ?>"></p>
        <p class="buttons"><input name="changes" type="submit" value="Submit Changes"/>
        <button type="reset">Cancel</button></p></p>
    </form>
    </section>

    <section class="tables" id="rentals">
<?php
    echo('<h2>Rentals</h2>');

    if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }
    if ( isset($_SESSION['success']) ) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }
        echo('<table border="1">'."\n");
            echo("<tr><th>");
            echo('Due Date');
            echo("</th><th>");
            echo('Return Date');
            echo("</th><th>");
            echo('Book');
            echo("</th><th>");
            echo('Action');
            echo("</th></tr>\n");
            $stmt = $pdo->query(
                "SELECT r.Due_Date AS Date, 
                r.Return_Date AS rDate,
                r.Rental_ID AS Rental,
                concat(a.First_Name, ' ', a.Last_Name) AS Name,
                a.Account_ID as Account,
                b.Title AS Title, 
                b.Book_ID as Book
                FROM rentals r
                JOIN accounts a
                    ON r.Account_FK = a.Account_ID
                JOIN books b
                    ON r.Book_FK = b.Book_ID
                WHERE Account_ID = $account
                ORDER BY Date");
            

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                echo("<tr><td>");
                echo(htmlentities($row['Date']));
                echo("</td><td>");
                echo(htmlentities($row['rDate']));
                echo("</td><td>");
                echo(htmlentities($row['Title']));
                echo("</td><td>");
                echo('<form method="post">
                <input type="hidden" name="Account" value='.$row['Account'].'>
                <input type="hidden" name="Book" value='.$row['Book'].'>
                <input type="hidden" name="Rental" value='.$row['Rental'].'>
                <input type="hidden" name="rDate" value='.$row['rDate'].'>
                <p class="buttons">
                  <input type="submit" value="Return" name="return">
                </p>  
                </form>');
                echo("</td></tr>\n");
            }
        echo("</table>");
?>
</section>

</body>
</html>