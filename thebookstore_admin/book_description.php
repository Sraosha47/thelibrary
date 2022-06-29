<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['title']) && isset($_POST['descr']) && isset($_POST['isbn'])
     && isset($_POST['release']) && isset($_POST['id']) ) {

    // Data validation
    if ( strlen($_POST['title']) < 1 || strlen($_POST['descr']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: library_management.php");
        exit;
    }

    $sql = "UPDATE books SET 
            Title = :title,
            Description = :descr,
            ISBN = :isbn, 
            Release_Date = :release,
            Available = :available
            WHERE Book_ID = :id;"; 
    $upd_book = $pdo->prepare($sql);
    $upd_book->execute(array(
        ':title' => $_POST['title'],
        ':descr' => $_POST['descr'],
        ':isbn' => $_POST['isbn'],
        ':release' => $_POST['release'],
        ':available' => $_POST['available'],
        ':id' => $_POST['id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: library_management.php' );
    exit;
} 

// Guardian: Make sure that Book_ID is present
if ( ! isset($_GET['Book_ID']) ) {
  $_SESSION['error'] = "Missing Book_ID";
  header('Location: library_management.php');
  exit;
}

$stmt = $pdo->prepare(
    "SELECT * FROM books
    WHERE Book_ID = :ID;");
$stmt->execute(array(":ID" => $_GET['Book_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for Book_ID';
    header( 'Location: library_management.php' ) ;
    exit;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$title = htmlentities($row['Title']);
$description = htmlentities($row['Description']);
$isbn = htmlentities($row['ISBN']);
$release = htmlentities($row['Release_Date']);
$available = htmlentities($row['Available']);
$book = $row['Book_ID'];

//array containing all Genres associated with the book
$Arr_Genres = array();
$Genres = $pdo->prepare(
    "SELECT g.Genre from books b
    JOIN genres_books gb
        on gb.Book_FK = b.Book_ID
    Join genres g
        on g.Genre_ID = gb.Genre_FK
    where b.Book_ID = :ID
    ");
$Genres->execute(array(":ID" => $_GET['Book_ID']));

while ($row = $Genres->fetch(PDO::FETCH_ASSOC)){
    array_push($Arr_Genres, $row['Genre']);
}

//array containing all Authors associated with the book
$Arr_Authors = array();
$Authors = $pdo->prepare(
    "SELECT ab.authors_books_ID, concat(a.First_Name, ' ', a.Last_Name) as Author from books b
    JOIN authors_books ab
        on ab.Book_FK = b.Book_ID
    Join authors a
        on a.Author_ID = ab.Author_FK
    where b.Book_ID = :ID
    ");
$Authors->execute(array(":ID" => $_GET['Book_ID']));

while ($row = $Authors->fetch(PDO::FETCH_ASSOC)){
    array_push($Arr_Authors, $row['Author']);
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="libstyle.css">
    <title>TB | Book Description</title>
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
    <h1>Book Description</h1>
    <form method="post">
        <input type="hidden" name="id" value="<?= $book ?>">
        <p>Title:
        <input type="text" name="title" value="<?= $title ?>"></p>
        <p>Description:
        <textarea name="descr" value="<?= $description ?>"><?= $description ?></textarea></p>
        <p>ISBN:
        <input type="text" name="isbn" value="<?= $isbn ?>"></p>
        <p>Release:
        <input type="date" name="release" value="<?= $release ?>"></p>
        <p><label for="available">Available:</label>
        <input type="radio" id="yes" name="available" <?php if($available) {echo("checked");} ?> value=1>
        <label for="yes">Yes</label>
        <input type="radio" id="no" name="available" <?php if(!$available) {echo("checked");} ?> value=0>
        <label for="no">No</label></p>
        <p><input type="submit" value="Update"/>
        <a href="library_management.php">Cancel</a></p>
    </form>

    <?php 
        echo('<table border="1">'."\n");
        echo("<tr><th>");
        echo('Authors');
        echo("</th><th>");
        echo("Action");
        echo("</th></tr>");
        foreach($Arr_Authors as $author){
            $stmt = $pdo->query("SELECT ab.Authors_Books_ID as Entry FROM authors_books ab 
            join authors a 
                on a.Author_ID = ab.Author_FK 
            join books b 
                on b.Book_ID = ab.Book_FK 
            where concat(a.First_Name, ' ', a.Last_Name) = '$author' AND b.ISBN = '$isbn' ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo("<tr><td>\n");
            echo($author);
            echo("</td><td>");
            echo('<a href="remove_author.php?A_B_ID='.$result['Entry'].'">Remove</a>');
            echo("</td></tr>");
        }
        echo("</table>");

        echo('<table border="1">'."\n");
        echo("<tr><th>");
        echo('Genres');
        echo("</th><th>");
        echo("Action");
        echo("</th></tr>");
        foreach($Arr_Genres as $genre){
            $stmt = $pdo->query("SELECT gb.Genres_Books_ID as Entry FROM genres_books gb 
            join genres g 
                on g.Genre_ID = gb.Genre_FK 
            join books b 
                on b.Book_ID = gb.Book_FK 
            where g.Genre = '$genre' AND b.ISBN = '$isbn' ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo("<tr><td>\n");
            echo($genre);
            echo("</td><td>");

            
            echo('<a href="remove_genre.php?G_B_ID='.$result['Entry'].'">Remove</a>');
            echo("</td></tr>");
        }
        echo("</table>");
    ?>

    <form method="post">
        <p>Author:
            <select name="author">
                <?php 
                //turns all the genres in the table Genres to options in the select field
                $stmt = $pdo->query("SELECT concat(First_Name, ' ', Last_Name) as Author FROM authors");
                while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value=''>" . htmlentities($row['Author']) . "</option>";
                } 
                ?>
            </select>
        </p>
        <p><input type="submit" value="Add Author"/></p>
    </form>
    <form method="POST">
        <p>Genre:
            <select name="genre">
                <?php 
                //turns all the genres in the table Genres to options in the select field
                $stmt = $pdo->query("SELECT Genre FROM genres");
                while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value=''>" . htmlentities($row['Genre']) . "</option>";
                } 
                ?>
            </select>
        </p>
        <p><input type="submit" value="Add Genre"/></p>
    </form>

</body>
</html>