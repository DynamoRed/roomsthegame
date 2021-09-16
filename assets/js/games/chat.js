const msgInput = document.getElementById("msg-input");
const carCount = document.getElementById("carCount");
let nbCar = 120;
let canSend = true;
let cooldown = 1000;

msgInput.addEventListener("keyup", event => {
  let diff = nbCar - msgInput.value.length;
  carCount.innerHTML = `${diff}/${nbCar}`;
  if (diff == 0) {
    carCount.style.color = "red";
  } else {
    carCount.style.color = "white";
  }
});

msgInput.addEventListener("keypress", event => {
  if (event.key === "Enter") {
    if (msgInput.value.trim().length > 0) {
      if (msgInput.value.trim().length <= 120) {
        sendMessage(msgInput.value);
      }
    }
    msgInput.value = "";
  }
});

function initChat() {
  setInterval(() => {
    getAllMessages();
  }, 1000);
}

function getAllMessages() {
  newAJAXBasicRequest(
    "GET",
    "https://assets.roomsthegame.com/php/messages/get_all",
    response => {
      let messageJson = JSON.parse(response);
      if (!messageJson) return;
      for (let message in messageJson) {
        if (document.getElementById(`msg${messageJson[message]["id"]}`)) continue;
        generateMessage(messageJson[message]);
      }
    }
  );
}

function sendMessage(content) {
  if (canSend) {
    const newMessageData = new FormData();
    newMessageData.set("content", content);
    newAJAXBasicRequest(
      "POST",
      "https://assets.roomsthegame.com/php/messages/send",
      response => {},
      newMessageData
    );
    canSend = false;
    setTimeout(() => {
      canSend = true;
    }, cooldown);
  } else {
    addNotification("warning", "Pas de spam !", 5);
  }
}

function generateMessage(messageData) {
  const chatContainer = document.getElementById("msg-list");

  const messageContainer = document.createElement("li");
  messageContainer.id = `msg${messageData["id"]}`;

  if (messageData["type"] == 1) {
    messageContainer.classList.add("narrator");

    const message = document.createElement("div");
    message.classList.add("msg");
    message.innerHTML = `${messageData["content"]}`;
    messageContainer.appendChild(message);
  } else {
    if (messageData["is_requester"]) {
      messageContainer.classList.add("li-you");
      messageData["author"] = "Vous";
    } else {
      messageContainer.classList.add("li-not-you");
    }
    const message = document.createElement("div");
    message.classList.add("msg");
    message.innerHTML = `
              <span class="username">${messageData["author"]}</span> 
              ${messageData["content"]}
              <span class="time">${messageData["writing_date"]}</span>
            `;
    messageContainer.appendChild(message);
  }

  chatContainer.appendChild(messageContainer);

  const scrollContainer = document.getElementsByClassName("chat")[0];
  scrollContainer.scrollTop = scrollContainer.scrollHeight;
}

setTimeout(() => {
  initChat();
}, 1000);
