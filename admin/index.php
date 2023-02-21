<?php
require_once "pdo.php";
session_start();
$_SESSION['admin'] = false;

if(isset($_POST['email']) 
&& isset($_POST['password'])){
    $stmt = $pdo->prepare(
        "SELECT * FROM accounts
        WHERE Email = :email AND Password = :password;");
    $stmt->execute(array(
        ":email" => $_POST['email'],
        ":password" => hash('sha256',$_POST['password'])));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Password or Email incorrect';
        header( 'Location: index.php' ) ;
        exit;
    }
    elseif($row['Admin'] === 0 ){
        $_SESSION['error'] = 'You shall not pass!';
        header( 'Location: index.php' ) ;
        exit;
    }
    else{
        $_SESSION['success'] = 'Login successful. Welcome ' . $row['First_Name'].'!';
        $_SESSION['admin'] = true;
        header('Location: library_management.php');
        exit;
    }
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
    <title>TL | Login</title>
</head>
<body>

    <section class="tables">
        <h1>The Library | Admin Portal</h1>
    <?php
        if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }
    ?>
        <form method="post">
            <p>Email:
            <input type="text" name="email"></p>
            <p>Password:
            <input type="password" name="password"></p>
            <p>
                <input type="submit" value="Sign In"/>
                <button type="reset">Cancel</button>
            </p>
        </form>
    </section>
</body>
</html>