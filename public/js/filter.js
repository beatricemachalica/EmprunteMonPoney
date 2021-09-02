window.onload = () => {
  const FilterForm = document.querySelector("#filters");
  // get the filter form

  // get all filters
  document.querySelectorAll("#filters input").forEach((input) => {
    input.addEventListener("change", () => {
      // get clic on every input

      // get form data
      const DataForm = new FormData(FilterForm);

      // queryString (create a new URL with those filters parameters)
      const Params = new URLSearchParams();

      DataForm.forEach((value, key) => {
        Params.append(key, value);
        // console.log(key, value);
        // console.log(Params.toString());
      });

      // get the current page URL
      const Url = new URL(window.location.href);

      // AJAX request
      // pathname = /posts/ + page
      // params = all categories, prices, etc.
      // ajax=1 param for PostController
      fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          // get the container "content"
          const content = document.querySelector("#content");

          // replace the content
          content.innerHTML = data.content;

          // URL update
          history.pushState({}, null, Url.pathname + "?" + Params.toString());
        })
        .catch((e) => alert(e));
      // catch potential error
    });
  });
};