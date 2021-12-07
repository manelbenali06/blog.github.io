<?php
    session_start();
    //utilisateur connecté
    if((!isset($_SESSION['user']) || empty($_SESSION['user'])) && (!isset($_COOKIE['user']) || empty($_COOKIE['user']))){
        header('Location: login.php');
    }

    //notification
    if (isset($_SESSION['notification'])) {
        echo $_SESSION['notification'];
        unset($_SESSION['notification']);
    }
    //base de donnée
    require_once('../formation_sql_tp/config/database.php');

    if (isset($_GET['update']) && !empty($_GET['update'])) { // vérifie la présence de update=nombre dans l'url et qu'il nest pas vide
        $id = (int)$_GET['update']; // récupère l'id dans l'url (transtypage en entier)
        $req = $db->query('SELECT * FROM post WHERE id=' . $id);//post nom de la table
        $post = $req->fetch();//$post variable pas nom de la table
        //print_r($post); verification pour etre sur que tout fonctionne bien
        //cette etape indisponbile pour que isset($post)  existe
    }
?>

<!DOCTYPE html>

<html lang="fr">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>BLOG • admin - form</title>

    </head>

    <body>

        <h1><?= isset($post) ? 'Modifier' : 'Ajouter' ?> un article</h1>

    <!--pour faire passer les infoen php il ya les supers globals ex 
    $server:http referer pour savoir de quelle page on vient
    {$_GET:se sont des tableaux['elemnts'] il passe toutes les info dans l'url
    $_POST:se sont des tableaux['elements'] il passe pas par l'url mais il stock c'est cacher,sert a envoyer donnée du formulaire sans passer par l'url pour qu'un utilisateur ne puissent pas les reccuperer(utilisé pour passer des parametres apres ?)}
    $session
    $coukies
    $files: pour faire passser les fichiers-->

        <form action="treatment.php<?= isset($post) ? "?update={$post['id']}" : null ?>" method="post" enctype="multipart/form-data">

            <label for="title">Titre</label>
                <!-- verfifier si $post est defini je met le titre sinon je vais verfier si j'ai $session et dons ce cas la je l'affiche ou non
                if(isset($post)){
                    echo $poste ['title']
                }else {
                    if (isset( $_SESSION['form']['title'] )){
                        echo  $_SESSION['form']['title']
                    }else{
                        echo '';
                    }
                }-->
            <input type="text" name="title" maxlength="100" value="<?= isset($post) ? $post['title'] : (isset($_SESSION['form']['title']) ? $_SESSION['form']['title'] : null) ?>" required>

            <label for="content">Contenu</label>
            <textarea name="content" cols="30" rows="10" maxlength="65535" required><?= isset($post) ? $post['content'] : (isset($_SESSION['form']['content']) ? $_SESSION['form']['content'] : null) ?></textarea>

            <label for="img">Image principale</label>
            <input type="file" name="img" accept="image/png, image/jpg, image/jpeg, image/jp2, image/webp" <?= isset($post) ? null : 'required' ?>>

            <label for="alt">Texte alternatif</label>
            <input type="text" name="alt" maxlength="100" value="<?= isset($post) ? $post['alt'] : (isset($_SESSION['form']['alt']) ? $_SESSION['form']['alt'] : null) ?>" required>

            <label for="author">Auteur</label>
            <input type="text" name="author" maxlength="45" value="<?= isset($post) ? $post['author'] : (isset($_SESSION['form']['author']) ? $_SESSION['form']['author'] : null) ?>" required>

            <label for="published">Publier ?</label>
            <select name="published" required>
                <option value="">-- choisir --</option>
                <option value="true" <?= isset($post) && $post['published'] == true ? 'selected' : null ?>>oui</option>
                <!--si $post existe et $post['published']=1 (true) j'affiche selected sinon rien-->
                <option value="false" <?= isset($post) && $post['published'] == false ? 'selected' : null ?>>non</option>
                <!--si $post existe et $post['published']=0 (false) j'affiche selected sinon rien-->
            </select>

            <input type="submit" value="<?= isset($post) ? 'Modifier' : 'Créer' ?>">

        </form>

    </body>

</html>