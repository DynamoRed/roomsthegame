<?php
require '/home/paesgi2021g1/www/assets/php/base.php';

if (!userHavePermission(getLoggedUserId(), "admin_panel.moderation.games")) {
    header("Location: https://errors.roomsthegame.com/403");
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
    <title>Parties en cours - Administration | Rooms The Game</title>
    <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
    <link rel="stylesheet" href="https://assets.roomsthegame.com/css/panel.css">
    <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
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
                <h1 class="panel-title">Parties en cours</h1>
                <div class="search-bar">
                    <img src="https://assets.roomsthegame.com/images/icons/search.svg" class="search-bar-icon">
                    <input type="text" class="search-bar-input" placeholder="Rechercher">
                </div>
            </div>
        </div>
    </main>
</body>

</html>