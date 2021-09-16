const burgerBtn = document.querySelector(".brg-btn");

const burgerWrap = document.querySelector(".brg-menu-wrap");

burgerBtn.addEventListener("click", burgerMenuToggle);

function burgerMenuToggle() {
  burgerWrap.classList.toggle("active");

  burgerBtn.classList.toggle("active");
}

window.addEventListener("resize", () => {
  const width = window.innerWidth;

  if (width > 576) {
    burgerBtn.classList.remove("active");

    burgerWrap.classList.remove("active");
  }
});
