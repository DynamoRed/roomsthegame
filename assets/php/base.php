<?php
    ini_set('session.cookie_domain', '.roomsthegame.com');

    if(!session_id()){
        session_name("RSID");
        session_set_cookie_params(0, '/', '.roomsthegame.com');
        session_start();
    }    

    //----GLOBALS VARS----//

    $GLOBALS['favicon_url'] = "https://assets.roomsthegame.com/images/logo-short-white-180x180.png";

    define('DB_HOST', "localhost");
    define('DB_NAME', "rooms_the_game");
    define('DB_USER', "root");
    define('DB_PASSWORD', "********************"); 

    //--------------------//

    if(isset($_COOKIE['uid']) && !empty($_COOKIE['uid']) && isset($_COOKIE['upwd']) && !empty($_COOKIE['upwd'])){
        $_SESSION['uid'] = $_COOKIE['uid'];
        $_SESSION['upwd'] = $_COOKIE['upwd'];
    }

    //--------------------//

    /**
    * Connexion a la base de donnée
    * @return mixed 
    */
    function connectToDatabase(){
        $GLOBALS['db'] = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
        $GLOBALS['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $GLOBALS['db'];
    }

    $GLOBALS['db'] = connectToDatabase();

    /**
    * Recuperation de l'ip de l'utilisateur qui visite la page
    * @return string 
    */
    function getLoggedUserIp() {  
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
           $ip = $_SERVER['HTTP_CLIENT_IP'];  
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        } else{  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }  

    /**
    * Ecrire une nouvelle log
    * @param string $content
    * @return void 
    */
    function registerNewLog($content){
        $sql = "INSERT INTO general_logs (content) VALUES (:content);";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->execute();
    }

    /**
    * Recuperer les logs du site
    * @return array 
    */
    function getLogs(){
        $sql = "SELECT content, DATE_FORMAT(at, '[%a %d/%m/%Y - %Hh%imin%ss]') AS at FROM general_logs;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $logs;
    }

    /**
    * Recuperer les utilisateurs du site
    * @return array 
    */
    function getAllUsers($min=0, $max=0) {  
        $max = intval($max);
        $min = intval($min);
        $sql = "SELECT 	
        uid,
        nick,
        mail,
        upwd,
        register_date,
        birthdate,
        pplayed,
        pwins,
        points,
        mail_verified,
        verified_account,
        htchange_pwd FROM users ORDER BY nick";
        if($max > 0) $sql .= " LIMIT " . $max;
        if($min > 0) $sql .= " OFFSET " . $min;
        $sql .= ";";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $users;
    }  

    /**
    * Rechercher les utilisateurs du site
    * @return array 
    */
    function searchUsers($query) {  
        $transformedQuery = '%'.$query.'%';
        $sql = "SELECT 	
        uid,
        nick,
        mail,
        upwd,
        register_date,
        birthdate,
        pplayed,
        pwins,
        points,
        mail_verified,
        verified_account,
        htchange_pwd FROM users WHERE (uid LIKE :query) OR (mail LIKE :query) or (nick LIKE :query) ORDER BY nick";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':query', $transformedQuery);
        $stmt->execute();
        $searched_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT uid FROM users_ipv4_address WHERE ipv4_address LIKE :query";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':query', $query);
        $stmt->execute();
        $searched_ip = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for($i = 0; $i < count($searched_ip); $i++){
            array_push($searched_users, getUserInfos($searched_ip[$i]['uid']));
        }

        return $searched_users;
    }  

    /**
    * Recuperer le nombre d'utilisateurs du site
    * @return int 
    */
    function getUsersCount() {  
        $sql = "SELECT uid FROM users;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $users_count = $stmt->rowCount();

        return $users_count;
    }  

    /**
    * Recuperer les rangs du site
    * @return array 
    */
    function getAllRanks() {  
        $sql = "SELECT 	
        rank_id,
        name,
        color,
        inherit_lvl,
        is_operator FROM ranks ORDER BY inherit_lvl DESC;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $ranks;
    }  

    /**
    * Recuperer les permissions du site
    * @return array 
    */
    function getAllPermissions() {  
        $sql = "SELECT permission_name FROM permissions;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $permissions;
    }  

    /**
    * Verifier si l'utilisateur est connecté au site
    * @return bool 
    */
    function userIsLogged(){
        if(isset($_SESSION['uid']) && !empty($_SESSION['uid']) && isset($_SESSION['upwd']) && !empty($_SESSION['upwd'])){
            $sql = "SELECT uid FROM users WHERE uid = :uid AND upwd = :upwd;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION['uid']);
            $stmt->bindParam(':upwd', $_SESSION['upwd']);
            $stmt->execute();
            $user_infos = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user_infos ? true : false;
        }
        return false;
    }

    /**
    * Recuperer l'utilisateur actuellement connécté au site
    * @return string
    * @return false Si aucun utilisateur n'est connecté
    */
    function getLoggedUserId(){
        if(userIsLogged()){
            if(isset($_SESSION['uid']) && !empty($_SESSION['uid'])){    
                return $_SESSION['uid'];
            }
        }
        return false;
    }

    /**
    * Recuperer le code de partie de l'utilisateur actuellement connécté au site
    * @param string $uid
    * @return string
    * @return false Si l'uid n'est pas valide ou s'il n'est pas dans une partie
    */
    function getUserParty($uid){
        if(userExist($uid)){
            $sql = "SELECT pcode FROM parties_members WHERE uid = :uid AND is_disconnected = 0;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION['uid']);
            $stmt->execute();
            $pcode = $stmt->fetch(PDO::FETCH_ASSOC);

            return $pcode ? $pcode['pcode'] : false;
        }
        return false;
    }

    /**
    * Verifier l'existence d'un utilisateur
    * @param string $uid
    * @return bool 
    */
    function userExist($uid){
        $sql = "SELECT uid FROM users WHERE uid = :uid;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exist ? true : false;
    }

    /**
    * Verifier l'existence d'un code de partie
    * @param string $uid
    * @return bool 
    */
    function partyExist($pcode){
        $sql = "SELECT pcode FROM parties WHERE pcode = :pcode;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exist ? true : false;
    }

    function getAllParties(){
        $sql = "SELECT
        pcode,
        starting_date,
        ending_date,
        max_members,
        private,
        manager_uid,
        state FROM parties;";

        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $parties = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $parties;
    }

    /**
    * Ajouter un texte systeme a un chat de partie
    * @param string $uid
    * @param string $pcode
    * @param bool $is_narrative
    */
    function addPartySystemChat($content, $pcode, $is_narrative){
        if(partyExist($pcode)){
            $content = htmlspecialchars($content);
            if($is_narrative) $content = '"'.$content.'"';
            $sql = "INSERT INTO parties_chat_messages (pcode, content, type) VALUES (:pcode, :content, 1);";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();  
        }
    }

    /**
    * Recuperer les messages d'une partie
    * @return array 
    */
    function getAllPartyMessage($pcode) {  
        $sql = "SELECT
        id,
        content,
        uid,
        writing_date, 
        type,
        visibility FROM parties_chat_messages WHERE pcode = :pcode 
                                              ORDER BY writing_date ASC;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $messages;
    } 

    /**
    * Verifier si une IP est bannie
    * @param string $ip
    * @return bool 
    */
    function ipIsBan($ip){
        $sql = "SELECT uid FROM users_ipv4_address WHERE ipv4_address = :ipv4_address;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':ipv4_address', $ip);
        $stmt->execute();
        $uids = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($uids as $uid){
            if(userIsBan($uid['uid'])) return true;
        }

        return false;
    }

    /**
    * Verifier si un utilisateur est banni
    * @param string $uid
    * @return bool 
    */
    function userIsBan($uid){
        $sql = "SELECT bid FROM bans WHERE uid = :uid;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exist ? true : false;
    }

    /**
    * Verifier si un utilisateur est en partie
    * @param string $uid
    * @return bool 
    */
    function userIsInParty($uid){
        $sql = "SELECT uid FROM parties_members WHERE uid = :uid AND is_disconnected = 0;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exist ? true : false;
    }

    /**
    * Verifier si un utilisateur est en partie
    * @param string $uid
    * @return bool 
    */
    function partyIsOver($pcode){
        $sql = "SELECT ending_date FROM parties WHERE pcode = :pcode AND ending_date < CURRENT_TIMESTAMP();";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->execute();
        $party_is_over = $stmt->fetch(PDO::FETCH_ASSOC);

        return $party_is_over ? true : false;
    }

    /**
    * Recuperer la raison d'un bannissement
    * @param string $uid
    * @return string 
    * @return false Si l'utilisateur n'est pas banni
    */
    function getBanReason($uid){
        if(!userIsBan($uid)) return false;

        $sql = "SELECT reason FROM bans WHERE uid = :uid;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $ban = $stmt->fetch(PDO::FETCH_ASSOC);

        return $ban['reason'];
    }
    
    /**
    * Verifier l'existence d'un rang
    * @param string $rank_id
    * @return bool 
    */
    function rankExist($rank_id){
        $sql = "SELECT rank_id FROM ranks WHERE rank_id = :rank_id;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':rank_id', $rank_id);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exist ? true : false;
    }

    /**
    * Recuperer une relation
    * @param string $fuid
    * @param string $suid
    * @return int - 1: Demande en cours; 2: Amis; 0: Aucune relation; 
    */
    function getRelation($fuid, $suid){
        $sql = "SELECT fuid FROM users_relations WHERE (fuid = :fuid AND suid = :suid) OR (fuid = :suid AND suid = :fuid);";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':fuid', $fuid);
        $stmt->bindParam(':suid', $suid);
        $stmt->execute();
        $relation = $stmt->rowCount();

        return $relation;
    }

    /**
    * Recuperer les relations d'un utilisateur
    * @param string $fuid
    * @return array
    */
    function getRelations($fuid){
        $sql = "SELECT fuid, suid FROM users_relations WHERE fuid = :fuid OR suid = :fuid;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':fuid', $fuid);
        $stmt->execute();
        $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $relations;
    }

    /**
    * Verifier l'existence d'une permission
    * @param string $permission_name
    * @return bool 
    */
    function permissionExist($permission_name){
        $sql = "SELECT permission_name FROM permissions WHERE permission_name = :permission_name;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':permission_name', $permission_name);
        $stmt->execute();
        $exist = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exist ? true : false;
    }

    /**
    * Recuperer les informations d'un utilisateurs
    * @param string $uid
    * @return array
    * @return false - Si l'utilisateur n'existe pas 
    */
    function getUserInfos($uid){
        if(userExist($uid)){
            $sql = "SELECT
            uid,
            nick,
            mail,
            upwd,
            register_date,
            birthdate,
            pplayed,
            pwins,
            points,
            mail_verified,
            verified_account,
            htchange_pwd FROM users WHERE uid = :uid;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $user_infos = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user_infos;
        }
        return false;
    }

    /**
    * Recuperer les informations d'une partie
    * @param string $pcode
    * @return array
    * @return false - Si la partie n'existe pas 
    */
    function getPartyInfos($pcode){
        if(partyExist($pcode)){
            $sql = "SELECT
            pcode,
            starting_date,
            ending_date,
            max_members,
            private,
            manager_uid,
            plate,
            state,
            known_cards FROM parties WHERE pcode = :pcode;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();
            $party_infos = $stmt->fetch(PDO::FETCH_ASSOC);

            $party_infos['plate'] = json_decode($party_infos['plate'], true);

            return $party_infos;
        }
        return false;
    }

    /**
    * Recuperer le plateau d'une partie
    * @param string $pcode
    * @return array
    * @return false - Si la partie n'existe pas 
    */
    function getPartyPlate($pcode){
        if(partyExist($pcode)){
            $sql = "SELECT plate FROM parties WHERE pcode = :pcode;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();
            $party_plate = $stmt->fetch(PDO::FETCH_ASSOC);

            $party_plate['plate'] = json_decode($party_plate['plate'], true);

            return $party_plate;
        }
        return false;
    }

    /**
    * Recuperer le nombre de joueurs d'une partie
    * @param string $pcode
    * @return int
    * @return false - Si la partie n'existe pas 
    */
    function getPartyMembersCount($pcode){
        if(partyExist($pcode)){
            $sql = "SELECT uid FROM parties_members WHERE pcode = :pcode AND is_disconnected = 0;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();
            $members_counts = $stmt->rowCount();

            return $members_counts;
        }
        return false;
    }

    /**
    * Recuperer les joueurs d'une partie
    * @param string $pcode
    * @return array
    * @return false - Si la partie n'existe pas 
    */
    function getPartyMembers($pcode){
        if(partyExist($pcode)){
            $sql = "SELECT uid, is_disconnected FROM parties_members WHERE pcode = :pcode;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $members;
        }
        return false;
    }

    /**
    * Recuperer un joueur d'une partie
    * @param string $pcode
    * @param string $uid
    * @return array
    * @return false - Si la partie n'existe pas ou si l'utilisateur n'existe pas
    */
    function getPartyMemberInfos($pcode, $uid){
        if(partyExist($pcode) && userExist($uid) && userIsInParty($uid) && getUserParty($uid) == $pcode){
            $sql = "SELECT role, character_id FROM parties_members WHERE pcode = :pcode AND uid = :uid;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            return $member;
        }
        return false;
    }

    /**
    * Recupère les charactères disponibles en jeu
    * @return array
    */
    function getAllCharacters(){
        $sql = "SELECT id, name FROM parties_characters;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $characters;
    }

    /**
    * Recuperer les informations d'un rang
    * @param string $rank_id
    * @return array
    * @return false - Si le rang n'existe pas 
    */
    function getRankInfos($rank_id){
        if(rankExist($rank_id)){
            $sql = "SELECT
            rank_id,
            name,
            color,
            inherit_lvl,
            is_operator FROM ranks WHERE rank_id = :rank_id;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':rank_id', $rank_id);
            $stmt->execute();
            $rank_infos = $stmt->fetch(PDO::FETCH_ASSOC);

            return $rank_infos;
        }
        return false;
    }

    /**
    * Recuperer les informations d'un rang
    * @param string $rank_id
    * @return array
    * @return false - Si le rang n'existe pas 
    */
    function getRankPermissions($rank_id){
        if(rankExist($rank_id)){
            $rank_infos = getRankInfos($rank_id);
            if($rank_infos['is_operator']) return getAllPermissions();

            $sql = "SELECT permission_name FROM ranks_permissions WHERE rank_id = :rank_id;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':rank_id', $rank_id);
            $stmt->execute();
            $rank_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $rank_permissions;
        }
        return false;
    }

    function avatarExist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if ($code == 200) { $status = true; } 
        else { $status = false; }
        
        curl_close($ch);
        return $status;
    }

    /**
    * Recuperer l'avatar d'un utilisateurs
    * @param string $uid
    * @return string URL
    */
    function getUserAvatar($uid){
        if(userExist($uid)){
            if(strpos(getUserInfos($uid)['nick'], "ZXYBot") === 0){
                return "https://assets.roomsthegame.com/images/avatars/bot.png";
            }
            $potential_extensions = array("png", "jpeg", "jpg", "gif", "pjp", "jfif", "pjpeg");
            foreach($potential_extensions as $extension){
                $potential_avatar = "https://assets.roomsthegame.com/images/avatars/". $uid ."." . $extension;
                if(avatarExist($potential_avatar)) return $potential_avatar;
            }
        }
        return "https://assets.roomsthegame.com/images/avatars/base.png";
    }

    /**
    * Recuperer les rangs d'un utilisateurs
    * @param string $uid
    * @return array
    * @return false - Si l'utilisateur n'existe pas 
    */
    function getUserRanks($uid){
        if(userExist($uid)){
            $sql = "SELECT r.rank_id,
            r.name,
            r.color,
            r.inherit_lvl,
            r.is_operator  FROM users_ranks AS ur JOIN ranks AS r ON r.rank_id = ur.rank_id WHERE ur.uid = :uid ORDER BY r.inherit_lvl DESC;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $user_ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $user_ranks;
        }
        return false;
    }

    /**
    * Recuperer le premier rang d'un utilisateurs
    * @param string $uid
    * @return array
    * @return false - Si l'utilisateur n'existe pas 
    */
    function getUserFirstRank($uid){
        if(userExist($uid)){
            $sql = "SELECT ur.rank_id FROM users_ranks AS ur JOIN ranks AS r ON r.rank_id = ur.rank_id WHERE ur.uid = :uid ORDER BY r.inherit_lvl DESC LIMIT 1;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $user_first_rank = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return getRankInfos($user_first_rank['rank_id']);
        }
        return false;
    }

    /**
    * Recuperer les préférences d'un utilisateurs
    * @param string $uid
    * @return array
    * @return false - Si l'utilisateur n'existe pas 
    */
    function getUserPreferences($uid){
        if(userExist($uid)){
            $sql = "SELECT 	
            private_profile,
            dark_mode,
            lang,
            colorblindness_mode FROM users_preferences WHERE uid = :uid;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $user_preferences = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user_preferences;
        }
        return false;
    }

    /**
    * Verifier si un utilisateur a un rang
    * @param string $uid
    * @param string $rank_id
    * @return bool
    */
    function userHaveRank($uid, $rank_id){
        if(userExist($uid)){
            if(rankExist($rank_id)){
                $user_ranks = getUserRanks($uid);

                if(in_array($rank_id, $user_ranks)) return true;
            }
        }
        return false;
    }

    /**
    * Verifier si un utilisateur est opérateur
    * @param string $uid
    * @return bool
    */
    function userIsOperator($uid){
        if(userExist($uid)){
            $user_ranks = getUserRanks($uid);
            foreach($user_ranks as $rank){
                if(getRankInfos($rank['rank_id'])['is_operator']) return true;
            }
        }
        return false;
    }

    /**
    * Verifier si un rang a une permission
    * @param string $rank_id
    * @param string $permission_name
    * @return bool
    */
    function rankHavePermission($rank_id, $permission_name){
        if(!permissionExist($permission_name)){
            return false;
        }
        if(rankExist($rank_id)){
            $rank_infos = getRankInfos($rank_id);
            if($rank_infos['is_operator']) return true;
            
            $sql = "SELECT permission_name FROM ranks_permissions WHERE rank_id = :rank_id;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':rank_id', $rank_id);
            $stmt->execute();
            $rank_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($rank_permissions as $permission){
                if($permission['permission_name'] == $permission_name) return true;
            }
        }
        return false;
    }

    /**
    * Verifier si un utilisateur a une permission
    * @param string $uid
    * @param string $permission_name
    * @return bool
    */
    function userHavePermission($uid, $permission_name){
        if(userExist($uid)){
            $user_ranks = getUserRanks($uid);

            foreach($user_ranks as $rank){
                if(!rankExist($rank['rank_id'])) return;

                $rank_infos = getRankInfos($rank['rank_id']);

                if(rankHavePermission($rank['rank_id'], $permission_name)) return true;

                $inherit_level = $rank_infos['inherit_lvl'];

                $sql = "SELECT rank_id FROM ranks WHERE inherit_lvl < :inherit_lvl;";
                $stmt = $GLOBALS['db']->prepare($sql);
                $stmt->bindParam(':inherit_lvl', $inherit_level);
                $stmt->execute();
                $inherit_ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($inherit_ranks as $irank){
                    if(rankHavePermission($irank['rank_id'], $permission_name)) return true;
                }
            }
        }
        return false;
    }

    /**
    * Enregistrer l'IP de l'utilisateur
    * @return void
    */
    function registerUserIp(){
        if(userIsLogged()){
            $actual_user_ip = getLoggedUserIp();
            $actual_uid = getLoggedUserId();

            $sql = "SELECT uid FROM users_ipv4_address WHERE ipv4_address = :ipv4_address AND uid = :uid;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':uid', $actual_uid);
            $stmt->bindParam(':ipv4_address', $actual_user_ip);
            $stmt->execute();
            $ip_infos = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$ip_infos){
                $sql = "INSERT INTO users_ipv4_address (ipv4_address, uid) VALUES (:ipv4_address, :uid);";
                $stmt = $GLOBALS['db']->prepare($sql);
                $stmt->bindParam(':uid', $actual_uid);
                $stmt->bindParam(':ipv4_address', $actual_user_ip);
                $stmt->execute();
            }
        }
    }

     //----MAINTENANCE SYSTEM----//

     function checkForMaintenance(){
        $sql = "SELECT id FROM maintenances WHERE starting_date <= CURRENT_TIMESTAMP AND ending_date >= CURRENT_TIMESTAMP;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->execute();
        $maintenance_exist = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $maintenance_exist ? true : false;
    }

    /**
    * Recuperer les informations de la maintenance actuelle
    * @return array
    * @return false - S'il n'y a pas de maintenance en cours
    */
    function getMaintenanceInfos(){
        if(checkForMaintenance()){
            $sql = "SELECT reason, created_by, DATE_FORMAT(ending_date, '%d/%m/%Y %H:%i') AS ending_date, DATE_FORMAT(starting_date, '%d/%m/%Y %H:%i') AS starting_date FROM maintenances WHERE starting_date <= CURRENT_TIMESTAMP AND ending_date >= CURRENT_TIMESTAMP;";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->execute();
            $maintenance_infos = $stmt->fetch(PDO::FETCH_ASSOC);

            return $maintenance_infos;
        }

        return false;
    }

    function checkForMaintenanceRedirection(){
        if(checkForMaintenance()){
            if(userIsLogged()){
                $user_infos = getUserInfos(getLoggedUserId());
                
                if(userHavePermission($user_infos['uid'], "maintenance.bypass")) return;
    
                header("Location: https://errors.roomsthegame.com/maintenance");
                exit;
            } else {
                header("Location: https://errors.roomsthegame.com/maintenance");
                exit;
            }
        }
    }

    //--------------------------//

    registerUserIp();
    $actual_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if($actual_url != "https://errors.roomsthegame.com/maintenance"){
        checkForMaintenanceRedirection();
    }

    $url_who_need_login = array("https://panel.roomsthegame.com", "https://account.roomsthegame.com", "https://play.roomsthegame.com");

    foreach($url_who_need_login as $url){
        if(strpos($actual_url, $url) === 0){
            if(!userIsLogged()) {
                header("Location: https://auth.roomsthegame.com/login?to=" . $actual_url);
                exit;
            }
        }
    }

    if(strpos($actual_url, "https://panel.roomsthegame.com") === 0){    
        if(!userHavePermission(getLoggedUserId(), "admin_panel.view")) {
            header("Location: https://errors.roomsthegame.com/403");
            exit;
        }
    }

    if($actual_url != "https://errors.roomsthegame.com/banned"){
        if(userIsBan(getLoggedUserId()) || ipIsBan(getLoggedUserIp())){
            header("Location: https://errors.roomsthegame.com/banned");
            exit;
        }
    }

    if(strpos($actual_url, "https://auth.roomsthegame.com") !== 0){
        if(userIsLogged() && getUserInfos(getLoggedUserId())['mail_verified'] !== "1"){
            header("Location: https://auth.roomsthegame.com/verification?mail=" . getUserInfos(getLoggedUserId())['mail']);
            exit;
        }
    }

    if(strpos($actual_url, "https://www.roomsthegame.com") === 0){
        $dbstats = new PDO("mysql:host=" . DB_HOST . ";dbname=rooms_the_stats", DB_USER, DB_PASSWORD);
        $dbstats->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $sql = "INSERT INTO visitors (visit_at, value) VALUES (CURRENT_DATE(), 1);";
            $stmt = $dbstats->prepare($sql);
            $stmt->execute();
        } catch (exception $e) {
            $sql = "UPDATE visitors SET value = value+1 WHERE visit_at = CURRENT_DATE();";
            $stmt = $dbstats->prepare($sql);
            $stmt->execute();
        }
    }

    if(getLoggedUserId() && userIsInParty(getLoggedUserId())){
        if(partyIsOver(getUserParty(getLoggedUserId()))){
            $user_id = getLoggedUserId();
            $pcode = getUserParty(getLoggedUserId());
            $sql = "UPDATE parties_members SET is_disconnected = 1 WHERE pcode = :pcode AND uid = :uid;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uid', $user_id);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();
        }
    }
