<?php

session_start();

require_once('../config/database.php');

if ($_SERVER['HTTP_REFERER'] == 'http://localhost/php/blog/admin/form.php' || (isset($_GET['update']) && !empty($_GET['update']))) { // vérifie qu'on vient bien du formulaire
//die('ok') pour voir les erreures
    // nettoyage des données
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $alt = htmlspecialchars($_POST['alt']);
    $author = htmlspecialchars($_POST['author']);
    $published = htmlspecialchars($_POST['published']);

    $errorMessage = '<p>Merci de vérifier les points suivants :</p>';
    $validation = true;

    if ($_GET['update']) {
        $action = 'update';
        $id = (int)$_GET['update'];
    } else {
        $action = 'create';
    }

    // vérification du titre
    if (empty($title) || strlen($title) > 100) {
        $errorMessage .= '<p>- le champ "titre" est obligatoire et doit comporter moins de 100 caractères.</p>';
        $validation = false;
    }

    // vérification du contenu
    if (empty($content) || strlen($content) > 65535) {
        $errorMessage .= '<p>- le champ "contenu" est obligatoire et doit comporter moins de 65535 caractères.</p>';
        $validation = false;
    }

    // vérification du champ alt
    if (empty($alt) || strlen($alt) > 100) {
        $errorMessage .= '<p>- le champ "alt" est obligatoire et doit comporter moins de 100 caractères.</p>';
        $validation = false;
    }

    // vérification du champ author
    if (empty($author) || strlen($author) > 45) {
        $errorMessage .= '<p>- le champ "auteur" est obligatoire et doit comporter moins de 45 caractères.</p>';
        $validation = false;
    }

    // vérification du champ published
    if (empty($published) || ($published != 'true' && $published != 'false')) {
        $errorMessage .= '<p>- le champ "publier" est obligatoire et doit être soit "oui", soit "non".</p>';
        $validation = false;
    }

    // vérification de l'image
    $authorizedFormats = [
        'image/png',
        'image/jpg',
        'image/jpeg',
        'image/jp2',
        'image/webp'
    ];

    // vérification de l'image
    if ($action === 'create' || ($action === 'update' && $_FILES['img']['name'])) { // si création ou si présence d'une image lors de la modification
        if (empty($_FILES['img']['name']) || $_FILES['img']['size'] > 2000000 || !in_array($_FILES['img']['type'], $authorizedFormats)) {
            $errorMessage .= '<p>- l\'image est obligatoire, ne doit pas dépasser 2 Mo et doit être au format PNG, JPG, JPEG, JP2 ou WEBP.</p>';
            $validation = false;
        } else {
            $timestamp = time(); // récupère le nombre de secondes écoulées depuis le 1er janvier 1970
            $format = strchr($_FILES['img']['name'], '.'); // récupère tout ce qui se trouve après le point (png, jpg, ...)
            $imgName = $timestamp . $format; // crée le nouveau nom d'image
            move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/posts/' . $imgName); // upload du fichier
        }
    }

    if ($validation === true) {
        if ($action === 'create') {//action de creation
            $req = $db->prepare('INSERT INTO post (title, content, img, alt, author, created_at, published) VALUES (:title, :content, :img, :alt, :author, NOW(), :published)'); // prépare la requête
        } else {//action de modification
            $reqImg = $db->query('SELECT img FROM post WHERE id=' . $id);
            $oldImg = $reqImg->fetch();
            if (isset($imgName)) {//j'ai bien une nouvelle image a inserer dans la base de donnée
                if (file_exists('../assets/img/posts/' . $oldImg['img'])) {
                    unlink('../assets/img/posts/' . $oldImg['img']);
                }//on cherche d'abord l'ancienne image pour la supprimer pour ne pas avoir pleins d'images inutile dans notre dossier
            } else {
                $imgName = $oldImg['img'];//si jai pas d'image il faut remettre lancienne
            }
            //on fait la requette pour inserer la nouvelle img
            $req = $db->prepare('UPDATE post SET title=:title, content=:content, img=:img, alt=:alt, author=:author, updated_at=NOW(), published=:published WHERE id=' . $id);
        }
        $req->bindParam(':title', $title, PDO::PARAM_STR); // associe la valeur $title à :title
        $req->bindParam(':content', $content, PDO::PARAM_STR); // associe la valeur $content à :content
        $req->bindParam(':img', $imgName, PDO::PARAM_STR); // associe la valeur $imgName à :img
        $req->bindParam(':alt', $alt, PDO::PARAM_STR); // associe la valeur $alt à :alt
        $req->bindParam(':author', $author, PDO::PARAM_STR); // associe la valeur $author à :author
        $req->bindParam(':published', $published, PDO::PARAM_BOOL); // associe la valeur $published à :published
        $req->execute(); // exécute la requête
        if ($action === 'create') {
            $_SESSION['notification'] = 'L\'article a bien été ajouté';
        } else {
            $_SESSION['notification'] = 'L\'article a bien été modifié';
        }
        //die('c\est bon');
        header('Location: index.php'); // redirection
    } else {
        $_SESSION['notification'] = $errorMessage;
        $_SESSION['form'] = [
            'title' => $title,
            'content' => $content,
            'alt' => $alt,
            'author' => $author
        ];
        //die('c\'est pas bon');
        header('Location: form.php'); // redirection
    }

    /*
    if (!empty($title) && strlen($title) <= 100) { // vérifie le titre
        if (!empty($content) && strlen($content) <= 65535) { // vérifie le contenu
            if (!empty($alt) && strlen($alt) <= 100) { // vérifie le champ alt
                if (!empty($author) && strlen($author) <= 45) { // vérifie le champ alt
                    if (!empty($published) && ($published === 'true' || $published === 'false')) { // vérifie le champ published
                        if (!empty($_FILES['img']['name']) && $_FILES['img']['size'] <= 2000000) { // vérifie la présence et la taille de l'image
                            if ($_FILES['img']['type'] == 'image/png' || $_FILES['img']['type'] == 'image/jpg' || $_FILES['img']['type'] == 'image/jpeg' || $_FILES['img']['type'] == 'image/jp2' || $_FILES['img']['type'] == 'image/webp') { // vérifie le type de fichier

                                $timestamp = time(); // récupère le nombre de secondes écoulées depuis le 1er janvier 1970
                                $format = strchr($_FILES['img']['name'], '.'); // récupère tout ce qui se trouve après le point (png, jpg, ...)
                                $imgName = $timestamp . $format; // crée le nouveau nom d'image

                                $req = $db->prepare('INSERT INTO post (title, content, img, alt, author, created_at, published) VALUES (:title, :content, :img, :alt, :author, NOW(), :published)'); // prépare la requête
                                $req->bindParam(':title', $title, PDO::PARAM_STR); // associe la valeur $title à :title
                                $req->bindParam(':content', $content, PDO::PARAM_STR); // associe la valeur $content à :content
                                $req->bindParam(':img', $imgName, PDO::PARAM_STR); // associe la valeur $imgName à :img
                                $req->bindParam(':alt', $alt, PDO::PARAM_STR); // associe la valeur $alt à :alt
                                $req->bindParam(':author', $author, PDO::PARAM_STR); // associe la valeur $author à :author
                                $req->bindParam(':published', $published, PDO::PARAM_BOOL); // associe la valeur $published à :published
                                $req->execute(); // exécute la requête

                                move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/posts/' . $imgName); // upload du fichier

                            } else {
                                echo 'l\'image doit être au format png, jpg, jpeg, jp2 ou webp';
                            }
                        } else {
                            echo 'le champ "image" est obligatoire et l\'image doit peser moins de 2 Mo';
                        }
                    } else {
                        echo 'le champ "publier" est obligatoire et doit être soit "oui", soit "non";
                    }
                } else {
                    echo 'le champ "auteur" est obligatoire et doit comporter moins de 45 caractères';
                }
            } else {
                echo 'le champ "alt" est obligatoire et doit comporter moins de 100 caractères';
            }
        } else {
            echo 'le champ "contenu" est obligatoire et doit comporter moins de 65535 caractères';
        }
    } else {
        echo 'le champ "titre" est obligatoire et doit comporter moins de 100 caractères';
    }*/
}
/**********************************DELETE****************************************************************** */
 elseif (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $req = $db->query('SELECT img FROM post WHERE id=' . $id); // récupère le nom de l'image
    $oldImg = $req->fetch();
    if (file_exists('../assets/img/posts/' . $oldImg['img'])) { // vérifie que le fichier existe
        unlink('../assets/img/posts/' . $oldImg['img']); // supprime l'image du dossier local
    }
    $reqDelete = $db->query('DELETE FROM post WHERE id=' . $id); // supprime les données en bdd
    $_SESSION['notification'] = 'L\'article a bien été supprimé';
    header('Location: index.php'); // redirection
}
/************************************user********************************************************* */
                     /************user register******************************** */

