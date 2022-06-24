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
            echo( "<tr style='text-align:center'><td colspan='5'>");
            echo('<a href="add_user.php">Add New Account</a>');
            echo("</td></tr>\n");
        echo("</table>")

                   

    ?>
</body>
</html>