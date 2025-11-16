/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};

var productNavSlider = new Swiper(".product-nav-slider", {
  loop: false,
  spaceBetween: 10,
  slidesPerView: 4,
  freeMode: true,
  watchSlidesProgress: true
});
var productThubnailSlider = new Swiper(".product-thumbnail-slider", {
  loop: false,
  spaceBetween: 24,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev"
  },
  thumbs: {
    swiper: productNavSlider
  }
});
var lightbox=GLightbox({selector:".image-popup",title:!1});
/******/ })()
;