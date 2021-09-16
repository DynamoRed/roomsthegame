<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(userIsLogged() && getUserInfos(getLoggedUserId())['mail_verified'] === "1"){
        header("Location: https://account.roomsthegame.com");
        exit;
    }

    function generateVCode(){
        return rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
    }

    if(isset($_GET['mail']) && !empty($_GET['mail']) && filter_var($_GET['mail'], FILTER_VALIDATE_EMAIL)){
        $sql = "SELECT * FROM users WHERE mail = :mail;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':mail', $_GET['mail']);
        $stmt->execute();
        $user_infos = $stmt->fetch();

        if($user_infos){
            if($user_infos['mail_verified']){
                $disable_verification_step = true;
                $verification_error = 'Le compte associé a cette adresse mail est déjà verifié !';
            } else {
                if(isset($_POST['finalCode']) && !empty($_POST['finalCode'])){
                    $sql = "SELECT * FROM verification_codes WHERE mail = :mail AND vcode = :vcode;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':mail', $_GET['mail']);
                    $stmt->bindParam(':vcode', $_POST['finalCode']);
                    $stmt->execute();
                    $code_infos = $stmt->fetch();

                    if($code_infos){
                        if($code_infos['active']){
                            $sql = "UPDATE verification_codes SET active = 0 WHERE vcode = :vcode AND mail = :mail;";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':vcode', $code_infos['vcode']);
                            $stmt->bindParam(':mail', $code_infos['mail']);
                            $stmt->execute();

                            $sql = "UPDATE users SET mail_verified = 1 WHERE mail = :mail;";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':mail', $_GET['mail']);
                            $stmt->execute();

                            $sql = "INSERT INTO users_preferences (uid) VALUES (:uid);";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':uid', $user_infos['uid']);
                            $stmt->execute();

                            $sql = "INSERT INTO users_ranks (uid, rank_id) VALUES (:uid, 0);";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':uid', $user_infos['uid']);
                            $stmt->execute();

                            $current_notification = [
                                'type' => 'success',
                                'content' => 'Compte vérifié ! Vous allez être redirigé(e)...',
                                'duration' => 30
                            ];
                            $disable_verification_step = true;

                            echo '<script>setTimeout(function(){ window.location.href = "https://auth.roomsthegame.com/login"; },3500);</script>';
                        } else {
                            $verification_error = 'Code Expiré ou Révoqué ! Veuillez contacter un Administrateur s\'il s\'agit d\'une erreur.';
                        }
                    } else {
                        $verification_error = 'Code Invalide ! Veuillez verifier votre code ou contacter un Administrateur.';
                    }
                } else {
                    $generated_vcode = generateVCode();
                    
                    $sql = "UPDATE verification_codes SET active = 0 WHERE mail = :mail AND active = 1;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':mail', $_GET['mail']);
                    $stmt->execute();

                    $sql = "INSERT INTO verification_codes (vcode, mail, active) VALUES(:vcode, :mail, 1);";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':vcode', $generated_vcode);
                    $stmt->bindParam(':mail', $_GET['mail']);
                    $stmt->execute();

                    $sql = "SELECT nick FROM users WHERE mail = :mail";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':mail', $_GET['mail']);
                    $stmt->execute();
                    $user_nick = $stmt->fetch();

                    $to = $_GET['mail']; 
                    $from = 'auth@roomsthegame.com'; 
                    $fromName = 'Rooms The Game'; 
                    $subject = 'Votre code de verification - Service d\'Authentification';  

                    $htmlContent = '<!DOCTYPE html>
                    <html>
                        <body style="background-color:#EDF2F4; width: 800px; height: 800px; font-family: Gilroy, \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; color: white;">
                            <img style="left: 100px; display: block;" height="506px" width="600px" src="https://auth.roomsthegame.com/getmailcode?vcode='.$generated_vcode.'&to='.$user_nick['nick'].'">
                            <span style="position: absolute; top: 650px; color: #EDF2F4; background-color: #0B090A; font-size: 18px; text-align: left;">Vous ne voyez pas d\'image ? Aucun soucis, votre code est le<br><strong style="letter-spacing: 3px;">'.$generated_vcode.'</strong></span>
                        </body>
                    </html>';

                    $headers = "From: $fromName"." <".$from.">"; 
                    $semi_rand = md5(time());  
                    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
                    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
                    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
                    "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
                    $message .= "--{$mime_boundary}--"; 
                    $returnpath = "-f" . $from; 

                    $mail = @mail($to, $subject, $message, $headers, $returnpath);  

                    if($mail){
                        $current_notification = [
                            'type' => 'info',
                            'content' => 'Un code de vérification a 6 chiffres a été envoyé sur votre adresse mail.',
                            'duration' => 15
                        ];
                    } else {
                        $current_notification = [
                            'type' => 'alert',
                            'content' => 'Une erreur est survenue lors de l\'envoie d\'un mail de confirmation. Veuillez recommencer !',
                            'duration' => 15
                        ];
                    }
                }
            }
        } else {
            $disable_verification_step = true;
            $verification_error = 'Cette adresse mail n\'est associée a aucun compte !';
        }
    } else {
        header("Location: https://errors.roomsthegame.com/400");
        exit;
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
        <title>Verification | Rooms The Game</title>
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
            <form method="post" action="" id="signInForm">
                <input id="authPageName" type="hidden" value="verification">
                <div class="auth-step">
                    <a href="https://www.roomsthegame.com" class="auth-hdr-logo"><img src="https://assets.roomsthegame.com/images/logo.png" class="logo"></a>
                    <div class="auth-main-step"><span>Etape 3/3</span></div>
                    <div class="auth-main-classic">
                        <div class="double-auth">
                            <input class="double-auth-input" type="text" name="vc1" maxlength="1" oninput="auth.signIn.vc.validateInput(1);" id="authVC1">
                            <input class="double-auth-input" type="text" name="vc2" maxlength="1" oninput="auth.signIn.vc.validateInput(2);" id="authVC2">
                            <input class="double-auth-input" type="text" name="vc3" maxlength="1" oninput="auth.signIn.vc.validateInput(3);" id="authVC3">
                            <input class="double-auth-input" type="text" name="vc4" maxlength="1" oninput="auth.signIn.vc.validateInput(4);" id="authVC4">
                            <input class="double-auth-input" type="text" name="vc5" maxlength="1" oninput="auth.signIn.vc.validateInput(5);" id="authVC5">
                            <input class="double-auth-input" type="text" name="vc6" maxlength="1" oninput="auth.signIn.vc.validateInput(6);" id="authVC6">
                        </div>

                        <input type="hidden" name="finalCode" id="finalVC" value="">

                        <div class="auth-missed">
                            <?php if(!isset($disable_verification_step)){ ?>
                                <a href="https://auth.roomsthegame.com/verification?mail=<?= $_GET['mail'] ?>">Vous n'avez rien recu ? Cliquez ici</a>
                            <?php } ?>
                        </div>

                        <div class="auth-next-step-container">
                            <a class="auth-next-step btn-primary btn-left-img" id="authVCSubmit" onclick="auth.signIn.vc.validateCode();">
                                Valider
                                <img src="https://assets.roomsthegame.com/images/icons/check.svg">
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script src="https://assets.roomsthegame.com/js/auth.js"></script>
        <?php if(isset($disable_verification_step) && $disable_verification_step){
            echo '<script>auth.signIn.vc.disableStep();</script>';
        } ?>
    </body>
</html>