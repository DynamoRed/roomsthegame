<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_GET['user_id']) || !isset($_GET['reason']) || empty($_GET['user_id']) || empty($_GET['reason'])){ http_response_code(412); die(); }
    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "global.ban_user")) { http_response_code(412); die(); }
    
    try {
        $user_id = $_GET['user_id'];
        $ban_reason = $_GET['reason'];
        $sql = "INSERT INTO bans (uid, reason) VALUES (:uid, :reason);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':reason', $ban_reason);
        $stmt->execute(); 
        
        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $_GET['user_id'] . '" target="_BLANK">' . getUserInfos($_GET['user_id'])['nick'] . '</a> a été banni par <a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> pour la raison: "' . $ban_reason . '"');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>