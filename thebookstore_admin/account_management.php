<?php
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Bookstore|Account Management</title>
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

    <h1>Account Management</h1>

    <?php
        if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }
        if ( isset($_SESSION['success']) ) {
            echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
            unset($_SESSION['success']);
        }
        echo('<table border="1">'."\n");
            echo "<tr><th>";
            echo('First_Name');
            echo("</th><th>");
            echo('Last_Name');
            echo("</th><th>");
            echo('Email');
            echo("</th><th>");
            echo('Admin');
            echo("</th><th>");
            echo('Action');
            echo("</th></tr>\n");
            $stmt = $pdo->query("SELECT First_Name, Last_Name, Email, Admin, Account_ID FROM accounts");

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                echo "<tr><td>";
                echo(htmlentities($row['First_Name']));
                echo("</td><td>");
                echo(htmlentities($row['Last_Name']));
                echo("</td><td>");
                echo(htmlentities($row['Email']));
                echo("</td><td>");
                if($row['Admin']) {
                    echo("Yes");
                } else {
                    echo("No");
                }
                echo("</td><td>");
                echo('<a href="edit_user.php?Account_ID='.$row['Account_ID'].'">Edit</a> / ');
                echo('<a href="delete_user.php?Account_ID='.$row['Account_ID'].'">Delete</a>');
                echo("</td></tr>\n");
            }
        echo("</table>");

        if ( isset($_POST['fname'])  
        && isset($_POST['lname']) 
        && isset($_POST['email'])
        && isset($_POST['password'])) {

        // Data validation
        if ( strlen($_POST['fname']) < 1 || strlen($_POST['password']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: add_user.php");
        return;
        }

        if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: add_user.php");
        return;
        }

        $sql = "INSERT INTO accounts (First_Name, Last_Name, Email, Password, Phone, Street, Postal_FK, Admin)
                VALUES (:fname, :lname, :email, :password, :phone, :address, (SELECT Postal_ID FROM postal_codes WHERE CODE LIKE :pcode && Town LIKE :town), :admin)";
        $add_user = $pdo->prepare($sql);
        $add_user->execute(array(
        ':fname' => $_POST['fname'],
        ':lname' => $_POST['lname'],
        ':email' => $_POST['email'],
        ':password' => $_POST['password'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'],
        ':pcode' => $_POST['pcode'],
        ':town' => $_POST['town'],
        ':admin' => $_POST['admin']
        ));
        $_SESSION['success'] = 'Account Added';
        header( 'Location: account_management.php' ) ;
        return;
    }

        // Flash pattern
        if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
        }


    ?>

    <h2>New Account</h2>

    <form method="post">
        <p><label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname"></p>
        <p><label for="lname">Last Name:</label>
        <input type="text" id="lname" name="lname"></p>
        <p><label for="email">Email:</label>
        <input type="email" id="email" name="email"></p>
        <p><label for="password">Password:</label>
        <input type="password" id="password" name="password"></p>
        <p><label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone"></p>
        <p><label for="address">Address:</label>
        <input type="text" id="address" name="address"></p>
        <p><label for="pcode">Postal Code:</label>
        <input type="text" id="pcode" name="pcode"></p>
        <p><label for="town">Town:</label>
        <input type="text" id="town" name="town"></p>
        <p><label for="admin">Admin:</label>
        <input type="radio" id="yes" name="admin" value=1>
        <label for="yes">Yes</label>
        <input type="radio" id="no" name="admin" value=0>
        <label for="no">No</label></p>
        <p><input type="submit" value="Add"/>
        <a href="account_management.php">Cancel</a></p>
    </form>
</body>
</html>