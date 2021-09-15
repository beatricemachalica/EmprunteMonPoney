// Google geocode
window.onload = () => {
  let cityInput = document.getElementById("search_city");
  // get input city
  cityInput.addEventListener("change", geocode);
};

function geocode() {
  let city = document.querySelector("#search_city").value;
  // get the city name (value of input #search_city)
  // console.log(city);

  axios
    .get("https://maps.googleapis.com/maps/api/geocode/json?", {
      params: {
        address: city,
        key: "AIzaSyBtck3NP2KMzE3bOv6Z2rhl-HLtozb-UZ0",
      },
    })
    .then(function (response) {
      // full data response
      // console.log(response.data);

      // get latitude and longitude
      let lat = response.data.results[0].geometry.location.lat;
      let lng = response.data.results[0].geometry.location.lng;
      // console.log(lng);

      // set the right latitude and longitude in inputs type hidden
      document.querySelector("#lat").value = lat;
      document.querySelector("#lng").value = lng;
    })
    .catch(function (error) {
      console.log(error);
    });
}

// window.onload = () => {
//   const FilterForm = document.querySelector("#filters");
//   // get the filter form

//   // get all filters
//   document.querySelectorAll("#filters input").forEach((input) => {
//     input.addEventListener("change", () => {
//       // get clic on every input

//       // get form data
//       const DataForm = new FormData(FilterForm);

//       // queryString (create a new URL with those filters parameters)
//       const Params = new URLSearchParams();

//       DataForm.forEach((value, key) => {
//         Params.append(key, value);
//         // console.log(key, value);
//         // console.log(Params.toString());
//       });

//       // get the current page URL
//       const Url = new URL(window.location.href);

//       // AJAX request
//       // pathname = /posts/ + page
//       // params = all categories, prices, etc.
//       // ajax=1 param for PostController
//       fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
//         headers: {
//           "X-Requested-With": "XMLHttpRequest",
//         },
//       })
//         .then((response) => response.json())
//         .then((data) => {
//           // get the container "content"
//           const content = document.querySelector("#content");

//           // replace the content
//           content.innerHTML = data.content;

//           // URL update
//           history.pushState({}, null, Url.pathname + "?" + Params.toString());
//         })
//         .catch((e) => alert(e));
//       // catch potential error
//     });
//   });
// };
