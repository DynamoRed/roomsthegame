<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    $id = $_GET['mid'];
    
    try {
        $sql = "SELECT id, reason, created_by, DATE_FORMAT(ending_date, '%d-%m-%YT%H:%i') AS ending_date, DATE_FORMAT(starting_date, '%d-%m-%YT%H:%i') AS starting_date FROM maintenances WHERE id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $maintenance = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($maintenance);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>