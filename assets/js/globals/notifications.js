function addNotification(type, content, duration){
    let notificationsContainer = document.getElementById("notifications");

    type = type.toLowerCase();

    if(type != "info" && type != "success" && type !="warning" && type != "alert") return;
    if(content.length <= 0) return;

    if(!notificationsContainer){
        notificationsContainer = document.createElement("div");
        notificationsContainer.classList.add("notifications");
        notificationsContainer.id = "notifications";

        document.body.prepend(notificationsContainer);
    }

    if(notificationsContainer.children.length > 2){
        notificationsContainer.children[0].classList.add("hidden");
        setTimeout(() => {
            notificationsContainer.removeChild(notificationsContainer.children[0]);
        }, 300);
    }

    let newNotificationIcon = document.createElement("img");
    newNotificationIcon.src = `https://assets.roomsthegame.com/images/icons/${type}.svg`
    newNotificationIcon.class = "notification-icon";

    let newNotificationContent = document.createElement("span");
    newNotificationContent.innerHTML = content;

    let newNotification = document.createElement("div");
    newNotification.classList.add("notification");
    newNotification.classList.add(type);
    newNotification.append(newNotificationIcon);
    newNotification.append(newNotificationContent);

    notificationsContainer.append(newNotification);

    setTimeout(() => {
        newNotification.classList.add("hidden");
    }, duration*1000);

    setTimeout(() => {
        if(newNotification.parentElement == notificationsContainer) notificationsContainer.removeChild(newNotification);
    }, duration*1000+300);
    
}