<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_GET['rank_id'])){ http_response_code(400); die(); }
    if(!rankExist($_GET['rank_id'])){ http_response_code(412); die(); }
    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "ranks.remove")) { http_response_code(412); die(); }

    try {
        $rank_id = $_GET['rank_id'];
        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a supprimé le rang "' . getRankInfos($rank_id)['name'] . '"');

        $sql = "DELETE FROM ranks WHERE rank_id = :rank_id;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':rank_id', $rank_id);
        $stmt->execute();        
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>