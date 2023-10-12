<?php
session_start();



?> 
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Paramètres</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php 
    if (isset($_SESSION['connected_id'])) {
    ?> 
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
        <div id="wrapper" class='profile'>


            <aside>

                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les informations de l'utilisatrice
                        n° <?php echo intval($_GET['user_id']) ?></p>

                </section>
            </aside>
            <main>
                <?php
               

                /**
                 * Etape 1: Les paramètres concernent une utilisatrice en particulier
                 * La première étape est donc de trouver quel est l'id de l'utilisatrice
                 * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
                 * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
                 * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
                 */
                $userId = intval($_GET['user_id']);

                /**
                 * Etape 2: se connecter à la base de donnée
                 */
                include 'config.php';

                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "
                    SELECT users.*, 
                    count(DISTINCT posts.id) as totalpost, 
                    count(DISTINCT given.post_id) as totalgiven, 
                    count(DISTINCT recieved.user_id) as totalrecieved 
                    FROM users 
                    LEFT JOIN posts ON posts.user_id=users.id 
                    LEFT JOIN likes as given ON given.user_id=users.id 
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                    WHERE users.id = '$userId' 
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                $user = $lesInformations->fetch_assoc();

                /**
                 * Etape 4: à vous de jouer
                 */
                /*
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $targetDirectory = '/uploads/'; 
                    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                    // Vérifier si le fichier est une image réelle
                    $check = getimagesize($_FILES["image"]["tmp_name"]);
                    if ($check === false) {
                        echo "Le fichier n'est pas une image.";
                        $uploadOk = 0;
                    }

                    // Vérifier la taille de l'image
                    if ($_FILES["image"]["size"] > 5000000) {
                        echo "L'image est trop grande. Veuillez choisir une image plus petite.";
                        $uploadOk = 0;
                    }

                    // Autoriser certains formats d'image
                    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
                    if (!in_array($imageFileType, $allowedExtensions)) {
                        echo "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
                        $uploadOk = 0;
                    }

                    // Vérifier si $uploadOk est à 0 à cause d'une erreur
                    if ($uploadOk == 0) {
                        echo "L'upload de votre image a échoué.";
                    } else {
                        // Tout est ok, essayer d'uploader l'image
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                            echo "L'image " . htmlspecialchars(basename($_FILES["image"]["name"])) . " a été téléchargée et enregistrée.";
                        } else {
                            echo "Une erreur est survenue lors de l'upload de votre image.";
                        }
                    }
                }
                */


                
                ?>                
                <article class='parameters'>
                    <h3>Mes paramètres</h3>
                    <dl>
                        <dt>Pseudo</dt>
                        <dd><?= $user["alias"]?></dd>
                        <dt>Email</dt>
                        <dd><?= $user["email"]?></dd>
                        <dt>Nombre de message</dt>
                        <dd><?= $user["totalpost"]?></dd>
                        <dt>Nombre de "J'aime" donnés </dt>
                        <dd><?= $user["totalgiven"]?></dd>
                        <dt>Nombre de "J'aime" reçus</dt>
                        <dd><?= $user["totalrecieved"]?></dd>
                    </dl>

                 <!--
                    <form action="settings.php" method="post" enctype="multipart/form-data">
                        <label for="image">Sélectionnez une image :</label>
                        <input type="file" name="image" id="image" accept="image/*" required>
                        <button type="submit" name="submit">Envoyer</button>
                    </form>
            -->
                    

                </article>
            </main>
        </div>
        <?php

            } else {
            
             
               
            
            }?>
    </body>
    
</html>
