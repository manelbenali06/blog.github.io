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
    require_once('../config/database.php');

    //reccupration de données
    //je selectionne la table post et le champs categorie_name dans ma table categorie  
    $reqPosts = $db->query('SELECT post.*, category.name  AS category_name FROM post  INNER JOIN category ON post.category_id = category.id ORDER BY post.id DESC');
    $posts = $reqPosts->fetchAll();
    $reqCategories = $db->query('SELECT*FROM category');
    $categories = $reqCategories->fetchAll();
?>

<!DOCTYPE html>

<html lang="fr">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>BLOG • espace administrateur</title>

        <link rel="stylesheet" href="../assets/css/fontawesome.css">

    </head>

    <body>

        <h1>Espace administrateur</h1>

        <h2>Articles</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TITRE</th>
                    <th>AUTEUR</th>
                    <th>CRÉATION</th>
                    <th>CATEGORIE</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($posts as $post) { ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td><?= $post['title'] ?></td>
                            <td><?= $post['author'] ?></td>
                            <td><?= $post['created_at'] ?></td>
                            <td><?= $post['category_name'] ?></td>
                            <td>
                                <a href="form.php?update=<?= $post['id'] ?>"><i class="fas fa-pen-square"></i></a>
                                <a href="treatment.php?delete=<?= $post['id'] ?>"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>    
                    <?php }
                ?>
            </tbody>
        </table>

        <a href="form.php">Ajouter un article</a>

        <!--gestion des catégories-->

        <h2>Catégories</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOM</th>
                    <th>DESCRIPTION</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category){ ?><!--parcourir un tableau pour chaque ligne de $categories je l'extrait et je le met dans $category-->
                    <tr>
                        <td><?= $category['id'] ?></td>
                        <td><?= $category['name'] ?></td>
                        <td><?= $category['description'] ?></td>
                        <td>
                            <a href="#"><i class="fas fa-pen-square"></i></a>
                            <a href="treatment.php?delete-category=<?=$category['id'] ?>"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <form action="treatment.php?create-category" method="post"><!--ou envoyer le formulaire et comment-->
            <label for="name">Nom</label>
            <input type="text" name="name" maxlength="45" required>
            <label for="description">Description</label>
            <textarea name="description" cols="30" rows="3" maxlength="65535" required></textarea>
            <input type="submit" value="Ajouter une catégorie">
        </form>

        <h2>Liens</h2>

        <a href="register.php">Ajouter un utilisteur</a>
        <a href="login.php">Connexion</a>
        <a href="treatment.php?logout">Déconnexion</a>

    </body>

</html>