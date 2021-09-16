<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    try {
        echo json_encode(getAllPermissions());
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>
