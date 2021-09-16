<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if (!isset($_GET['user_id']) || empty($_GET['user_id'])) { http_response_code(412); die(); }
    $requester_uid = getLoggedUserId();
    if(!userExist($_GET['user_id'])){ http_response_code(412); die(); }
    
    try {
        $user = getUserInfos($_GET['user_id']);
        $user['user_ranks'] = getUserRanks($_GET['user_id']);
        if (userIsBan($user['uid'])) {
            $user['ban'] = getBanReason($user['uid']);
        }
        echo json_encode($user);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>
