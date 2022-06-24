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

    <nav id="navbar" class="nav">
        <ul class="nav-list">
            <li><a href="account_management.php">Account Management</a></li>
            <li><a href="library_management.php">Library</a></li>
            <li><a href="rentals.php">Rentals</a></li>
            <li><a href="index.php">Log Out</a></li>   
        </ul>
    </nav>
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
            echo "<tr><th>";
            echo('Title');
            echo("</th><th>");
            echo('ISBN');
            echo("</th><th>");
            echo('Available');
            echo("</th><th>");
            echo('Action');
            echo("</th></tr>\n");
        
            $stmt = $pdo->query("SELECT Title, ISBN, Available, Book_ID FROM Books");

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                echo( "<tr><td>");
                echo('<a href="book_description.php?Book_ID='.$row['Book_ID'].'">' . htmlentities($row['Title']) . '</a>');
                echo("</td><td>");
                echo(htmlentities($row['ISBN']));
                echo("</td><td>");
                if($row['Available']) {
                    echo("Yes");
                } else {
                    echo("No");
                }
                echo("</td><td>");
                echo('<a href="book_description.php?Book_ID='.$row['Book_ID'].'">Edit</a>');
                echo("</td></tr>\n");
            }
            echo( "<tr style='text-align:center'><td colspan='4'>");
            echo("<a href='add_book.php'>Add New Book</a>");
            echo("</td></tr>\n");
        echo("</table>")
    ?>

    
</body>
</html>