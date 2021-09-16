<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    if(!isset($_GET['o']) || empty($_GET['o'])){
        header("Location: https://errors.roomsthegame.com/404");
        exit;
    }

    $manage_category = $_GET['o'];
    if($manage_category == "gcomponents"){
        $manage_category_name = "des Composants de jeu";
        if(isset($_GET['s']) && !empty($_GET['s'])){
            $manage_section = $_GET['s'];
            echo $manage_section;
            if($manage_section == "characters"){
                $manage_category_name = "des Personnages (Jeu)";
            } else if($manage_section == "roles"){
                $manage_category_name = "des Roles (Jeu)";
            } else if($manage_section == "rooms"){
                $manage_category_name = "des Salles (Jeu)";
            } else if($manage_section == "actions"){
                $manage_category_name = "des Actions (Jeu)";
            } else {
                header("Location: https://errors.roomsthegame.com/404");
                exit;
            }
        }
    } else if($manage_category == "ranks"){
        $manage_category_name = "des Rangs";
    } else if($manage_category == "news"){
        $manage_category_name = "des Actualités";
    } else if($manage_category == "devpath"){
        $manage_category_name = "du DevPath";
    } else if($manage_category == "support"){
        $manage_category_name = "du Support";
    } else {
        header("Location: https://errors.roomsthegame.com/404");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""><![endif]-->
    <!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang=""><![endif]-->
    <!--[if IE 8]><html class="no-js lt-ie9" lang=""><![endif]-->
    <!--[if gt IE 8]><html class="no-js" lang=""><![endif]-->
    <head>
        <title>Gestion <?= $manage_category_name ?> - Administration | Rooms The Game</title>
        <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/panel.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
        <script src="https://assets.roomsthegame.com/js/ap/ap_base.js"></script>
        <script src="https://assets.roomsthegame.com/js/ap/ap_ranks.js"></script>
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
    </head>
    <body>
        <!--[if lt IE 8]>
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->
        <?php require_once('/home/paesgi2021g1/www/assets/includes/header_panel.php') ?>
        
        <main>
            <?php require_once('/home/paesgi2021g1/www/assets/includes/nav_panel.php') ?>

            <div class="panel-main">
                <div class="panel-main-top">
                    <h1 class="panel-title">Gestion <?= $manage_category_name ?></h1>
                </div>
                <?php if($manage_category == "gcomponents"){ 
                    if(isset($manage_section)){ ?>

                    <?php } else { ?>
                        <div class="panel-gcomponents">
                            <a class="panel-gcomponent-container" href="https://panel.roomsthegame.com/manage/gcomponents&s=characters">
                                <img src="https://assets.roomsthegame.com/images/gcomponents/characters/Marie_Poppins.svg" alt="Personnages">
                                <h1>Personnages</h1>
                            </a>
                            <a class="panel-gcomponent-container" href="https://panel.roomsthegame.com/manage/gcomponents&s=roles">
                                <img src="" alt="Roles">
                                <h1>Roles</h1>
                            </a>
                            <a class="panel-gcomponent-container" href="https://panel.roomsthegame.com/manage/gcomponents&s=rooms">
                                <img src="https://assets.roomsthegame.com/images/gcomponents/rooms/Empty_Room.svg" alt="Salles">
                                <h1>Salles</h1>
                            </a>
                            <a class="panel-gcomponent-container" href="https://panel.roomsthegame.com/manage/gcomponents&s=actions">
                                <img src="" alt="Actions">
                                <h1>Actions</h1>
                            </a>
                        </div>
                <?php }
                    } else if($manage_category == "ranks"){ ?>
                        <div class="panel-ranks" id="panelRanks">
                            
                        </div>
                <?php } else if($manage_category == "news"){ ?>

                <?php } else if($manage_category == "devpath"){ ?>

                <?php } else if($manage_category == "support"){ ?>

                <?php } ?>
            </div>
        </main>
    </body>
</html>