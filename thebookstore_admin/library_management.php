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
    <title>The Bookstore|Library Management</title>
</head>
<body>
    <h1>Library Management</h1>

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
        echo('Title');
        echo("</td><td>");
        echo('ISBN');
        echo("</td><td>");
        echo('Available');
        echo("</td><td>");
        echo('Action');
        echo("</td></tr>\n");
        
        $stmt = $pdo->query("SELECT Title, ISBN, Available, Book_ID FROM Books");

        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            echo( "<tr><td>");
            echo(htmlentities($row['Title']));
            echo("</td><td>");
            echo(htmlentities($row['ISBN']));
            echo("</td><td>");
            if($row['Available']) {
                echo("Yes");
            } else {
                echo("No");
            }
            echo("</td><td>");
            echo('<a href="edit_user.php?Book_ID='.$row['Book_ID'].'">Edit</a> / ');
            echo('<a href="delete_user.php?Book_ID='.$row['Book_ID'].'">Remove</a>');
            echo("</td></tr>\n");
        }
        echo("<a href='add_book.php'>Add New Book</a>");

    ?>

    
</body>
</html>