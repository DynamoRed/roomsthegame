<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if (!userIsLogged()) {
        header("Location: https://auth.roomsthegame.com/login");
        exit;
    }

    if(isset($_GET['e']) && !empty($_GET['e'])){
        switch(htmlspecialchars($_GET['e'])){
            case "1002":
                $error = "Cette partie est pleine !";
                break;
            case "1003":
                $error = "Cette partie est privée !";
                break;
            case "1004":
                $error = "Cette partie est introuvable !";
                break;
            case "1005":
                $error = "Cette partie est déjà en cours !";
                break;
            case "1006":
                $error = "Cette partie est finie !";
                break;
            case "1007":
                $error = "Vous êtes déjà dans une partie !";
                break;
            default: break;
        }
    }
 
    $user_infos = getUserInfos(getLoggedUserId());
    $user_ranks = getUserRanks($user_infos['uid']);
?>
<!DOCTYPE html>
<html lang="fr">
    <!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""><![endif]-->
    <!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang=""><![endif]-->
    <!--[if IE 8]><html class="no-js lt-ie9" lang=""><![endif]-->
    <!--[if gt IE 8]><html class="no-js" lang=""><![endif]-->
    <head>
        <title>Hub de Jeu | Rooms The Game</title>
        <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/hub.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
        <meta name="robots" content="index">
        <meta name="googlebot" content="index">
        <link rel="canonical" href="https://www.roomsthegame.com/" />
        <script src="https://assets.roomsthegame.com/js/hub.js"></script>
    </head>

    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <?php if(isset($error)){
            echo '<script>addNotification("alert", "'. $error .'", 5);</script>';
        } ?>
        <?php require_once('/home/paesgi2021g1/www/assets/includes/header.php') ?>
        <main>
            <div class="parties-container">
                <a class="btn-primary" onclick="createParty()">
                    <img src="https://assets.roomsthegame.com/images/icons/add.svg">
                    Creer une partie
                </a>
            </div>
        </main>
        <script src="https://assets.roomsthegame.com/js/burger-menu.js"></script>
    </body>
</html>