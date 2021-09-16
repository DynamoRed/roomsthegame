<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_GET['user_id']) || empty($_GET['user_id'])){ http_response_code(412); die(); }
    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "global.unban_user")) { http_response_code(412); die(); }

    try {
        $user_id = $_GET['user_id'];
        $sql = "DELETE FROM bans WHERE uid = :uid;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':uid', $user_id);
        $stmt->execute(); 

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $_GET['user_id'] . '" target="_BLANK">' . getUserInfos($_GET['user_id'])['nick'] . '</a> a été débanni par <a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a>');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>