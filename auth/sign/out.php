<?php
    if(!session_id()){
        session_name("RSID");
        session_set_cookie_params(0, '/', '.roomsthegame.com');
        session_start();
    }
    unset($_SESSION);
    setcookie("uid", "", time()-3600, "/", ".roomsthegame.com");
    setcookie("upwd", "", time()-3600, "/", ".roomsthegame.com");
    header("Location: https://www.roomsthegame.com/");
    session_destroy();
    exit;
?>