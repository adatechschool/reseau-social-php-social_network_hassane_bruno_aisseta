<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
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
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId =intval($_GET['user_id']);
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
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
             
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice :  <?= $user["alias"]?>
                        (n° <?php echo $userId ?>)
                    </p>
                </section>
                <?php

                $followers = "SELECT count(followed_user_id) as num FROM followers WHERE following_user_id = " . $_SESSION['connected_id']  . " AND followed_user_id = " . $userId; 

                $res= $mysqli->query($followers);
                $counter= $res->fetch_assoc();
                if (!$counter || $counter["num"]== 0){


                    if($_SESSION['connected_id'] != $userId){
                       if(isset($_POST["button1"])){
                        $laQuestionFollowers = " INSERT INTO followers "
                        . "(id, followed_user_id, following_user_id) "
                        . "VALUES(NULL, "
                        . $_POST['user_id'] 
                        . ", "
                        . $_POST['current_user_id'] 
                        . ');'
                        ;

                        $ok = $mysqli->query($laQuestionFollowers);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter l'abonnement: " . $mysqli->error;
                        } else
                        {
                            echo "abonné à :" . $user["alias"];
                            header("Location: wall.php?user_id=" . $userId);
                        }
                       }

                    
                ?>
                    <form method="post">
                        <input type="hidden" name="user_id" value=<?=$userId?>>
                        <input type="hidden" name= "current_user_id" value=<?=$_SESSION['connected_id']?>>
                        <input type="submit" name="button1" value="S'abonner"/>
               </form>
                <?php }} ?>
            </aside>
            <main>

            <?php
                    if($_SESSION['connected_id'] != $userId && isset($_POST["message_id"])){
                       
                       
                         $message_id = $_POST["message_id"];
                         $user_id = $_SESSION['connected_id'];
                         
                         
                        

                         $check_likes = "SELECT * FROM likes WHERE post_id = '$message_id' AND user_id = '$user_id'";
                         $result = $mysqli->query($check_likes);

                       


                         if ($result->num_rows == 0) {

                            $insert_query = "INSERT INTO likes (post_id, user_id) VALUES ('$message_id', '$user_id')";
                    
                            if ($mysqli->query($insert_query) === TRUE) {
                                echo "Vous avez aimé ce message.";
                            } else {
                                echo "Erreur lors du like : " . $mysqli->error;
                            }
                        }     
                    }
                ?>
                <?php   



            if(isset( $_SESSION['connected_id']) &&  $_SESSION['connected_id'] == $userId){
    
                 if(isset($_POST["message"])){
                        
                        $matches= [];
                        
                        preg_match_all('/#[\p{L}]+/u', $_POST["message"],$matches);

                       // print_r($matches);

                        $lesTags= "SELECT * FROM tags"; 
                        $ok =$mysqli->query($lesTags);
                        
                        $labels = array();
                        $ids = array();
                        
                        if ($ok-> num_rows > 0){
                            while ($res = $ok->fetch_assoc()){
                                $labels[] = $res['label'];
                                $ids[]= $res['id'];
                            }
                        }
                        $validId= array();
                        
                        foreach ($matches[0] as $tag) {
                            $tag = str_replace('#' , "", $tag);
                            $index = array_search($tag, $labels);
                            //echo $index . "  ";
                            echo $tag;
                            if (!in_array($tag, $labels)) {
                                //echo $tag;
                            }else {
                                array_push($validId, $index);
                                //echo $tag;
                                //echo $index;
                                //print_r($validId);
                            }
                           
                        }


                        
                        

                        


                      
                        

                        $laQuestionPostEnSql = "INSERT INTO posts " 
                        . "(id, user_id, content, created, parent_id) " 
                        . "VALUES(NULL, "
                        .  $_SESSION['connected_id']
                        .", '" 
                        . $_POST["message"]
                        . "', "
                        . "NOW(), "
                        . "NULL);"
                        ;
    
                        //echo $laQuestionPostEnSql;
    
                        $ok = $mysqli->query($laQuestionPostEnSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            $params= 'SELECT max(id) as maximum FROM posts';
                            $ok = $mysqli->query($params);
                            $result = $ok->fetch_assoc();

                            foreach ($validId as $value) {

                                $posttag = 'INSERT INTO posts_tags (id, post_id, tag_id) VALUES (NULL,' . $result['maximum'] . ' , ' . $ids[$value] . ')';
                                $ok_insert= $mysqli->query($posttag);
                            }

                            //echo "Message posté en tant que :" . $user["alias"];
                            echo $result['maximum'];

                        }
                    }
                        
                ?>
                <form style="background-color: white; margin-bottom: 20px;" action=<?= "wall.php?user_id=" . $userId?> method="post">
                            <input type='hidden' name='???' value='achanger'>
                            <dl>
                                <dt><label for='message'>Message</label></dt>
                                <dd><textarea name='message'></textarea></dd>
                            </dl>
                            <input type='submit'>
                        </form> 
                        
                        <?php }?>
                <?php
                /**
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $laQuestionEnSql = "
                    SELECT posts.id, posts.content, posts.created, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                
              
                while ($post = $lesInformations->fetch_assoc())
                {
                    ?>                
                    <article>
                        <h3>
                            <time datetime='2020-02-01 11:12:13' > <?= $post["created"]?></time>
                        </h3>
                        <address>par  <?= $post["author_name"]?></address>
                        <div>
                            <p><?= $post["content"] ?></p>
                        </div>                                            
                        <footer>
                            <form action=<?= "wall.php?user_id=" . $userId?> method="post">
                            <input type="hidden" name="message_id" value="<?php echo $post["id"]; ?>">
                            <input type="submit" name="likes" value="J'aime"/>  
                            <small>♥ <?= $post["like_number"]?></small>
                            </form>
                            <a href="">#<?= $post["taglist"]?></a>
                          
                        </footer>
                    </article>

                <?php } ?>

                 
            </main>
        </div>
    </body>
</html>
