<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if (!isset($_GET['stat']) || empty($_GET['stat'])) { http_response_code(412); die(); }
    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "admin_panel.manage.stats.view")) { http_response_code(412); die(); }
    $requested_stat = $_GET['stat'];
    
    try {
        $dbstats = new PDO("mysql:host=" . DB_HOST . ";dbname=rooms_the_stats", DB_USER, DB_PASSWORD);
        $dbstats->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(strtolower($requested_stat) === "visitors"){
            $visitorsResults = array();

            $sql = "SELECT DATE_FORMAT(visit_at, '%d/%m') AS at, value FROM visitors WHERE visit_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY visit_at;";
            $stmt = $dbstats->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($result); $i++){
                if(!in_array($result[$i]['at'], $visitorsResults)) $visitorsResults[$result[$i]['at']] = $result[$i]['value'];
            }

            echo json_encode($visitorsResults);
        } else if(strtolower($requested_stat) === "parties_played"){
            $partiesPlayedResults = array();

            $sql = "SELECT DATE_FORMAT(starting_date, '%d/%m') AS at FROM parties WHERE starting_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY starting_date;";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($result); $i++){
                $at = strval($result[$i]['at']);
                if(!array_key_exists($at, $partiesPlayedResults)) $partiesPlayedResults[$at] = 0;
                $partiesPlayedResults[$at]++;
            }

            echo json_encode($partiesPlayedResults);
        } else if(strtolower($requested_stat) === "registrations"){
            $registrationsResults = array();

            $sql = "SELECT DATE_FORMAT(register_date, '%d/%m') AS at FROM users WHERE register_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY register_date;";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($result); $i++){
                $at = strval($result[$i]['at']);
                if(!array_key_exists($at, $registrationsResults)) $registrationsResults[$at] = 0;
                $registrationsResults[$at]++;
            }

            echo json_encode($registrationsResults);
        } else {
            http_response_code(412);
            die();
        }
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>