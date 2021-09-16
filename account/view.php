<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    $uid = $_GET['uid'];

    if(!isset($uid) || empty($uid) || !userExist($uid)){
        header("Location: https://errors.roomsthegame.com/404");
        exit;
    }

    if(userIsLogged() && getLoggedUserId() == $uid){
        header("Location: https://account.roomsthegame.com/");
        exit;
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
        <title>Profil de <?= $user_infos['nick'] ?> | Rooms The Game</title>
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
                    <span class="profile-nick"><?= $user_infos['nick'] ?></span>
                    <div class="profile-friend-request">
                        <?php if(getRelation(getLoggedUserId(), $user_infos['uid']) == 0){ ?>
                            <a class="btn-primary slim" onclick="profile.sendFriendRequest(<?= $user_infos['uid'] ?>)">
                                <img src="https://assets.roomsthegame.com/images/icons/add.svg">
                                Demander en Ami
                            </a>
                        <?php } else if(getRelation(getLoggedUserId(), $user_infos['uid']) == 1){ ?>
                            <a class="btn-primary slim" onclick="profile.denyFriendRequest(<?= $user_infos['uid'] ?>)">
                                <img src="https://assets.roomsthegame.com/images/icons/cross.svg">
                                Retirer la demande d'Ami
                            </a>
                        <?php } else if(getRelation(getLoggedUserId(), $user_infos['uid']) == 2){ ?>
                            <a class="btn-primary slim" onclick="profile.removeFriend(<?= $user_infos['uid'] ?>)">
                                <img src="https://assets.roomsthegame.com/images/icons/cross.svg">
                                Retirer l'Ami
                            </a>
                        <?php } ?>
                    </div>
                    <div class="profile-info-ranks">
                        <?php foreach($user_ranks as $rank){ 
                            $rank_infos = getRankInfos($rank['rank_id']); ?>
                            <div class="user-rank" style="border-color: #<?= $rank_infos['color'] ?>;">
                                <?= $rank_infos['name'] ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if($user_preferences['private_profile']){ ?>
                        <h1>Ce profil est privé.</h1>
                    <?php } else { ?>
                        <h1>Informations</h1>
                        <div class="container-others">
                            <h5>Enregistré(e) le <?= $registerdate ?></h5>
                            <h5><?= isset($birthdate) ? 'Né(e) le ' . $birthdate : 'Aucune date de naissance enregistrée' ?></h5>              
                        </div>
                    <?php } ?>
                </div>
                <?php if(!$user_preferences['private_profile']){ ?>
                    <div class="profile-container full">
                        <h1>Statistiques</h1>
                        <!-- <div class="container-content">
                            
                        </div> -->
                    </div>
                <?php } ?>
            </div>
        </main>

        <?php require_once('/home/paesgi2021g1/www/assets/includes/footer.php') ?>
        <script src="https://assets.roomsthegame.com/js/profile.js"></script>
    </body>
</html>