// Menu burger
document.querySelector(".burger").addEventListener("click", function () {
  document.querySelector("nav").classList.toggle("visible");
});

// fonctionnement :
// dans le document, je recherche le selecteur avec la class burger ; puis écouter les événements clic
// lorsqu'il "entend" le clic, il execute une fonction
// dans le doc, on recherche le selecteur "nav", on regarde sa liste de classe, et on lui demande
// d'alterner entre ajouter/supprimer la classe "visible"
// toggle = alterner un état vers un autre

// "ON / OFF" video button
// var video = document.getElementById("myVideo");
// Get the button
// var btn = document.getElementById("myBtn");
// Pause and play the video, and change the button text
// function myFunction() {
//   if (video.paused) {
//     video.play();
//     btn.innerHTML = "Pause";
//   } else {
//     video.pause();
//     btn.innerHTML = "Play";
//   }
// }

// "Go to Top" button
function scrolltotop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// Animation navbar
const navigation = document.getElementById("mainNav");
window.addEventListener("scroll", () => {
  if (window.scrollY > 50) {
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
