<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if (!isset($_GET['rank_id']) || empty($_GET['rank_id'])) { http_response_code(412); die(); }
    $requester_uid = getLoggedUserId();
    if(!rankExist($_GET['rank_id'])) {
        http_response_code(400);
        return;
    }
    
    try {
        $rank = getRankInfos($_GET['rank_id']);
        $rank_permissions = getRankPermissions($_GET['rank_id']);
        $rank['permissions'] = $rank_permissions;
        
        echo json_encode($rank);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>