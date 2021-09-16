<?php
    require_once '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!checkForMaintenance()){
        header("Location: https://www.roomsthegame.com");
        exit;
    }

    if(isset($_POST['pseudoOrMail']) && !empty($_POST['pseudoOrMail'])
        && isset($_POST['password']) && !empty($_POST['password'])){
        $hashed_password = hash("sha256",$_POST['password']);

        $sql = "SELECT * FROM users WHERE (mail = :mailornick OR nick = :mailornick) AND upwd = :upwd;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':mailornick', $_POST['pseudoOrMail']);
        $stmt->bindParam(':upwd', $hashed_password);
        $stmt->execute();
        $user_infos = $stmt->fetch();

        if($user_infos){
            $user_ranks = getUserRanks($user_infos['uid']);

            if(userHavePermission($user_infos['uid'], "maintenance.bypass")){
                setcookie("uid", $user_infos['uid'], 0, "/", "roomsthegame.com");
                setcookie("upwd", $user_infos['upwd'], 0, "/", "roomsthegame.com");
                $_SESSION["uid"] = $user_infos['uid'];
                $_SESSION["upwd"] = $user_infos['upwd'];
                header("Location: https://panel.roomsthegame.com");
                exit;
            }
           
            $verification_error = 'Vous n\'etes pas habilité a vous connecter ici !';
        } else {
            $verification_error = 'Pseudo/Mail ou Mot de Passe incorrect !';
        }
    }

    if(isset($verification_error) && !empty($verification_error)){
        $current_notification = [
            'type' => 'alert',
            'content' => $verification_error,
            'duration' => 10
        ];
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
        <title>Site en Maintenance | Rooms The Game</title>
        <meta name="description" content="Page Non Trouvée">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/errors.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
    <body>
        <?php if(isset($current_notification)){
            echo '<script>addNotification("'. $current_notification['type'] .'", "'. $current_notification['content'] .'", '. $current_notification['duration'] .');</script>';
        } ?>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="error-plain-container">
            <img src="https://assets.roomsthegame.com/images/logo.png" class="logo">
            <div class="error-plain-description">
                <h3>Notre site est actuellement en maintenance jusqu'au <?= getMaintenanceInfos()['ending_date'] ?>. (Initiée par <?= getUserInfos(getMaintenanceInfos()['created_by'])['nick'] ?>)</h3>
                <span>Nous sommes désolé de ce désagrément et mettons tout en oeuvre pour vous redonner accès a notre plateforme</span>
            </div>
            <form method="post" action="" onsubmit="" id="loginForm">
                <div class="auth-main-separator"><span class="developer-auth">CONNEXION EN MODE DEVELOPPEUR</span></div>
                <div class="auth-main-classic">
                    <input class="auth-input" placeholder="Pseudonyme ou Email" name="pseudoOrMail" type="text" id="authPseudoOrMail" oninput="this.className = 'auth-input'">
                    <input class="auth-input" placeholder="Mot de Passe" name="password" type="password" id="authPassword" oninput="this.className = 'auth-input'">
                    <a class="auth-submit btn-primary" onclick="auth.login.validateForm();">
                        <img src="https://assets.roomsthegame.com/images/icons/register.svg">
                        Se Connecter
                    </a>
                </div>
            </form>
        </div>

        <script src="https://assets.roomsthegame.com/js/auth.js"></script>
    </body>
</html>