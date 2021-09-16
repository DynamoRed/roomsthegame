<?php
    require_once '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!isset($_GET['e']) || empty($_GET['e'])){ header("Location: https://www.roomsthegame.com/"); exit(); }
    
    $errors = array(
        "400" => "Requête Incorrecte",
        "401" => "Authorisation Réquise",
        "402" => "Paiement Réquis",
        "403" => "Accès Refusé",
        "404" => "Page non trouvée",
        "405" => "Méthode Non Autorisée",
        "406" => "Encodage Interdit",
        "407" => "Authentification Proxy Réquise",
        "408" => "Demande Expirée",
        "409" => "Requête Conflictueuse",
        "410" => "Introuvable",
        "411" => "Longueur du Contenu Requise",
        "412" => "Echec de la Précondition",
        "413" => "Entité de Requête trop longue",
        "414" => "URI de Requête trop longue",
        "415" => "Type de Média Non Supporté",
        "500" => "Erreur Interne du Serveur",
        "501" => "Non Implementé",
        "502" => "Passerelle Incorrecte",
        "503" => "Service Indisponible",
        "504" => "Passerelle Expirée",
        "505" => "Version HTTP Non Supportée",
    );

    if(!array_key_exists($_GET['e'], $errors)){ header("Location: https://errors.roomsthegame.com/404"); exit(); }
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $errors[$_GET["e"]] ?> - Erreur <?= $_GET["e"] ?> | Rooms The Game</title>
        <meta name="description" content="Page Non Trouvée">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/errors.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <span class="error-main">
            <div class="first-number"><?= str_split($_GET["e"])[0] ?></div><div class="second-number"><?= str_split($_GET["e"])[1] ?></div><div class="third-number"><?= str_split($_GET["e"])[2] ?></div>
        </span>

        <span class="error-description"><?= $errors[$_GET["e"]] ?></span>

        <a class="btn-primary" href="https://www.roomsthegame.com">
            <img src="https://assets.roomsthegame.com/images/icons/arrow_back.svg" title="Revenir en lieux sûrs">
            Revenir en lieux sûrs
        </a>
        
        <?php if($_GET['e'] === "404") { ?>
            <img src="https://assets.roomsthegame.com/images/snake_head.png" class="easter-egg-icon" title="On se fait une partie ?">
            <script src="https://assets.roomsthegame.com/js/base.js"></script>
            <script src="https://assets.roomsthegame.com/js/404.js"></script>
            <link rel="stylesheet" href="https://assets.roomsthegame.com/css/404.css">
        <?php } ?>
    </body>
</html>