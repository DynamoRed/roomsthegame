<?php 
    $GLOBALS['plate_cards'] = [
        'room_1' => [
            'type' =>'central_room',
            'name' => 'Salle Centrale',
            'description' => 'Salle dans laquelle tous les personnages commencent le jeu. 
            Seules les actions Regarder et Déplacer sont autorisées.
            Cette salle ne peut en aucun cas être déplacée et restera au centre du plateau pendant toute la partie.'
        ],
        'room_2' => [
            'type' =>'exit_room',
            'name' => '🎉 Salle de Sortie',
            'description' => 'Quand tous les prisonniers sont entrés dans cette salle, l\'un d\'eux doit la déplacer hors du complexe en utilisant l\'action Contrôle.'
        ],

        'room_3' => [
            'type' =>'empty_room',
            'name' => 'Salle Vide',
            'description' => 'Cette salle n\'a rien de particulier. Elle est simplement vide.'
        ], 
        'room_4' => [
            'type' =>'vision_room',
            'name' => '👀 Salle de Vision',
            'description' => 'Regardez secrètement n\'importe quelle salle du complexe.'
        ],
        'room_5' => [
            'type' =>'moving_room',
            'name' => '⚙️ Salle Mouvante',
            'description' => 'En entrant, échangez cette salle avec n\'importe quelle salle cachée du complexe. 
            La salle cachée reste cachée. Si toutes les salles sont déjà révélées, cette salle n\'a pas effet.'
        ],
        'room_6' => [
            'type' =>'tunnel_room',
            'name' => '🕳️ Salle Tunnel',
            'description' => 'Cette salle vous permet d\'emprunter un tunnel pour vous balader dans le complexe seulement si les deux salle tunnels sont rélevées.
            Si un joueur est poussé dans cette salle il est forcé d\'emprunter le tunnel.'
        ],
        'room_7' => [
            'type' =>'tunnel_room',
            'name' => '🕳️ Salle Tunnel',
            'description' => 'Cette salle vous permet d\'emprunter un tunnel pour vous balader dans le complexe seulement si les deux salle tunnels sont rélevées.
            Si un joueur est poussé dans cette salle il est forcé d\'emprunter le tunnel.'
        ],

        'room_8' => [
            'type' =>'jamming_room',
            'name' => '📡 Salle de Brouillage',
            'description' => 'Tant qu\'un personnage est dans cette salle, aucune information ne peut être échangée entre les joueurs.'
        ],
        'room_9' => [
            'type' =>'cold_room',
            'name' => '🥶 Salle Froide',
            'description' => 'Quand vous vous trouvez dans cette salle vous ne pouvez programmer qu\'une seule action pendant la phase de programmation.
            Un garde révélé qui commence son tour dans cette salle n\'exécutera aussi qu\'une seule action.'
        ],
        'room_10' => [
            'type' =>'cold_room',
            'name' => '🥶 Salle Froide',
            'description' => 'Quand vous vous trouvez dans cette salle vous ne pouvez programmer qu\'une seule action pendant la phase de programmation.
            Un garde révélé qui commence son tour dans cette salle n\'exécutera aussi qu\'une seule action.'
        ],
        'room_11' => [
            'type' =>'vortex_room',
            'name' => '🌀 Salle Vortex',
            'description' => 'Retournez a la case centrale.'
        ],
        'room_12' => [
            'type' =>'jail_room',
            'name' => '⛓️ Cellule de Prison',
            'description' => 'Vous ne pouvez quitter cette salle qu\'en vous déplaçant vers un personnage situé dans une salle adjacente à la vôtre OU en vous déplaçant vers la salle centrale, si elle est adjacente à la salle de prison quand vous voulez la quitter.'
        ],
        'room_13' => [
            'type' =>'mirrors_room',
            'name' => '😵 Salle des Mirroirs',
            'description' => 'Vos actions restantes sont annulées jusqu\'a la fin du tour.
            Vous pourrez programmer vos actions normalement a la prochaine phase de programmation.'
        ],
        'room_14' => [
            'type' =>'dark_room',
            'name' => '👁️ Salle Obscure',
            'description' => 'Tant que vous êtes dans cette salle, vous ne pouvez pas utiliser l\'action Regarder.'
        ],
        'room_15' => [
            'type' =>'dark_room',
            'name' => '👁️ Salle Obscure',
            'description' => 'Tant que vous êtes dans cette salle, vous ne pouvez pas utiliser l\'action Regarder.'
        ],

        'room_16' => [
            'type' =>'mortal_room',
            'name' => '💀 Salle Mortelle',
            'description' => 'Vous êtes immédiatement éliminé. Pas de chance.'
        ],
        'room_17' => [
            'type' =>'mortal_room',
            'name' => '💀 Salle Mortelle',
            'description' => 'Vous êtes immédiatement éliminé. Pas de chance.'
        ],
        'room_18' => [
            'type' =>'time_room',
            'name' => '⏲️ Salle du Temps',
            'description' => 'Si un personnage entre dans cette salle avant le dernier tour, le compte à rebours est réduit d\'un tour.
            Si un personnage entre dans cette salle lors du dernier tour, l\'effet de la salle est différent. 
            Ce personnage est immédiatement éliminé du jeu et le compte à rebours n\'est pas réduit.
            Dans les deux cas, la salle du temps est alors supprimée du jeu et remplacée par une salle vide.'
        ],
        'room_19' => [
            'type' =>'acid_room',
            'name' => '🧪 Bain d\'Acide',
            'description' => 'Dès que deux personnages sont dans cette salle, celui qui est entré en premier est éliminé par l\'arrivée du second.'
        ],
        'room_20' => [
            'type' =>'flooded_room',
            'name' => '🌊 Salle Inondee',
            'description' => 'Une fois révélée, cette salle se verrouille de l\'intérieur et devient inaccessible pour le reste de la partie.
            Si votre personnage est toujours dans cette salle a la fin du prochain tour vous vous noyez et êtes éliminé.'
        ],
        'room_21' => [
            'type' =>'flooded_room',
            'name' => '🌊 Salle Inondee',
            'description' => 'Une fois révélée, cette salle se verrouille de l\'intérieur et devient inaccessible pour le reste de la partie.
            Si votre personnage est toujours dans cette salle a la fin du prochain tour vous vous noyez et êtes éliminé.'
        ],
        'room_22' => [
            'type' =>'torment_room',
            'name' => '🔪 Salle de Torture',
            'description' => 'Vous devez quitter cette salle avant la fin de votre prochaine action ou vous êtes exécuté et éliminé.'
        ],
        'room_23' => [
            'type' =>'torment_room',
            'name' => '🔪 Salle de Torture',
            'description' => 'Vous devez quitter cette salle avant la fin de votre prochaine action ou vous êtes exécuté et éliminé.'
        ],
        'room_24' => [
            'type' =>'illusion_room',
            'name' => '💫 Salle Illusoire',
            'description' => 'Échangez immédiatement la Salle Illusoire avec n\'importe quelle salle cachée sur le plateau.
            Révélez la nouvelle salle, placez-y votre personnage et appliquez ses effets.
            Si toutes les salles sont déjà révélées, cette salle n\'a aucun effet.'
        ],
        'room_25' => [
            'type' =>'paranoia_room',
            'name' => '👽 Salle de Paranoia',
            'description' => 'Lorsque votre personnage entre dans cette salle, vos actions pour le prochain tour ainsi que pour le tour actuel (s\'il en reste) sont mélangées et choisies aléatoirement.'
        ],
    ];