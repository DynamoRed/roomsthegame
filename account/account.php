<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    $uid = getLoggedUserId();

    if(isset($_POST['pseudo']) || isset($_POST['birthdate']) || isset($_POST['mail']) || isset($_POST['newpassword']) || isset($_FILES['newavatar'])){
        if(isset($_POST['password'])){
            $user_actual_infos = getUserInfos($uid);
            $hashed_password = hash("sha256", $_POST['password']);

            if($hashed_password == $user_actual_infos['upwd']){
                if(isset($_POST['pseudo']) && !empty($_POST['pseudo'])){
                    $sql = "UPDATE users SET nick = :newvalue WHERE uid = :uid;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':newvalue', $_POST['pseudo']);
                    $stmt->bindParam(':uid', $uid);
                    $stmt->execute();
                }

                if(isset($_POST['birthdate']) && !empty($_POST['birthdate'])){
                    $sql = "UPDATE users SET birthdate = :newvalue WHERE uid = :uid;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':newvalue', $_POST['birthdate']);
                    $stmt->bindParam(':uid', $uid);
                    $stmt->execute();
                }

                if(isset($_POST['mail']) && !empty($_POST['mail'])){
                    $sql = "UPDATE users SET mail = :newvalue WHERE uid = :uid;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':newvalue', $_POST['mail']);
                    $stmt->bindParam(':uid', $uid);
                    $stmt->execute();
                }

                if(isset($_POST['newpassword']) && !empty($_POST['newpassword'])){
                    $hashed_newpassword = hash("sha256", $_POST['newpassword']);
                    $sql = "UPDATE users SET upwd = :newvalue WHERE uid = :uid;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':newvalue', $hashed_newpassword);
                    $stmt->bindParam(':uid', $uid);
                    $stmt->execute();

                    setcookie("upwd", $hashed_newpassword, 0, "/", "roomsthegame.com");
                    $_SESSION["upwd"] = $hashed_newpassword;

                    header('Location: https://account.roomsthegame.com/');
                    exit;
                }

                if(isset($_FILES['newavatar']) && !empty($_FILES['newavatar']['name'])){
                    $newavatar_name= $_FILES["newavatar"]["name"];
                    $newavatar_ext = pathinfo($newavatar_name,PATHINFO_EXTENSION);
                    $newavatar_path = "/home/paesgi2021g1/www/assets/images/avatars/". $uid;
                    $existings_avatars = glob($newavatar_path.'.*'); 
                    foreach($existings_avatars as $file) {
                        if(is_file($file)) 
                            unlink($file); 
                    }
                    move_uploaded_file($_FILES["newavatar"]["tmp_name"], $newavatar_path . '.' . $newavatar_ext);
                }

                $current_notification = [
                    'type' => 'success',
                    'content' => 'Changement(s) pris en compte avec succès !',
                    'duration' => 5
                ];
            } else {
                $current_notification = [
                    'type' => 'alert',
                    'content' => 'Votre mot de passe actuel est incorrect !',
                    'duration' => 5
                ];
            }            
        } else {
            $current_notification = [
                'type' => 'alert',
                'content' => 'Vous devez saisir votre mot de passe actuel !',
                'duration' => 5
            ];
        }
    }
    
    $user_infos = getUserInfos($uid);
    $user_ranks = getUserRanks($uid);

    $user_avatar = getUserAvatar($uid);
    $user_preferences = getUserPreferences($uid);

    if(isset($user_infos['birthdate'])){
        $birthdate = new DateTime($user_infos['birthdate']);
        $birthdate = $birthdate->format('d/m/Y');
    }

    $registerdate = new DateTime($user_infos['register_date']);
    $registerdate = $registerdate->format('d/m/Y');
