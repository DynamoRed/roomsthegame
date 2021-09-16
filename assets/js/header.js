window.addEventListener("scroll", () => {
    var scroll = window.scrollY;
    let header = document.getElementById("header");
    if(scroll > window.innerHeight*0.5 && scroll < window.innerHeight*3){
        header.className = "hdr scrolled";
    } else if(header.classList.contains("scrolled")) {
        header.className = "hdr unscrolled";
    }
});