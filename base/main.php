<?php
require '/home/paesgi2021g1/www/assets/php/base.php';
?>
<!DOCTYPE html>
<html lang="fr">
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang=""><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang=""><![endif]-->
<!--[if gt IE 8]><html class="no-js" lang=""><![endif]-->

<head>
    <title>Rooms The Game</title>
    <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
    <link rel="stylesheet" href="https://assets.roomsthegame.com/css/index.css">
    <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
    <script src="https://assets.roomsthegame.com/js/header.js"></script>
    <meta name="robots" content="index">
    <meta name="googlebot" content="index">
    <link rel="canonical" href="https://www.roomsthegame.com/" />
</head>

<body class="index-body">
    <!--[if lt IE 8]>
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->
    <?php require_once('/home/paesgi2021g1/www/assets/includes/header.php') ?>

    <main>
        <section class="first">
            <div class="middle">
                <img src="https://assets.roomsthegame.com/images/logo.png" class="logo" alt="Middle Logo">
            </div>
            <div class="bottom">
                <a class="btn-primary" id="" onclick="" href="https://play.roomsthegame.com/hub">
                    <img src="https://assets.roomsthegame.com/images/icons/play_arrow.svg">
                    Lancer une partie
                </a>
                <a href="#informations" class="smoothScroll"><img class="scroll-indicator" src="https://assets.roomsthegame.com/images/icons/double_arrow_down.svg" alt="Indicateur de Scroll"></a>
            </div>
        </section>
        <section class="second" id="informations">
            <div class="top">
                <span>Arriverez-vous à vous échapper ?</span>
            </div>
            <div class="bottom">
                <div class="left">
                    <img src="https://assets.roomsthegame.com/images/thumbnail.png" alt="Thumbnail">
                </div>
                <div class="right">
                    <p>
                        Entrez dans un complexe rempli de salles piégées et tentez de vous en échapper !
                        Attention a bien trouver qui est traitre et qui est votre allié.
                        Cela pourrait vous être utile !
                    </p>
                </div>
            </div>
        </section>
        <section class="third">
            <div class="top">
                <span>6 Personnages Exclusifs</span>
            </div>
            <div class="middle">
                <div class="slider">
                    <div class="slider-content">
                        <div class="slide slide-0">
                            <img src="https://assets.roomsthegame.com/images/gcomponents/characters/Annabelle.svg">
                        </div>
                        <div class="slide slide-1">
                            <img src="https://assets.roomsthegame.com/images/gcomponents/characters/Arthur.svg">
                        </div>
                        <div class="slide slide-2">
                            <img src="https://assets.roomsthegame.com/images/gcomponents/characters/Hunger_Games.svg">
                        </div>
                        <div class="slide slide-3">
                            <img src="https://assets.roomsthegame.com/images/gcomponents/characters/Marie_Poppins.svg">
                        </div>
                        <div class="slide slide-4">
                            <img src="https://assets.roomsthegame.com/images/gcomponents/characters/MIB.svg">
                        </div>
                        <div class="slide slide-5">
                            <img src="https://assets.roomsthegame.com/images/gcomponents/characters/Stuart_Little.svg">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom">
                <a class="btn-primary prev" id="" onclick="">
                    <img src="https://assets.roomsthegame.com/images/icons/arrow_back.svg">
                    Précédent
                </a>
                <div class="active-card-indicator">
                    <div class="indicator active"></div>
                    <div class="indicator"></div>
                    <div class="indicator"></div>
                    <div class="indicator"></div>
                    <div class="indicator"></div>
                    <div class="indicator"></div>
                </div>
                <a class="btn-primary next btn-left-img" id="" onclick="">
                    Suivant
                    <img src="https://assets.roomsthegame.com/images/icons/arrow_next.svg">
                </a>
            </div>
        </section>
    </main>

    <?php require_once('/home/paesgi2021g1/www/assets/includes/footer.php') ?>
    <script src="https://assets.roomsthegame.com/js/slider.js"></script>
</body>

</html>