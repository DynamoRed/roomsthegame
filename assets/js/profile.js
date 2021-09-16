profile = {};

let imageUpload = document.getElementById("avatar");
let uploadMsg = document.getElementById("inputActualFile");
if(imageUpload && uploadMsg){
    imageUpload.onchange = () => {
        let input = imageUpload.files[0];
        if (input) {
            uploadMsg.innerHTML = input.name;
        } else {
            uploadMsg.innerHTML = "";
        }
    };
}

let labelSwitchs = document.getElementsByClassName("input-switch");
for(let i = 0; i < labelSwitchs.length; i++){
    labelSwitchs[i].onchange = () => {
        labelSwitchs[i].classList.toggle("checked");
        profile.savePreference(labelSwitchs[i].children[0]);
        if(labelSwitchs[i].children[0].name == "dark_mode"){
            labelSwitchs[i].classList.contains("checked") ? document.body.classList.remove("lightmode") : document.body.classList.add("lightmode");
        }
    }
}

profile.savePreference = element => {
    var preferenceData = new FormData(element.form);

    let inputLabel = element.parentElement;
    if(!inputLabel) return;

    let dataValue = inputLabel.classList.contains("checked") ? 1 : 0;
    preferenceData.set(element.name, dataValue);

    newAJAXBasicRequest("POST", "https://assets.roomsthegame.com/php/users/set_preferences", (response) => {
        addNotification("success", "Changement enregistré !", 5);
    }, preferenceData);
}

let formInputs = document.getElementsByClassName("auto-reset-inputs");
for(let input in formInputs){
    if(formInputs[input].id == "birthdate"){
        profile.actualBirthDate = formInputs[input].value;
    }
    formInputs[input].oninput = () => {
        formInputs[input].value = formInputs[input].value.trim(); 
        formInputs[input].classList.remove('invalid');
    }
}

profile.sendFriendRequest = sUid => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/relations/send?suid=${sUid}`, (response) => {
        addNotification("success", "Demande Envoyée ! Nous rechargeons votre page...", 5);
        window.location.reload(true);
    });
}

profile.acceptFriendRequest = sUid => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/relations/send?suid=${sUid}`, (response) => {
        addNotification("success", "Ami ajouté ! Nous rechargeons votre page...", 5);
        window.location.reload(true);
    });
}

profile.denyFriendRequest = sUid => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/relations/remove?suid=${sUid}`, (response) => {
        addNotification("success", "Demande rejetée ! Nous rechargeons votre page...", 5);
        window.location.reload(true);
    });
}

profile.removeFriend = sUid  => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/relations/remove?suid=${sUid}`, (response) => {
        addNotification("success", "Ami retiré ! Nous rechargeons votre page...", 5);
        window.location.reload(true);
    });
}

profile.changeInfos = () => {
    let pseudoInput = document.getElementById("pseudo");
    let birthDateInput = document.getElementById("birthdate");
    let mailInput = document.getElementById("mailAddress");
    let newPasswordInput = document.getElementById("newPassword");
    let newPasswordConfirmInput = document.getElementById("newPasswordConfirm");
    let newAvatarInput = document.getElementById("avatar");
    let actualPasswordInput = document.getElementById("actualPassword");

    if(!pseudoInput) return;
    if(!birthDateInput) return;
    if(!mailInput) return;
    if(!newPasswordInput) return;
    if(!newPasswordConfirmInput) return;
    if(!newAvatarInput) return;
    if(!actualPasswordInput) return;

    let pseudoData = pseudoInput.value;
    let birthDateData = birthDateInput.value;
    let mailData = mailInput.value.trim();
    let newPasswordData = newPasswordInput.value.trim();
    let newPasswordConfirmData = newPasswordConfirmInput.value.trim();
    let avatarData = newAvatarInput.files;
    let actualPasswordData = actualPasswordInput.value;

    if (pseudoData.length <= 3 && pseudoData.length != 0) {
        pseudoInput.classList.add("invalid");
        addNotification("warning", "Votre Pseudonyme est trop court !", 5);
        return false;
    }

    if (pseudoData.length >= 32 && pseudoData.length != 0) {
        pseudoInput.classList.add("invalid");
        addNotification("warning", "Votre Pseudonyme est trop long !", 5);
        return false;
    }

    function validatePseudo(pseudo) {
        const pseudoRegex = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]+/;
        return pseudoRegex.test(pseudo.toLowerCase());
    }

    if(validatePseudo(pseudoData) && pseudoData.length != 0){
        addNotification("warning", "Votre pseudonyme ne peut contenir que des lettres et des chiffres !", 5);
        return false;
    }

    if (mailData.length > 150 && mailData.length != 0) {
        mailInput.classList.add("invalid");
        addNotification(
          "warning",
          "Votre adresse mail est trop longue !",
          5
        );
        return false;
      }

    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    if (!validateEmail(mailData) && mailData.length != 0) {
        mailInput.classList.add("invalid");
        addNotification(
            "warning",
            "Vous devez entrer une adresse mail valide !",
            5
        );
        return false;
    }

    if(newPasswordData.length <= 5 && newPasswordData.length != 0) {
        newPasswordInput.classList.add("invalid");
        addNotification("warning", "Votre mot de passe est trop court !", 5);
        return false;
    }

    if(newPasswordConfirmData.length == 0 && newPasswordData.length != 0) {
        newPasswordConfirmInput.classList.add("invalid");
        addNotification(
            "warning",
            "Vous devez confirmer votre mot de passe !",
            5
        );
        return false;
    }

    if(newPasswordData != newPasswordConfirmData && newPasswordData.length != 0) {
        newPasswordConfirmInput.classList.add("invalid");
        newPasswordInput.classList.add("invalid");
        addNotification(
            "warning",
            "Vos saisies de mots de passe ne correspondent pas !",
            5
        );
        return false;
    }

    if(actualPasswordData.length == 0) {
        actualPasswordInput.classList.add("invalid");
        addNotification(
            "warning",
            "Vous devez entrer votre mot de passe actuel !",
            5
        );
        return false;
    }

    if(pseudoData.length == 0 && mailData.length == 0 && newPasswordData.length == 0 && avatarData.length == 0 && birthDateData == profile.actualBirthDate) {
        addNotification(
            "alert",
            "Vous devez effectuer des changements pour cela !",
            5
        );
        return false;
    }

    let changeInfosForm = document.getElementById("changeInfosForm");
    
    if(!changeInfosForm) return;

    changeInfosForm.submit();
}