elseif (isset($_GET['register'])){
$email = htmlentities($_POST['email']);
$password = htmlentities($_POST['password']);
$passwordConfirm = htmlentities($_POST['password_confirm']);
$lastName = htmlentities($_POST['last_name']);
$firstName = htmlentities($_POST['first_name']);
$roles = '["ROLE_USER"]';
$errorMessage = '<ul> une erreur est survenu:';
$valid = true;

if (empty($email) || strlen($email) > 255) {
    $errorMessage .= '<li> le champs email est vide ou trop long(maximum 100 carracteres).</li>';
    $valid = false;
}else {
    $reqMail = $db->prepare('SELECT email FROM user WHERE email=:email');
    $reqMail->bindParam(':email', $email, PDO::PARAM_STR);
    $reqMail->execute();
    $count = $reqMail->rowCount();//compte les resultat de la requette
    if($count > 0) {
        $errorMessage .= '<li> cette adresse mail est deja utilisé.</ul>';
        $valid = false;
    }

}


if ($password !== $passwordConfirm) {
        $errorMessage .= '<li>les mots de passe ne correspondent pas</li> ';
        $valid = false;
} else {
    if (empty($password) || strlen($password) > 255) {
        $errorMessage .= '<li>le champs email est vide ou trop long (maximum 255 caractere)</li> ';
        $valid = false;
    }
    if (strlen($password) < 8) {
        $errorMessage .= '<li>le mot de passe doit contenir au moint 8 caractere</li> ';
        $valid = false;
    }
    if (preg_match('/[a-z]/', $password) == 0) {
        $errorMessage .= '<li>le mot de passe doit contenir au moint une lettre miniscule</li> ';
        $valid = false;
    }
    if (preg_match('/[A-Z]/', $password) == 0) {
        $errorMessage .= '<li>le mot de passe doit contenir au moint une lettre Majuscule</li> ';
        $valid = false;
    }
    if (preg_match('/\d/', $password) == 0) {//un chiffre
         $errorMessage .= '<li>le mot de passe doit contenir au moint un chiffre</li> ';
         $valid = false;
    }
    if (preg_match('/[^a-zA-Z]/', $password) == 0) {
         $errorMessage .= '<li>le mot de passe doit contenir au moint un caractere special</li> ';
        $valid = false;
    }
}   
//special chars
//majuscule
//minuscule
//chiffre

if (empty($lastName) || strlen($lastName) > 100){
    $errorMessage .='<li>le champ nom est vide ou trop long (maximum 100 carractères).</li>';
    $valid = false;
}
if (empty($firstName) || strlen($firstName) > 100) {
    $errorMessage .='<li>le champ prénom est vide ou trop long (maximum 100 carractères).</li>';
    $valid = false;
}

if ($valid === true) {
    $password = password_hash ($password, PASSWORD_BCRYPT);
    $req = $db->prepare('INSERT INTO user (email, password, roles, nom, prenom) VALUES (:email, :password, :roles, :nom, :prenom)');
    $req->bindParam(':email', $email, PDO::PARAM_STR);
    $req->bindParam(':password', $password, PDO::PARAM_STR);
    $req->bindParam(':roles', $roles, PDO::PARAM_STR);
    $req->bindParam(':nom', $lastName, PDO::PARAM_STR);
    $req->bindParam(':prenom', $firstName, PDO::PARAM_STR);
    $req->execute();
    $_SESSION['notification'] = 'l\utilisateur a bien été créer.';
    header('Location: login.php');

}else {
    $errorMessage .='</li>';
    $_SESSION['notification'] = $errorMessage;
    header('Location: register.php');
}

}
/***************************************user-login****************************************************** */
elseif (isset($_GET['login'])){
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    $req = $db->prepare('SELECT id, email, password, prenom FROM user WHERE email=:email');
    $req->bindParam(':email', $email, PDO::PARAM_STR);
    $req->execute();
    if($req->rowCount() > 0) {
        $user = $req->fetch();
        if (password_verify($password, $user['password'])){
            $_SESSION['notification'] = "Bonjour {$user['prenom']} !";
            $_SESSION['user'] = $user['id'];
            if (isset($_Post['remember']) && $_POST['remember'] != NULL){
               setcookie('user', $user['id'], time()+60*60*24*30);
            }   
            header('Location: index.php');
        }
    }else{
        $_SESSION['notification'] = 'Adresse email invalide.';
        header('Location: login.php');
    }
}

