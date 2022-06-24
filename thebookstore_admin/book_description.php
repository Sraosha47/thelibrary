<?php
require_once "pdo.php";
session_start();
 /*if ( isset($_POST['title']) && isset($_POST['descr']) && isset($_POST['isbn'])
     && isset($_POST['release']) && isset($_POST['id']) ) {

    // Data validation
    if ( strlen($_POST['title']) < 1 || strlen($_POST['release']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: book_description.php?Book_ID=".$_POST['Book_ID']);
        return;
    }

    $sql = "UPDATE books b SET 
            JOIN authors_books ab
                ON b.Book_ID = ab.Book_FK
            JOIN authors a
               ON a.Author_ID = ab.Author_FK
           JOIN genres_books gb
              ON b.Book_ID = gb.Book_FK
            JOIN genres g
                ON gb.Genre_FK = g.Genre_ID
            b.Title = :title,
            b.Description = :descr,
            b.ISBN = :isbn, 
            b.Release_Date = :release,
            b.Available = :available,
            g.Genre = :genre, 
            WHERE Book_ID = :Book_ID"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':title' => $_POST['title'],
        ':descr' => $_POST['descr'],
        ':isbn' => $_POST['isbn'],
        ':release' => $_POST['release'],
        ':available' => $_POST['available'],
        ':genre' => $_POST['genre'],
        ':author' => $_POST['author'],
        ':Book_ID' => $_POST['id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: account_management.php' );
    return;
} */

// Guardian: Make sure that Book_ID is present
if ( ! isset($_GET['Book_ID']) ) {
  $_SESSION['error'] = "Missing Book_ID";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare(
    "SELECT 
    b.*, g.Genre, CONCAT(a.First_Name, ' ', a.Last_Name) AS Author 
    FROM books b
    JOIN authors_books ab
        ON b.Book_ID = ab.Book_FK
    JOIN authors a
        ON a.Author_ID = ab.Author_FK
    JOIN genres_books gb
        ON b.Book_ID = gb.Book_FK
    JOIN genres g
        ON gb.Genre_FK = g.Genre_ID
    WHERE b.Book_ID = :ID");
$stmt->execute(array(":ID" => $_GET['Book_ID']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for Book_ID';
    header( 'Location: account_management.php' ) ;
    return;
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
$genre = htmlentities($row['Genre']);
$author = htmlentities($row['Author']);
$book = $row['Book_ID'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Bookstore|Book Description</title>
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
        <input type="hidden" name="id" value=<?= $book ?>>
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
        <p>Genre:
        <input type="text" name="genre" value="<?= $genre ?>"></p>
        <p>Author:
        <input type="text" name="author" value="<?= $author ?>"></p>
        <p><input type="submit" value="Submit Changes"/>
        <a href="account_management.php">Cancel</a></p>
    </form>

</body>
</html>