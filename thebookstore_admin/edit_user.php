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
    <title>The Bookstore|Edit Account</title>
</head>
<body>
    <h1>Edit Account</h1>
    <form method="post">
        <input type="hidden" name="user_id" value="user_id">
        <p>First Name:
        <input type="text" name="fname"></p>
        <p>Last Name:
        <input type="text" name="lname"></p>
        <p>Email:
        <input type="text" name="email"></p>
        <p>Password:
        <input type="password" name="password"></p>
        <p>Phone:
        <input type="text" name="phone"></p>
        <p>Address:
        <input type="text" name="address"></p>
        <p>Postal Code:
        <input type="text" name="pcode"></p>
        <p>Town:
        <input type="text" name="town"></p>
        <p>Admin:
        <input type="radio" id="yes" name="admin" value=1>
        <label for="yes">Yes</label>
        <input type="radio" id="no" name="admin" value=0>
        <label for="no">No</label></p>
        <p><input type="submit" value="Submit Changes"/>
        <a href="edit_user.php">Cancel</a></p>
    </form>
</body>
</html>