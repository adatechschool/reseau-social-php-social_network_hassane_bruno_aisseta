<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnements</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>
            <nav id="menu">
            <a href="news.php">Actualités</a>
                <a href=<?="wall.php?user_id=" .  $_SESSION['connected_id']?>>Mur</a>
                <a href=<?="feed.php?user_id=" .  $_SESSION['connected_id']?>>Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href=<?="settings.php?user_id=" .  $_SESSION['connected_id']?>>Paramètres</a></li>
                    <li><a href=<?="followers.php?user_id=" .  $_SESSION['connected_id']?>>Mes suiveurs</a></li>
                    <li><a href=<?="subscriptions.php?user_id=" .  $_SESSION['connected_id']?>>Mes abonnements</a></li>
                    <li><a href="index.php">Déconnexion</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">
        <?php
            /**
             * Cette page est TRES similaire à wall.php. 
             * Vous avez sensiblement à y faire la meme chose.
             * Il y a un seul point qui change c'est la requete sql.
             */
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             */
            $userId = intval($_GET['user_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            include 'config.php';
            ?>
            <aside>
            <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes dont
                        l'utilisatrice <?php echo $user["alias"] ?>
                        suit les messages
                    </p>

                </section>
            </aside>
            <main class='contacts'>
                <?php

                if (isset($_POST["button2"])){

                    $delete = "DELETE FROM followers  WHERE followed_user_id='" . $_POST['followed_id'] . "' AND following_user_id='". $userId . "'";

                    $result = $mysqli->query($delete);
                }
                // Etape 3: récupérer le nom de l'utilisateur
                $laQuestionEnSql = "
                    SELECT users.* 
                    FROM followers 
                    LEFT JOIN users ON users.id=followers.followed_user_id 
                    WHERE followers.following_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Etape 4: à vous de jouer
                //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
                while($followings = $lesInformations->fetch_assoc()){
                ?>
                <article>
                    <img src="user.jpg" alt="blason"/>
                    <h3><?= $followings['alias'] ?></h3>
                    <p><?= $followings['id'] ?></p>  
                    
                    <form action=" " method="post"> 
                        <input type="hidden" name="followed_id" value="<?= $followings['id'] ?>"/> 
                        <input type="submit" name="button2" value="Se désabonner"/>
                    </form>         
                </article>
                <?php }?>
            </main>
        </div>
    </body>
</html>
