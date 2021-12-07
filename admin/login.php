<?php
    session_start();
    //notification
    if (isset($_SESSION['notification'])) {
        echo $_SESSION['notification'];
        unset($_SESSION['notification']);
    }
?>

<!DOCTYPE html>

<html lang="fr">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>BLOG • connexion</title>

    </head>

    <body>
        <form action="treatment.php?login" method="post">
            <label for="email">Email</label>
            <input type="email" name="email" maxlength="255">
            <br>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" maxlength="255">
            <br>
            <input type="checkbox" name="remember">
            <label for="remember">se souvenir de moi</label>
            <br>
            <input type="submit" value="Connexion">
        </form>

        Pas encore de compte? <a href="register.php">Inscrivez vous</a> !
    </body>

</html>