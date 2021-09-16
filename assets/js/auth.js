auth = {};
auth.signIn = {};
auth.login = {};
auth.signIn.vc = {};
auth.captcha = {};

auth.signIn.actualStep = 0;
auth.captcha.selectedImage = undefined;

let formInputs = document.getElementsByClassName("auto-reset-inputs");
for(let input in formInputs){
  input.oninput = () => { 
    input.value = input.value.trim(); 
    input.classList.remove('invalid');
    }
}

document.onkeydown = evt => {
  var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
  if(keyCode == 13){
    let authPage = document.getElementById("authPageName");
    if(!authPage) return;

    if(authPage.value == "signin") auth.signIn.nextStep(1);
    if(authPage.value == "login") auth.login.validateForm();
    if(authPage.value == "verification") auth.signIn.vc.validateCode();
  }

  if(keyCode == 8){
    let actualInput = document.activeElement;
    if(actualInput){
      if(actualInput.id.startsWith("authVC")){
        if(actualInput.value.trim().length != 0) return;
        let inputCompleteId = actualInput.id;
        let inputId = inputCompleteId.replace("authVC", '');
        
        evt.preventDefault();

        let previousInput = document.getElementById(`authVC${inputId-1}`);
        if(!previousInput) return;
        previousInput.focus();
      }
    }
  }
}

function randomSort(a, b) {  
  return 0.5 - Math.random();
}  

let captchaImages = document.getElementsByClassName("captcha-img-selector");
let captchaRotations = ['-90deg', '180deg', '90deg', '0deg', '45deg', '-45deg'];
var captchaRotationsSorted = captchaRotations.sort(randomSort);  

for(let i = 0; i < captchaImages.length; i++){
  captchaImages[i].childNodes[0].style.transform = `rotate(${captchaRotationsSorted[i]})`;
  captchaImages[i].setAttribute("rotation", captchaRotationsSorted[i]);
  captchaImages[i].onclick = event => {
    captchaImages[i].parentElement.classList.remove("invalid");
    event.preventDefault();
    captchaImages[i].classList.add("selected");
    auth.captcha.selectedImage = i;
    for(let y = 0; y < captchaImages.length; y++){
      if(y != i){
        captchaImages[y].classList.remove("selected");
      }
    }
  }
}

auth.signIn.captchaIsComplete = () => {
  let captchaImages = document.getElementsByClassName("captcha-img-selector");

  for(let i = 0; i < captchaImages.length; i++){
    if(captchaImages[i].classList.contains("selected") && captchaImages[i].getAttribute("rotation") == "0deg") return i;
  }

  return false;
}

auth.signIn.vc.disableStep = () => {
  let codeInputs = document.getElementsByClassName("double-auth-input");
  if(codeInputs.length == 0) return;

  for(let input in codeInputs){
    input.classList.add('disabled');
    input.setAttribute("disabled", true);
  }

  document.getElementById("authVCSubmit").classList.add("disabled");
  document.getElementById("authVCSubmit").onclick = () => {return false;}
}

auth.signIn.vc.validateCode = () => {
  let codeInputs = document.getElementsByClassName("double-auth-input");
  if(codeInputs.length == 0) return;

  let finalCode = "";

  for(let i = 1; i < codeInputs.length+1; i++){
    let codeInput = document.getElementById(`authVC${i}`);
    if(!codeInput) continue;
    if(codeInput.value.trim().length == 0){
      codeInput.classList.add("invalid");
      addNotification("warning", "Ce champs doit contenir un chiffre correct !", 5);
      break;
    }

    finalCode += codeInput.value.trim();
  }

  if(finalCode.length == 6){
    document.getElementById("finalVC").value = finalCode;
    document.getElementById("signInForm").submit();
  }
}

auth.signIn.vc.validateInput = inputCode => {
  let input = document.getElementById(`authVC${inputCode}`);
  if(!input) return;

  input.classList.remove("invalid");

  if(isNaN(input.value.trim())){ input.value = ''; return; }

  if(input.value.trim().length != 1) return;

  let nextInput = document.getElementById(`authVC${inputCode+1}`);
  if(!nextInput) return;
  nextInput.focus();
}

auth.signIn.showStep = step => {
  let stepsTabs = document.getElementsByClassName("auth-step");
  if(stepsTabs.length == 0) return;
  stepsTabs[step].style.display = "flex";
};

auth.signIn.showStep(auth.signIn.actualStep);

auth.signIn.nextStep = step => {
  let stepsTabs = document.getElementsByClassName("auth-step");
  if(stepsTabs.length == 0) return;
  if (step == 1 && !auth.signIn.validateForm()) return false;

  stepsTabs[auth.signIn.actualStep].style.display = "none";
  auth.signIn.actualStep = auth.signIn.actualStep + step;

  if (auth.signIn.actualStep >= stepsTabs.length) {
    document.getElementById("signInForm").submit();
    return false;
  }

  auth.signIn.showStep(auth.signIn.actualStep);
};

