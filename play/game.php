<?php
require '/home/paesgi2021g1/www/assets/php/base.php';

$db = connectToDatabase();

if (!userIsLogged()) {
    header("Location: https://auth.roomsthegame.com/login");
    exit;
}

if (!isset($_GET['gid']) || empty($_GET['gid'])) {
    header("Location: https://play.roomsthegame.com/hub?e=1004");
    exit;
}

if (!partyExist($_GET['gid'])) {
    header("Location: https://play.roomsthegame.com/hub?e=1004");
    exit;
}

$is_party_member = false;
$party_members = getPartyMembers($_GET['gid']);
for($i = 0; $i < count($party_members); $i++){
    if($party_members[$i]['uid'] == getLoggedUserId()) $is_party_member = true;
}

if (getPartyInfos($_GET['gid'])['state'] == 1 && !$is_party_member) {
    header("Location: https://play.roomsthegame.com/hub?e=1005");
    exit;
}

if (getPartyInfos($_GET['gid'])['state'] == 2) {
    header("Location: https://play.roomsthegame.com/hub?e=1006");
    exit;
}

if (getPartyInfos($_GET['gid'])['private'] == 1 && getPartyInfos($_GET['gid'])['manager_uid'] != getLoggedUserId()) {
    if (getRelation(getLoggedUserId(), getPartyInfos($_GET['gid'])['manager_uid']) != 2) {
        header("Location: https://play.roomsthegame.com/hub?e=1003");
        exit;
    }
}

if (getPartyInfos($_GET['gid'])['max_members'] <= getPartyMembersCount($_GET['gid'])) {
    header("Location: https://play.roomsthegame.com/hub?e=1002");
    exit;
}


if(strtotime(getPartyInfos($_GET['gid'])['ending_date']) <= time()) {
    header("Location: https://play.roomsthegame.com/hub?e=1006");
    exit;
}

if(userIsInParty(getLoggedUserId()) && !$is_party_member) {
    header("Location: https://play.roomsthegame.com/hub?e=1007");
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
    <title>Partie de <?= getUserInfos(getPartyInfos($_GET['gid'])['manager_uid'])['nick'] ?> | Rooms The Game</title>
    <?php require_once('/home/paesgi2021g1/www/assets/includes/metas.php') ?>
    <link rel="stylesheet" href="https://assets.roomsthegame.com/css/game.css">
    <link rel="stylesheet" href="https://assets.roomsthegame.com/css/cards.css">
    <?php require_once('/home/paesgi2021g1/www/assets/includes/essentials.php') ?>
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <link rel="canonical" href="https://play.roomsthegame.com/" />
    <script src="https://assets.roomsthegame.com/js/games/global.js"></script>
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <main>
        <div class="left-containers">
            <div class="game-players container">
                <img src="https://assets.roomsthegame.com/images/logo.png" class="logo">
                <div class="game-players-bottom" id="membersContainer"></div>
            </div>
            <div class="game-settings container">
                <div class="primary-btns">
                    <a class="btn-primary" onclick="launchGame()">
                        <img src="https://assets.roomsthegame.com/images/icons/play_arrow.svg">
                        Lancer la partie
                    </a>
                    <a class="btn-primary" onclick="copyPartyURL('<?= $_GET['gid'] ?>')">
                        <img src="https://assets.roomsthegame.com/images/icons/link.svg">
                        Copier l'invitation
                    </a>
                </div>
            </div>
        </div>
        <div class="right-containers">
            <div class="game-chat container">
                <div class="chat">
                    <ul id="msg-list">
                    </ul>
                </div>
                <div class="sendbox">
                    <p id="carCount">120/120</p>
                    <input type="text" id="msg-input" name="send-message" placeholder="Écrivez quelque chose..." maxlength="120">
                </div>
            </div>
        </div>
    </main>
    <script>
        async function copyPartyURL(pcode) {
            if (navigator.clipboard) {
                await navigator.clipboard.writeText("https://roomsthegame.com/g/" + pcode);
                alert("Invitation copiée :-)");
            }
        }
    </script>
    <div class="game-members-container" id="membersContainer"></div>
    <script src="https://assets.roomsthegame.com/js/games/chat.js"></script>
    <script src="https://assets.roomsthegame.com/js/games/game.js"></script>
</body>

</html>