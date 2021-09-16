<?php
    require_once '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!userIsBan(getLoggedUserId()) && !ipIsBan(getLoggedUserIp())){
        header("Location: https://www.roomsthegame.com");
        exit;
    }
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
        <title>Vous êtes banni ! | Rooms The Game</title>
        <meta name="description" content="Page Non Trouvée">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/errors.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="error-plain-container">
            <img src="https://assets.roomsthegame.com/images/logo.png" class="logo">
            <div class="error-plain-description">
                <h3>Il semblerait que vous ou une des ip avec lesquelles vous avez acceder a notre site soit banni(e) pour la raison <u><?= getBanReason(getLoggedUserId()) ?></u></h3>
                <span>Si vous pensez qu'il s'agit d'une erreur veuillez vous rendre sur notre <a href="https://support.roomsthegame.com/unban" target="_BLANK">Support</a></span>
            </div>
        </div>
    </body>
</html>