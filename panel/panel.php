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
    <title>Panel d'Administration | Rooms The Game</title>
    <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
    <link rel="stylesheet" href="https://assets.roomsthegame.com/css/panel.css">
    <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
    <script src="https://assets.roomsthegame.com/js/ap/ap_base.js"></script>
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
                <h1 class="panel-title">Accueil</h1>
            </div>
            <div class="panel-stats">
                <div class="graph-container">
                    <canvas id="graph-1"></canvas>
                    <div class="infos">
                        <div class="info">
                            <div style="background-color: #BA181B;"></div>
                            <p>Nombre de visiteurs</p>
                        </div>
                        <div class="info">
                            <div style="background-color: #ff9b13;"></div>
                            <p>Nombre d'inscriptions</p>
                        </div>
                    </div>
                </div>
                <div class="graph-container">
                    <canvas id="graph-2"></canvas>
                    <div class="infos">
                        <div class="info">
                            <div style="background-color: #BA181B;"></div>
                            <p>Parties jouées</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-main-other">
                <div class="panel-main-container" id="maintenanceContainer">
                    <div class="panel-main-maintenances-top">
                        <h1>Maintenances</h1>
                        <a class="maintenance-button add" onclick="panel.openNewMaintenancePanel();">
                            <img title="Ajouter" src="https://assets.roomsthegame.com/images/icons/add.svg">
                        </a>
                    </div>
                </div>
                <!--<div class="panel-main-container">
                    <h1>Captcha</h1>
                    
                </div>-->
            </div>
        </div>
    </main>

    <script src="https://assets.roomsthegame.com/js/stats/graph.js"></script>
</body>

</html>