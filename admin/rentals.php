<?php
require_once "pdo.php";
session_start();

//checks if user is actually logged in
if($_SESSION['admin'] === false){
    $_SESSION['error'] = 'You shall not pass!';
    header('Location: index.php');
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
    <title>TL | Rentals</title>
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
<?php
    echo('<h1>Rentals</h1>');

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
            echo('Client');
            echo("</th><th>");
            echo('Book');
            echo("</th><th>");
            echo('Action');
            echo("</th></tr>\n");
            $stmt = $pdo->query(
                "SELECT r.Due_Date AS Date, a.Account_ID AS ID, concat(a.Last_Name, ', ', a.First_Name) AS Name, b.Title AS Book 
                FROM rentals r
                JOIN accounts a
                    ON r.Account_FK = a.Account_ID
                JOIN books b
                    ON r.Book_FK = b.Book_ID
                WHERE r.Return_Date = 0
                ORDER BY Date");

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                echo("<tr><td>");
                echo(htmlentities($row['Date']));
                echo("</td><td>");
                echo(htmlentities($row['Name']));
                echo("</td><td>");
                echo(htmlentities($row['Book']));
                echo("</td><td>");
                echo('<a class="button" href="edit_user.php?Account_ID='.$row['ID'].'#rentals">Edit</a>');
                echo("</td></tr>\n");
            }
        echo("</table>");
?>
</section>
</body>
</html>