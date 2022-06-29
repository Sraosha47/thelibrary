<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['title'])  
&& isset($_POST['isbn']) 
&& isset($_POST['description'])){

// Data validation
    if ( strlen($_POST['title']) < 1 || strlen($_POST['release']) < 1) {
    $_SESSION['error'] = 'Missing data';
    header("Location: library_management.php");
    return;
    }

    $sql = "INSERT INTO books(Title, ISBN, Description, Release_Date, Available)
        VALUES (:title, :isbn, :description, :release, :available)";
    $add_book = $pdo->prepare($sql);
    $add_book->execute(array(
    ':title' => $_POST['title'],
    ':isbn' => $_POST['isbn'],
    ':description' => $_POST['description'],
    ':release' => $_POST['release'],
    ':available' => $_POST['available']
    ));
    $_SESSION['success'] = $_POST['title'].' added to the library';
    header( 'Location: library_management.php' ) ;
    return;
    }
if ( isset($_POST['first_name'])  
&& isset($_POST['last_name'])){
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: library_management.php");
        return;
        }

        $sql = "INSERT INTO authors(First_Name, Last_Name)
            VALUES (:first_name, :last_name)";
        $add_author = $pdo->prepare($sql);
        $add_author->execute(array(
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name']
        ));
        $_SESSION['success'] = $_POST['first_name'].' '.$_POST['last_name'].' added to Authors';
        header( 'Location: library_management.php' );
        return;
    }    


// Flash pattern
if ( isset($_SESSION['error']) ) {
echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
unset($_SESSION['error']);
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TB | Library Management</title>
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
        echo('<h2>Books</h2>');
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
        echo("</table>");
    ?>
    

    <h3>Add New Book</h3>
    <form method="post">
        <p><label for="title">Title:</label>
        <input type="text" id="title" name="title"></p>
        <p><label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn"></p>
        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>
        <p><label for="release">Release:</label>
        <input type="date" id="release" name="release"></p>
        <p><label for="available">Available:</label>
        <input type="radio" id="yes" name="available" value=1>
        <label for="yes">Yes</label>
        <input type="radio" id="no" name="available" value=0>
        <label for="no">No</label></p>
        <p><input type="submit" value="Add"/>
        <a href="library_management.php">Cancel</a></p>
    </form>
<?php
    echo('<h2>Authors</h2>');
        echo('<table border="1">'."\n");
            echo "<tr><th>";
            echo('Name');
            echo("</th><th>");
            echo('Action');
            echo("</th></tr>\n");
        
            $stmt = $pdo->query("SELECT Author_ID, concat(First_Name, ' ', Last_name) AS Author FROM Authors");

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                echo( "<tr><td>");
                echo(htmlentities($row['Author']));
                echo("</td><td>");
                echo('<a href="delete_author.php?Book_ID='.$row['Author_ID'].'">Delete</a>');
                echo("</td></tr>\n");
            }
        echo("</table>");
    ?>

    <h2>Add New Author</h2>
    <form method="post">
        <p><label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name"></p>
        <p><label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name"></p>
        <p><input type="submit" value="Add"/>
        <a href="library_management.php">Cancel</a></p>
    </form>


</body>
</html>