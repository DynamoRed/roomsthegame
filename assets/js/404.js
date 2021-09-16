let hoverToSnake = document.getElementsByClassName("easter-egg-icon")[0];

if(hoverToSnake){
    hoverToSnake.onclick = () => {
        let snakeFrame = document.createElement("iframe");
        snakeFrame.src = "https://assets.roomsthegame.com/snake/snake";
        snakeFrame.style.height = "100vh";
        snakeFrame.style.width = "100vh";
        let overlay = createOverlay(document.body);
        overlay.append(snakeFrame);
        snakeFrame.focus();
    }
}