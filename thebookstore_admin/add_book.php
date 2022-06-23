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
    <title>The Bookstore|New Book</title>
</head>
<body>

    <?php
        if ( isset($_POST['title'])  
            && isset($_POST['isbn']) 
            && isset($_POST['description'])
        ) {

            // Data validation
            if ( strlen($_POST['title']) < 1 || strlen($_POST['release']) < 1) {
            $_SESSION['error'] = 'Missing data';
            header("Location: add_book.php");
            return;
            }

            $sql = "INSERT INTO books(Title, ISBN, Description, Release_Date, Available)
                    VALUES (:title, :isbn, :description, :release, :available)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
            ':title' => $_POST['title'],
            ':isbn' => $_POST['isbn'],
            ':description' => $_POST['description'],
            ':release' => $_POST['release'],
            ':available' => $_POST['available']
            ));
            $_SESSION['success'] = 'Account Added';
            header( 'Location: library_management.php' ) ;
            return;
        }

        // Flash pattern
        if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
        }
    ?>

    <h1>New Account</h1>
    <form method="post">
        <p><label for="title">Title:</label>
        <input type="text" id="title" name="title"></p>
        <p><label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn"></p>
        <p><label for="description">Description:</label>
        <input type="text" id="description" name="description"></p>
        <p><label for="release">Release:</label>
        <input type="release" id="release" name="release"></p>
        <p><label for="available">Available:</label>
        <select id="available" name="available">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select></p> 
        <p><input type="submit" value="Add"/>
        <a href="add_user.php">Cancel</a></p>
    </form>
</body>
</html>