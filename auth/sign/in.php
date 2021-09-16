<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(userIsLogged()){
        header("Location: https://account.roomsthegame.com");
        exit;
    }

    function generateID(){
        return rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
    }

    if(isset($_POST['mail']) && !empty($_POST['mail']) && filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
        $sql = "SELECT * FROM users WHERE mail = :mail";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':mail', $_POST['mail']);
        $stmt->execute();
        $user_infos = $stmt->fetch();

        if(!$user_infos){
            $sql = "SELECT * FROM users WHERE nick = :nick";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nick', $_POST['pseudo']);
            $stmt->execute();
            $user_infos = $stmt->fetch();

            if(!$user_infos){
                try { 
                    $generated_user_id = generateID();
                    $hashed_password = hash("sha256",$_POST['password']);
                    $user_ip = getLoggedUserIp();
    
                    $sql = "SELECT * FROM users WHERE uid = :uid";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':uid', $generated_user_id);
                    $stmt->execute();
                    $exist_id = $stmt->fetch();
    
                    while($exist_id){
                        $generated_user_id = generateID();
                        $stmt->execute();
                        $exist_id = $stmt->fetch();
                    }
                                    
                    $sql = "INSERT INTO users (uid, nick, mail, register_date, upwd) VALUES(:uid, :nick, :mail, NOW(), :upwd);";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':uid', $generated_user_id);
                    $stmt->bindParam(':nick', $_POST['pseudo']);
                    $stmt->bindParam(':mail', $_POST['mail']);
                    $stmt->bindParam(':upwd', $hashed_password);
                    $stmt->execute();
        
                    header("Location: https://auth.roomsthegame.com/verification?mail=". $_POST['mail']);
                    exit;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {$verification_error = 'Ce pseudo n\'est pas disponible !';}
        } else {$verification_error = 'Cet email est déjà associé a un compte !';}
    }

    if(isset($verification_error) && !empty($verification_error)){
        $current_notification = [
            'type' => 'alert',
            'content' => $verification_error,
            'duration' => 10
        ];
    }

    $fileList = glob('/home/paesgi2021g1/www/assets/images/captcha/*');
    $randomFile = rand(0,(count($fileList)-1));

    $i = 0;
    foreach($fileList as $fileName){
        if($i === $randomFile) $randomFile = $fileName;
        $i++;
    }

    $randomFile = str_replace('/home/paesgi2021g1/www/assets/images/captcha/', '', $randomFile);
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <title>Inscription | Rooms The Game</title>
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
            echo '<script>addNotification("'. $current_notification['type'] .'", "'. $current_notification['content'] .'", '. $current_notification['duration'] .')</script>';
        } ?>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="auth-main">
            <form method="post" action="" id="signInForm">
                <input id="authPageName" type="hidden" value="signin">
                <div class="auth-step">
                    <a href="https://www.roomsthegame.com"><img src="https://assets.roomsthegame.com/images/logo.png" class="logo"></a>
                    <div class="auth-main-step"><span>Etape 1/3</span></div>
                    <div class="auth-main-classic">
                        <input class="auth-input auto-reset-inputs" name="pseudo" id="authPseudo" placeholder="Pseudonyme" type="text">
                        <input class="auth-input auto-reset-inputs" name="mail" id="authMail" placeholder="Email" type="email" autocomplete="off">
                        <input class="auth-input auto-reset-inputs" name="mailCofirm" id="authMailConfirm" placeholder="Confirmation de l'Email" type="email" autocomplete="off">
                        <div class="auth-missed">
                            <a href="https://auth.roomsthegame.com/login">Déjà enregistré ? Connectez vous !</a>
                        </div>
                        <div class="auth-next-step-container">
                            <a class="auth-next-step btn-primary btn-left-img" onclick="auth.signIn.nextStep(1);">
                                Suivant
                                <img src="https://assets.roomsthegame.com/images/icons/arrow_next.svg">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="auth-step">
                    <a href="https://www.roomsthegame.com" class="auth-hdr-logo"><img src="https://assets.roomsthegame.com/images/logo.png" class="logo"></a>
                    <div class="auth-main-step"><span>Etape 2/3</span></div>
                    <div class="auth-main-classic">
                        <input class="auth-input auto-reset-inputs" name="password" id="authPassword" placeholder="Mot de Passe" type="password">
                        <input class="auth-input auto-reset-inputs" name="passwordConfirm" id="authPasswordConfirm" placeholder="Confirmation du Mot de Passe" type="password">
                        <span class="auth-captcha-title">Veuillez selectionner l'image correctement orientée.</span>
                        <div class="auth-captcha" id="captcha">
                            <a class="captcha-img-selector"><img src="https://assets.roomsthegame.com/images/captcha/<?= $randomFile ?>" class="captcha-img"></a>
                            <a class="captcha-img-selector"><img src="https://assets.roomsthegame.com/images/captcha/<?= $randomFile ?>" class="captcha-img"></a>
                            <a class="captcha-img-selector"><img src="https://assets.roomsthegame.com/images/captcha/<?= $randomFile ?>" class="captcha-img"></a>
                            <a class="captcha-img-selector"><img src="https://assets.roomsthegame.com/images/captcha/<?= $randomFile ?>" class="captcha-img"></a>
                            <a class="captcha-img-selector"><img src="https://assets.roomsthegame.com/images/captcha/<?= $randomFile ?>" class="captcha-img"></a>
                            <a class="captcha-img-selector"><img src="https://assets.roomsthegame.com/images/captcha/<?= $randomFile ?>" class="captcha-img"></a>
                        </div>

                        <div class="auth-next-step-container">
                            <a class="auth-next-step btn-primary btn-left-img" onclick="auth.signIn.nextStep(1);">
                                Finaliser
                                <img src="https://assets.roomsthegame.com/images/icons/arrow_next.svg">
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script src="https://assets.roomsthegame.com/js/auth.js"></script>
    </body>
</html>