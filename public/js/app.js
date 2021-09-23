// import Filter from "./modules/Filter.js";
// new Filter(document.querySelector(".js-posts"));

// Menu burger :
document.querySelector(".burger").addEventListener("click", function () {
  document.getElementById("mainMenu").classList.toggle("visible");
});

// "Go to Top" button
function scrolltotop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// Animation navbar
const navigation = document.getElementById("mainNav");
window.addEventListener("scroll", () => {
  if (window.scrollY > 20) {
    navigation.classList.add("nav-animation");
    // navigation.classList.remove("");
  } else {
    navigation.classList.remove("nav-animation");
  }
});

// Slider
var counter = 1;
setInterval(function () {
  document.getElementById("radio" + counter).checked = true;
  counter++;
  if (counter > 4) {
    counter = 1;
  }
}, 6000);
