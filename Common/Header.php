<!DOCTYPE html>
<html lang="en" style="position: relative; min-height: 100%;">
<head>
    <title>Welcome to Algonquin Social</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.6/dist/css/bootstrap.min.css">
    
</head>
<body style="padding-top: 50px; margin-bottom: 60px;">
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" style="padding: 10px" href="http://www.algonquincollege.com">
                    <img src="Common/img/AC.png" alt="Algonquin College" style="max-width:100%; max-height:100%;"/>
                </a>    
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="Home.php">Home</a></li>
                    <li><a href="MyFriends.php">My Friends</a></li>
                    <li><a href="MyAlbums.php">My Albums</a></li>
                    <li><a href="MyPictures.php">My Pictures</a></li>
                    <li><a href="UploadPictures.php">Upload Pictures</a></li>
                    <li <?php if (basename($_SERVER['PHP_SELF']) == 'LogIn.php' || basename($_SERVER['PHP_SELF']) == 'Logout.php') echo 'class="active"'; ?>>
                    <?php if(isset($_SESSION["login"]) && $_SESSION["login"] == "OK"): ?>
                        <a href="Logout.php">Log Out</a>
                    <?php else: ?>
                        <a href="LogIn.php">Log In</a>
                    <?php endif; ?></li>
                </ul>
            </div>
        </div>  
    </nav>
    <!-- Остальное содержимое страницы -->
</body>
</html>
