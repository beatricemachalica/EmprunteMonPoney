/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLElement} filter
 */
export default class Filter {
  /**
   * @param {HTMLElement|null} element
   */
  constructor(element) {
    if (element === null) {
      return;
    }
    this.pagination = element.querySelector(".js-posts-pagination");
    this.content = element.querySelector(".js-posts-content");
    this.sorting = element.querySelector(".js-posts-sorting");
    this.filter = element.querySelector(".js-posts-filter");
    this.bindEvents();
    // console.log("test ajax en construction");
  }

  /**
   * add behavior to different elements
   */
  bindEvents() {
    this.sorting.querySelectorAll("a").forEach((a) => {
      a.addEventListener("click", (e) => {
        e.preventDefault();
        this.loadUrl(a.getAttribute("href"));
      });
    });
  }

  async loadUrl(url) {
    const response = await fetch(url, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    if (response.status >= 200 && response.status < 300) {
      const data = await response.json();
      this.content.innerHTML = data.content;
    } else {
      console.error(response);
    }
  }
}