/*****************************************user-login******************************************************/

elseif (isset($_GET['logout'])){
    unset($_SESSION['user']);
    setcookie('user', null, time()-1);
    $_SESSION['notification'] = 'Vous avez bien été déconnecté.';
    header('Location: login.php');
}
/*************************************************categories******************************************** */




elseif (isset($_GET['create-category'])){
    $name = htmlentities($_POST['name']);
    $description = htmlentities($_POST['description']);
    if (!empty($name) && strlen($name) <= 45 && !empty($description) && strlen($description)<= 65535){
        $req = $db->prepare('INSERT INTO category (name, description) VALUES (:name, :description)');
        $req->bindParam(':name', $name, PDO::PARAM_STR);
        $req->bindParam(':description', $description, PDO::PARAM_STR);
        $req->execute();
        $_SESSION['notificaion'] = 'La catégorie a bien été crée.';
        header('Location: index.php');
    }else{
        $_SESSION['notification'] = 'Une erreur s\est produite lors de la création de la catégorie.';
        header('Location: index.php');//revenir vers la page index.php
    }
}

/*******************************delete-categorie******************************************************** */
 
elseif (isset($_GET['delete-category'])){
    $id = (int)$_GET['delete-category'];//supprimer les articles
    $req = $db->query('SELECT img FROM post WHERE category_id=' .$id);//supp lesimages
    $imgs = $req->fetchAll();
    foreach ($imgs as $img) {
        unlink('');
    }
    $req = $db->query('DELETE FROM category WHERE id=' . $id);
    $_SESSION['notification'] = 'La catégorie ainsi que tous les articles associés ont bien été suprimés.';
    header('Location: index.php');
}


?>