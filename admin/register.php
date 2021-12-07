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

        <title>BLOG • inscription</title>

    </head>

    <body>
        <h1>Inscription</h1>
        <!--get sa passe en url et post en chaché-->
        <form action="treatment.php?register" method="post">
            <label for="last_name">Nom</label>
            <input type="text" name="last_name" maxlength="100" required>
            <br>
            <label for="first_name">Prenom</label> 
            <input type="text" name="first_name" maxlength="100" required>
            <br>
            <label for="email">Email</label> 
            <input type="email" name="email" maxlength="255" required>
            <br>
            <label for="password">Mot de Passe</label>
            <input type='password' name="password" maxlength="255" required>
            <br>
            <label for="password_confirm">Mot de passe</label> 
            <input type="password" name="password_confirm" maxlength="255" required>
            <br>
            <input type="submit" value="Inscription">
        </form>
    </body>

</html>