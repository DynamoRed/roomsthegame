<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_GET['query']) || empty($_GET['query'])){ http_response_code(412); die(); }

    try {
        $searched_users = searchUsers($_GET['query']);
        $requester_uid = getLoggedUserId();
        for($i = 0; $i < count($searched_users); $i++){
            $searched_users[$i]['avatar'] = getUserAvatar($searched_users[$i]['uid']);
            $searched_users[$i]['private_profile'] = getUserPreferences($searched_users[$i]['uid'])['private_profile'];
            if(userIsBan($searched_users[$i]['uid'])){
                $searched_users[$i]['ban'] = getBanReason($searched_users[$i]['uid']);
            }
        }

        echo json_encode($searched_users);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>
