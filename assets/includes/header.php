<?php if(isset($current_notification)){
    echo '<script>addNotification("'. $current_notification['type'] .'", "'. $current_notification['content'] .'", '. $current_notification['duration'] .');</script>';
} 

$actual_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>
<header>
  <div class="hdr" id="header">
    <div class="hdr-left">
      <div class="brg-ctnr">
        <div class="brg-btn">
          <div class="brg-line"></div>
        </div>
      </div>
      <a href="https://www.roomsthegame.com"><img src="https://assets.roomsthegame.com/images/logo.png" class="small-logo-text"></a>
      <div class="hdr-nav">
        <a class="hdr-link" href="https://www.roomsthegame.com">Accueil</a>
        <a class="hdr-link" href="https://www.roomsthegame.com/news">Actualités</a>
        <a class="hdr-link" href="https://play.roomsthegame.com/ranking">Classement</a>
        <a class="hdr-link" href="https://support.roomsthegame.com">Support</a>
      </div>
    </div>
    <div class="hdr-right">
      <?php if (userIsLogged()) { ?>
      <div class="dropmenu">
        <a class="hdr-link profile dropbutton">
          <img src="https://assets.roomsthegame.com/images/icons/expand_more.svg" class="dropmore">
          <span class="hdr-profile-name"><?= getUserInfos(getLoggedUserId())['nick'] ?></span>
          <img src="<?= getUserAvatar(getLoggedUserId()) ?>" class="hdr-profile-avatar">
        </a>
        <div class="dropcontent">
          <a href="https://account.roomsthegame.com" class="hdr-link">Profil</a>
          <a href="https://auth.roomsthegame.com/sign/out" class="hdr-link">Se Deconnecter</a>
        </div>
      </div>
      <?php } else { ?>
        <a class="hdr-link" href="https://auth.roomsthegame.com/login?to=<?= $actual_url ?>">Se connecter</a>
      <?php } ?>
      <a class="btn-primary" href="https://play.roomsthegame.com/hub" id="hdrPlayButton">
        <img src="https://assets.roomsthegame.com/images/icons/play_arrow.svg">
        Jouer
      </a>      
      <?php if (userIsLogged()) { 
        if(userHavePermission(getLoggedUserId(), "admin_panel.view")){ ?>
        <a class="btn-primary icon-only btn-to-admin" href="https://panel.roomsthegame.com/" title="Panel d'Administration">
          <img src="https://assets.roomsthegame.com/images/icons/admin_panel.svg">
        </a>
      <?php }} ?>
    </div>
  </div>
</header>

<div class="brg-menu-wrap">
  <div class="brg-nav">
    <a class="hdr-link" href="https://www.roomsthegame.com">Accueil</a>
    <a class="hdr-link" href="https://www.roomsthegame.com/news">Actualités</a>
    <a class="hdr-link" href="https://play.roomsthegame.com/ranking">Classement</a>
    <a class="hdr-link" href="https://support.roomsthegame.com">Support</a>
  </div>
</div>