?>
<!DOCTYPE html>
<html lang="fr">
    <!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""><![endif]-->
    <!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang=""><![endif]-->
    <!--[if IE 8]><html class="no-js lt-ie9" lang=""><![endif]-->
    <!--[if gt IE 8]><html class="no-js" lang=""><![endif]-->
    <head>
        <title>Profil | Rooms The Game</title>
        <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
        <link rel="stylesheet" href="https://assets.roomsthegame.com/css/account.css">
        <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
        <meta name="robots" content="index">
    </head>
    <body>
        <!--[if lt IE 8]>
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->
        <?php require_once('/home/paesgi2021g1/www/assets/includes/header.php') ?>

        <main>
            <div class="profile-section">
                <div class="profile-container full info">
                    <div class="profile-badges">
                        <?php if($user_infos['verified_account']){ ?>
                            <img class="badge-account-icon" src="https://assets.roomsthegame.com/images/icons/verified_user.svg" title="Compte Vérifié">
                        <?php } 
                        if($user_preferences['private_profile']){ ?>
                            <img class="badge-account-icon" id="privateProfileIcon" src="https://assets.roomsthegame.com/images/icons/private_profile.svg" title="Compte Privé">
                        <?php } ?>
                    </div>
                    <img class="profile-avatar" src="<?= $user_avatar ?>" alt="Avatar">
                    <div class="profile-info-ranks">
                        <?php foreach($user_ranks as $rank){ 
                            $rank_infos = getRankInfos($rank['rank_id']); ?>
                            <div class="user-rank" style="border-color: #<?= $rank_infos['color'] ?>;">
                                <?= $rank_infos['name'] ?>
                            </div>
                        <?php } ?>
                    </div>
                    <h1>Mes informations</h1>
                    <div class="container-others">
                        <h5>Enregistré(e) le <?= $registerdate ?></h5>
                        <h5><?= isset($birthdate) ? 'Né(e) le ' . $birthdate : 'Aucune date de naissance enregistrée' ?></h5>              
                    </div>
                    <div class="container-content">
                        <form action="" method="post" enctype="multipart/form-data" id="changeInfosForm">
                            <input name="pseudo" placeholder="<?= $user_infos['nick'] ?>" id="pseudo" type="text" class="auto-reset-inputs">
                            <input name="birthdate" value="<?= $user_infos['birthdate'] ?>" title="Date de Naissance" id="birthdate" type="date" min="1950-12-31" max="<?= date('Y-m-d') ?>" class="auto-reset-inputs">
                            <input name="mail" placeholder="<?= $user_infos['mail'] ?>" id="mailAddress" type="email" class="auto-reset-inputs space-after-input">
                            <input name="newpassword" placeholder="Nouveau mot de passe (Facultatif)" id="newPassword" type="password" class="auto-reset-inputs">
                            <input name="newpasswordconfirm" placeholder="Confirmation du mot de passe (Facultatif)" id="newPasswordConfirm" type="password" class="auto-reset-inputs space-after-input">
                            <div class="avatar-input space-after-input">
                                <p>Avatar: <span class="actual-file" id="inputActualFile"></span></p>
                                <div class="input-file">
                                    Selectionner
                                    <input name="newavatar" id="avatar" type="file" id="inputFile" accept="image/x-png,image/gif,image/jpeg" />
                                </div>
                            </div>
                            <input name="password" placeholder="Mot de passe actuel (Obligatoire)" id="actualPassword" type="password" class="auto-reset-inputs space-after-input">

                            <a class="profile-infos-submit btn-primary" onclick="profile.changeInfos();">
                                <img src="https://assets.roomsthegame.com/images/icons/save.svg">
                                Enregistrer
                            </a>
                        </form>
                    </div>
                </div>
                <div class="profile-row-section mobile">
                    <div class="profile-container full-horizontal">
                        <h1>Mes statistiques</h1>
                        <!-- <div class="container-content">
                            
                        </div> -->
                    </div>
                </div>
                <div class="profile-row-section">
                    <div class="profile-container links-accounts">
                        <h1>Mes comptes externes</h1>
                        <div class="container-content">
                            <div class="link-account-container">
                                <div class="link-account-icon-container discord">
                                    <img src="https://assets.roomsthegame.com/images/icons/discord_white.svg" class="link-account-icon">
                                </div>
                            
                                <h2>Discord</h2>
                                <a class="btn-primary slim" href="">
                                    <img src="https://assets.roomsthegame.com/images/icons/link.svg">
                                    Lier
                                </a>
                            </div>

                            <div class="link-account-container ">
                                <div class="link-account-icon-container google">
                                    <img src="https://assets.roomsthegame.com/images/icons/google_white.svg" class="link-account-icon">
                                </div>
                            
                                <h2>Google</h2>
                                <a class="btn-primary slim" href="">
                                    <img src="https://assets.roomsthegame.com/images/icons/link.svg">
                                    Lier
                                </a>
                            </div>

                            <div class="link-account-container ">
                                <div class="link-account-icon-container twitter">
                                    <img src="https://assets.roomsthegame.com/images/icons/twitter_white.svg" class="link-account-icon">
                                </div>
                            
                                <h2>Twitter</h2>
                                <a class="btn-primary slim" href="">
                                    <img src="https://assets.roomsthegame.com/images/icons/link.svg">
                                    Lier
                                </a>
                            </div>

                            <div class="link-account-container">
                                <div class="link-account-icon-container facebook">
                                    <img src="https://assets.roomsthegame.com/images/icons/facebook_white.svg" class="link-account-icon">
                                </div>
                            
                                <h2>Facebook</h2>
                                <a class="btn-primary slim" href="">
                                    <img src="https://assets.roomsthegame.com/images/icons/link.svg">
                                    Lier
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="profile-container preferences">
                        <h1>Mes préférences</h1>
                        <div class="container-content">
                            <div class="preference-container">
                                <img src="https://assets.roomsthegame.com/images/icons/dark_mode.svg" class="preference-icon">
                                <h2>Thème Sombre</h2>
                                <form class="preference-form">
                                    <label class="input-switch <?php if($user_preferences['dark_mode']){ echo 'checked'; } ?>">
                                        <input type="checkbox" id="darkModeInput" name="dark_mode">
                                        <span class="switch-round-slider"></span>
                                    </label>
                                </form>
                            </div>
                            <div class="preference-container">
                                <img src="https://assets.roomsthegame.com/images/icons/lock.svg" class="preference-icon">
                                <h2>Profil privé</h2>
                                <form class="preference-form">
                                    <label class="input-switch <?php if($user_preferences['private_profile']){ echo 'checked'; } ?>">
                                        <input type="checkbox" name="private_profile">
                                        <span class="switch-round-slider"></span>
                                    </label>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="profile-container full-horizontal">
                        <h1>Mes statistiques</h1>
                        <!-- <div class="container-content">
                            
                        </div> -->
                    </div>
                    <div class="profile-container friend-requests">
                        <h1>Demande d'amis</h1>
                        <?php foreach(getRelations($user_infos['uid']) as $relation) {
                                if(getRelation($user_infos['uid'], $relation['fuid']) == 1){ ?>
                                    <div class="friend-request" suid="<?= $relation['fuid'] ?>">
                                        <div class="friend-request-left">
                                            <img src="<?= getUserAvatar($relation['fuid']) ?>" class="friend-request-avatar">
                                            <a href="https://account.roomsthegame.com/view/<?= $relation['fuid'] ?>" target="_BLANK"><?= getUserInfos($relation['fuid'])['nick'] ?></a>
                                        </div>
                                        <div class="friend-request-right">
                                            <a class="friend-request-button accept" onclick="profile.acceptFriendRequest(<?= $relation['fuid'] ?>);">
                                                <img src="https://assets.roomsthegame.com/images/icons/check.svg" title="Accepter">
                                            </a>
                                            <a class="friend-request-button deny" onclick="profile.denyFriendRequest(<?= $relation['fuid'] ?>);">
                                                <img src="https://assets.roomsthegame.com/images/icons/cross.svg" title="Refuser">
                                            </a>
                                        </div>
                                    </div>
                            <?php }
                            }?>
                    </div>
                    <div class="profile-container friends">
                        <h1>Amis</h1>
                        <?php foreach(getRelations($user_infos['uid']) as $relation) {
                                if(getRelation($user_infos['uid'], $relation['fuid']) == 2){ ?>
                                    <div class="friend-container" suid="<?= $relation['fuid'] ?>">
                                        <div class="friend-container-left">
                                            <img src="<?= getUserAvatar($relation['fuid']) ?>" class="friend-avatar">
                                            <a href="https://account.roomsthegame.com/view/<?= $relation['fuid'] ?>" target="_BLANK"><?= getUserInfos($relation['fuid'])['nick'] ?></a>
                                        </div>
                                        <div class="friend-container-right">
                                            <a class="friend-button deny" onclick="profile.removeFriend(<?= $relation['fuid'] ?>);">
                                                <img src="https://assets.roomsthegame.com/images/icons/cross.svg" title="Retirer">
                                            </a>
                                        </div>
                                    </div>
                            <?php }
                            }?>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once('/home/paesgi2021g1/www/assets/includes/footer.php') ?>
        <script src="https://assets.roomsthegame.com/js/profile.js"></script>
    </body>
</html>