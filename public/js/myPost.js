window.onload = () => {
  // delete a picture
  // get all links "delete"
  let links = document.querySelectorAll("[data-delete]");

  for (link of links) {
    // click on link
    link.addEventListener("click", function (e) {
      // prevent the link behavior

      e.preventDefault();

      // confirmation in case of user mistake
      if (confirm("Souhaitez-vous supprimer cette image ?")) {
        // fetch = ajax request with method DELETE (send URL json request with delete_token needed)

        fetch(this.getAttribute("href"), {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ _token: this.dataset.token }),
        })
          .then(
            // get JSON response
            (response) => response.json()
          )
          .then((data) => {
            // data array success or error
            if (data.success) {
              this.parentElement.remove();
            } else {
              alert(data.error);
            }
          })
          .catch((e) => alert(e));
      }
      // end if confirmation
    });
    // end function on click
  }

  // toggle button = activate post
  let activateButtons = document.querySelectorAll(".custom-control-input");
  // console.log(activateButtons);

  for (let button of activateButtons) {
    button.addEventListener("click", function () {
      let postId = this.dataset.id;
      // console.log("click!");
      fetch("/activate", {
        method: "UPDATE",
        headers: {
          Accept: "application/json, text/plain, */*",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ postId }),
      })
        .then(
          // get JSON response
          (response) => response.json()
        )
        .then((data) => {
          // console.log(data);
        })
        .catch((e) => alert(e));
    });
  }
};
