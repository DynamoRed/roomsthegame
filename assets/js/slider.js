let maxRotate = 180;

let slider, content, prev, next;
let slides = [];
let styles = [];
let currentIndex = 0;

/*
 * Wrapping Array
 * Takes slices in an array, but wraps back around to the beginning when it reaches the end
 * Ex:
 * let wa = new WrappingArray([0, 1, 2, 3, 4])
 * wa.slice(0) // outputs [0, 1, 2, 3, 4]
 * wa.slice(3) // outputs [2, 3, 4, 0, 1]
 */
function WrappingArray(list) {
  this.list = Array.prototype.slice.call(list);
  Object.defineProperty(this, "length", {
    get: function () {
      return this.list.length;
    },
  });
}

WrappingArray.prototype.slice = function slice(start, end) {
  let l = this.length,
    s = start,
    e = end || l + s,
    res = [];
  while (s < e) {
    res.push(this.list[((s % l) + l) % l]);
    s++;
  }
  return res;
};

WrappingArray.prototype.map = function map() {
  return Array.prototype.map.apply(this.list, arguments);
};

function init() {
  slides = new WrappingArray(document.getElementsByClassName("slide"));
  slider = document.getElementsByClassName("slider")[0];
  content = document.getElementsByClassName("slider-content")[0];
  next = document.getElementsByClassName("next")[0];
  prev = document.getElementsByClassName("prev")[0];

  next.addEventListener("click", incSlides);
  prev.addEventListener("click", decSlides);

  sliderWidth = slider.getBoundingClientRect().width;
  slideWidth = slides.list[0].getBoundingClientRect().width;
  perspective = +window
    .getComputedStyle(content)
    .getPropertyValue("perspective")
    .replace(/px/, "");
  calculateStyles();
  sliderToIndex();
}

function calculateStyles() {
  let rotateInterval = maxRotate / slides.length;
  let translateInterval = (sliderWidth * 2) / slides.length;
  let center = Math.floor(slides.length / 2);
  let slideinfset = sliderWidth / 2 - slideWidth / 2;

  styles = slides.map(function (slide, idx) {
    let infset = ((idx + center) % slides.length) - center;
    let translateX = infset * translateInterval * 1.5 + slideinfset;
    let translateZ = -Math.abs((infset * perspective) / 2);
    return {
      rotate: -infset * rotateInterval,
      translateX: translateX,
      translateZ: translateZ,
      opacity: 1 - Math.abs(((2 * infset) / slides.length) * 0.75),
    };
  });
}

function sliderToIndex(idx) {
  currentIndex = idx || currentIndex;
  let els = slides.slice(currentIndex);
  els.forEach(function (el, i) {
    let style = styles[i];
    el.style.transform =
      `translate3d(${style.translateX}px, 0,${style.translateZ}px)`;
    el.style.opacity = style.opacity;
  });
}

const indicators = document.getElementsByClassName("indicator");
let activeIndic = 0;

function incSlides() {
  currentIndex++;
  if (currentIndex > 5) currentIndex = 0;
  sliderToIndex(currentIndex);
  indicators[activeIndic].classList.remove("active");
  activeIndic++;
  if (activeIndic > indicators.length - 1) activeIndic = 0;
  indicators[activeIndic].classList.add("active");
}

function decSlides() {
  currentIndex--;
  if (currentIndex < 0) currentIndex = 5;
  sliderToIndex(currentIndex);
  indicators[activeIndic].classList.remove("active");
  activeIndic--;
  if (activeIndic < 0) activeIndic = indicators.length - 1;
  indicators[activeIndic].classList.add("active");
}

document.addEventListener("DOMContentLoaded", init);
window.addEventListener('resize', init);

