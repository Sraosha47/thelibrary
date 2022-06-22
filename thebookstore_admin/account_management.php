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
        echo "<tr><td>";
        echo('First_Name');
        echo("</td><td>");
        echo('Last_Name');
        echo("</td><td>");
        echo('Email');
        echo("</td><td>");
        echo('Admin');
        echo("</td><td>");
        echo('Action');
        echo("</td></tr>\n");
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
    ?>
</body>
</html>