auth.signIn.validateForm = () =>  {
  if (auth.signIn.actualStep == 0) {
    const pseudoInput = document.getElementById("authPseudo");
    const mailInput = document.getElementById("authMail");
    const mailConfirmInput = document.getElementById("authMailConfirm");

    if(!pseudoInput) return;
    if(!mailInput) return;
    if(!mailConfirmInput) return;

    let pseudoData = pseudoInput.value;
    let mailData = mailInput.value.trim();
    let mailConfirmData = mailConfirmInput.value.trim();

    pseudoInput.value = pseudoData;
    mailInput.value = mailData;
    mailConfirmInput.value = mailConfirmData;

    if (pseudoData.length <= 3) {
      pseudoInput.classList.add("invalid");
      addNotification("warning", "Votre Pseudonyme est trop court !", 5);
      return false;
    }

    if (pseudoData.length >= 32) {
      pseudoInput.classList.add("invalid");
      addNotification("warning", "Votre Pseudonyme est trop long !", 5);
      return false;
    }

    function validatePseudo(pseudo) {
      const pseudoRegex = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]+/;
      return pseudoRegex.test(pseudo.toLowerCase());
    }

    if(validatePseudo(pseudoData)){
      addNotification(
        "warning",
        "Votre pseudonyme ne peut contenir que des lettres et des chiffres !",
        5
      );
      return false;
    }

    if (mailData.length == 0) {
      mailInput.classList.add("invalid");
      addNotification(
        "warning",
        "Vous devez renseigner une adresse mail !",
        5
      );
      return false;
    }

    if (mailData.length > 150) {
      mailInput.classList.add("invalid");
      addNotification(
        "warning",
        "Votre adresse mail est trop longue !",
        5
      );
      return false;
    }

    function validateEmail(email) {
      const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return emailRegex.test(email.toLowerCase());
    }

    if (!validateEmail(mailData)) {
      mailInput.classList.add("invalid");
      addNotification(
        "warning",
        "Vous devez entrer une adresse mail valide !",
        5
      );
      return false;
    }

    if (mailConfirmData.length == 0) {
      mailConfirmInput.classList.add("invalid");
      addNotification(
        "warning",
        "Vous devez confirmer votre adresse mail !",
        5
      );
      return false;
    }

    if (mailConfirmData != mailData) {
      mailInput.classList.add("invalid");
      mailConfirmInput.classList.add("invalid");
      addNotification(
        "warning",
        "Vos saisies d'adresses mail ne correspondent pas !",
        5
      );
      return false;
    }
  } else if (auth.signIn.actualStep == 1) {
    const passwordInput = document.getElementById("authPassword");
    const passwordConfirmInput = document.getElementById("authPasswordConfirm");
    const captcha = document.getElementById("captcha");

    if(!passwordInput) return;
    if(!passwordConfirmInput) return;
    if(!captcha) return;

    let passwordData = passwordInput.value;
    let passwordConfirmData = passwordConfirmInput.value;

    if (passwordData.length <= 5) {
      passwordInput.classList.add("invalid");
      addNotification("warning", "Votre mot de passe est trop court !", 5);
      return false;
    }

    if (passwordConfirmData.length == 0) {
      passwordConfirmInput.classList.add("invalid");
      addNotification(
        "warning",
        "Vous devez confirmer votre mot de passe !",
        5
      );
      return false;
    }

    if (passwordData != passwordConfirmData) {
      passwordConfirmInput.classList.add("invalid");
      passwordInput.classList.add("invalid");
      addNotification(
        "warning",
        "Vos saisies de mots de passe ne correspondent pas !",
        5
      );
      return false;
    }

    if (!auth.signIn.captchaIsComplete()) {
      captcha.classList.add("invalid");
      addNotification(
        "alert",
        "Vous n'avez pas complété correctement notre captcha !",
        5
      );
      return false;
    }
  }

  return true;
};

auth.login.validateForm = () =>  {
  const pseudoOrMailInput = document.getElementById("authPseudoOrMail");
  const passwordInput = document.getElementById("authPassword");

  if(!pseudoOrMailInput) return;
  if(!passwordInput) return;

  let pseudoOrMailData = pseudoOrMailInput.value.trim();
  let passwordData = passwordInput.value.trim();

  pseudoOrMailInput.value = pseudoOrMailData;
  passwordInput.value = passwordData;

  if (pseudoOrMailData.length <= 3) {
    pseudoOrMailInput.classList.add("invalid");
    addNotification(
      "warning",
      "Vous devez renseigner une adresse mail ou un pseudo !",
      5
    );
    return false;
  }

  if (passwordData.length == 0) {
    passwordInput.classList.add("invalid");
    addNotification("warning", "Vous devez renseigner un mot de passe !", 5);
    return false;
  }

  document.getElementById("loginForm").submit();
  return true;
};
