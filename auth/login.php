<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(userIsLogged()){
        header("Location: https://account.roomsthegame.com");
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
            if($user_infos['mail_verified']){
                setcookie("uid", $user_infos['uid'], 0, "/", ".roomsthegame.com");
                setcookie("upwd", $user_infos['upwd'], 0, "/", ".roomsthegame.com");
                $redirection = (isset($_GET['to']) && !empty($_GET['to'])) ? $_GET['to'] : "https://account.roomsthegame.com";
                header("Location: ".$redirection);
                exit;
            } else {
                $verification_error = 'Votre adresse mail doit être verifié ! <a href=\"https://auth.roomsthegame.com/verification?mail=' . $user_infos['mail'] . '\">Cliquez ici pour la verifier</a>';
            }
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
        <title>Connexion | Rooms The Game</title>
        <meta charset="utf-8">
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <meta http-equiv="content-language" content="fr">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" type="image/png" href="<?= $GLOBALS['favicon_url'] ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/auth.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
    </head>
    <body>
        <?php if(isset($current_notification)){
            echo '<script>addNotification("'. $current_notification['type'] .'", "'. $current_notification['content'] .'", '. $current_notification['duration'] .');</script>';
        } ?>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        
        <div class="auth-main">
            <form method="post" action="" id="loginForm">
                <input id="authPageName" type="hidden" value="login">
                <div class="auth-step visible">
                    <a href="https://www.roomsthegame.com"><img src="https://assets.roomsthegame.com/images/logo.png" class="logo"></a>
                    <div class="oauth2-auth">
                        <a href="" class="oauth2-button discord">
                            <img src="https://assets.roomsthegame.com/images/icons/discord_white_rounded.svg">
                        </a>
                        <a href="" class="oauth2-button twitter">
                            <img src="https://assets.roomsthegame.com/images/icons/twitter_white_rounded.svg">
                        </a>
                        <a href="" class="oauth2-button facebook">
                            <img src="https://assets.roomsthegame.com/images/icons/facebook_white_rounded.svg">
                        </a>
                        <a href="" class="oauth2-button google">
                            <img src="https://assets.roomsthegame.com/images/icons/google_white_rounded.svg">
                        </a>
                    </div>
                    <div class="auth-main-separator"><span>OU</span></div>
                    <div class="auth-main-classic">
                        <input class="auth-input auto-reset-inputs" placeholder="Pseudonyme ou Email" name="pseudoOrMail" type="text" id="authPseudoOrMail">
                        <input class="auth-input auto-reset-inputs" placeholder="Mot de Passe" name="password" type="password" id="authPassword">
                        <div class="auth-missed">
                            <a href="https://auth.roomsthegame.com/sign/in">Pas encore de compte ? Enregistrez vous !</a>
                            <a href="https://auth.roomsthegame.com/lostpassword">Mot de passe oublié ? Renouvellez le !</a>
                        </div>
                        <a class="auth-submit btn-primary" onclick="auth.login.validateForm();">
                            <img src="https://assets.roomsthegame.com/images/icons/register.svg">
                            Se Connecter
                        </a>
                    </div>

                    <a class="auth-backto btn-primary" href="https://www.roomsthegame.com">
                        <img src="https://assets.roomsthegame.com/images/icons/arrow_back.svg" title="Retour">
                        Retour
                    </a>
                </div>
            </form>
        </div>

        <script src="https://assets.roomsthegame.com/js/auth.js"></script>
    </body>
</html>