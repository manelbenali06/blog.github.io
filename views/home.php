<div class="container my-3">

    <h1>Accueil</h1>

    <div class="row">
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sint ad excepturi deserunt aliquam quasi exercitationem possimus praesentium placeat, amet vero explicabo nesciunt consectetur numquam nulla veniam harum beatae. Quaerat, aperiam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum sit aliquam esse harum ullam. Animi enim, non et minima, optio iusto, sed ipsa eum culpa incidunt est id similique cumque.</p>
    </div>

    <div class="row">
        <h2>Les derniers articles</h2>
        <?php
            $req = $db->query('SELECT * FROM post ORDER BY id DESC LIMIT 3');
            $posts = $req->fetchAll();
            foreach ($posts as $post) { ?>
                <div class="col-sm-12 col-md-4 p-3">
                    <div class="card">
                        <img src="assets/img/posts/<?= $post['img'] ?>" class="card-img-top" alt="<?= $post['alt'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $post['title'] ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= date('Y-m-d', strtotime($post['created_at'])) . ' - ' . $post['author'] ?></h6>
                            <p class="card-text"><?= substr($post['content'], 0, 200) . '...' ?></p>
                            <a href="index.php?page=post&article=<?= $post['id'] ?>" class="btn btn-secondary">Lire la suite</a>
                        </div>
                    </div>
                </div>
            <?php }
        ?>
        <div class="col-12 text-end mb-5">
            <a href="index.php?page=posts" class="btn btn-outline-dark">Tous les articles</a>
        </div>
    </div>

    <div class="row">
        <h2>Les rédacteurs</h2>
        <!-- récupérer les utilisateurs ayant le rôle de rédacteur -->
    </div>

</div>