<?php
session_start();
include("./common/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome to Algonquin Social Media Website</h1>
        <p>If you have never used this before, you have to <a href="NewUser.php">sign up</a> first.</p>
        <p>If you have already signed up, you can <a href="LogIn.php">log in</a> now.</p>
    </div>
</body>

<?php include('./common/footer.php'); ?>

</html>
