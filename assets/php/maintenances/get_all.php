<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();

    try {
        $sql = "SELECT id, reason, created_by, DATE_FORMAT(ending_date, '%d/%m/%Y %H:%i') AS ending_date, DATE_FORMAT(starting_date, '%d/%m/%Y %H:%i') AS starting_date FROM maintenances ORDER BY starting_date DESC;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $maintenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for($i = 0; $i < count($maintenances); $i++){
            $maintenances[$i]['created_by'] = getUserInfos($maintenances[$i]['created_by']);
            if(strtotime($maintenances[$i]['ending_date']) > time()
                && strtotime($maintenances[$i]['starting_date']) <= time()){
                $maintenances[$i]['is_active'] = true;
            }
        }

        echo json_encode($maintenances);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>