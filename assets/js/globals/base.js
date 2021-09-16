function newAJAXBasicRequest(method, to, onsuccess, data){
    let req = new XMLHttpRequest();
    req.open(method, to, true);
    req.withCredentials = true;
    req.onreadystatechange = function(evt) {
        if (this.status != 200) {
            evt.preventDefault();
            console.warn(`Une erreur est survenue ! (${this.readyState}/${this.status})`);
            console.error(this.response);
            addNotification("alert", "Une erreur est survenue ! Veuillez contacter un Administrateur.", 5);
            return false;
        }

        if(this.readyState === 4) onsuccess(this.response);
    }
    req.send(data || undefined);
    return req;
}

document.onkeydown = evt => {
    var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
    if (keyCode == 27) {
        removeOverlay();
    }
};

function createOverlay(element) {
    let overlay = document.createElement("div");
    overlay.classList.add("overlay");
    overlay.id = "overlay";

    element.append(overlay);

    return overlay;
};

function removeOverlay() {
    let overlay = document.getElementsByClassName("overlay");
    if (!overlay) return;
    for(let i = 0; i < overlay.length; i++) overlay[i].remove();
};