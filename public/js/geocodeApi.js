// Google geocode

window.onload = () => {
  let cityInput = document.getElementById("post_city");
  cityInput.addEventListener("change", geocode);
};

function geocode() {
  let city = document.querySelector("#post_city").value;
  // console.log(city);

  axios
    .get("https://maps.googleapis.com/maps/api/geocode/json?", {
      params: {
        address: city,
        key: "AIzaSyBtck3NP2KMzE3bOv6Z2rhl-HLtozb-UZ0",
      },
    })
    .then(function (response) {
      // full response
      // console.log(response.data);

      // latitude and longitude
      let lat = response.data.results[0].geometry.location.lat;
      let lng = response.data.results[0].geometry.location.lng;
      // console.log(lng);

      // set the right latitude and longitude
      document.querySelector("#post_lat").value = lat;
      document.querySelector("#post_lng").value = lng;
    })
    .catch(function (error) {
      console.log(error);
    });
}
