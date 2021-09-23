// 3 HTMLElement creation
let $addCollectionButton = $(
  '<button type="button" class="add_collection_link btn btn btn-secondary mt-2">Ajouter une activité</button>'
);
let $delCollectionButton = $(
  '<button type="button" class="del_collection_link btn btn btn-secondary mt-2">Supprimer</button>'
);

// the first li element (button "ajouter")
let $newLinkLi = $("<li></li>").append($addCollectionButton);

function generateDeleteButton() {
  var $btn = $delCollectionButton.clone();
  $btn.on("click", function () {
    // click event on "supprimer" button
    $(this).parent("li").remove();
    $collection.data("index", $collection.data("index") - 1);
  });
  return $btn;
}

// function which adds a new li field (depending on the entry_type of the collectionType) in the collection
function addCollectionForm($collection, $newLinkLi) {
  // content of the prototype data attribute which contains the HTML of a field
  var newForm = $collection.data("prototype");

  // the number of fields already present in the collection
  var index = $collection.data("index");

  // we replace the location provided for the id of a field by its index in the collection
  newForm = newForm.replace(/__name__/g, index);

  // we modify the data index of the collection by the new number of elements
  $collection.data("index", index + 1);

  // we build the li element with the display and the delete button
  var $newFormLi = $("<li></li>")
    .append(newForm)
    .append(generateDeleteButton());

  // we add the new li above the one which contains the "add" button
  $newLinkLi.before($newFormLi);
}

// rendering of the collection on page load
$(document).ready(function () {
  // we point to the complete list (the container of the collection)
  var $collection = $("ul#activities");

  // we add the "ajouter" button (at the end of the content)
  $collection.append($newLinkLi);

  // for each li already present in the collection (in the case of a modification)
  $(".activity").each(function () {
    // we generate and add a "supprimer" button
    $(this).append(generateDeleteButton());
  });

  // the data index of the collection is equal to the number of inputs inside the collection
  $collection.data("index", $collection.find(":input").length);

  $addCollectionButton.on("click", function (e) {
    // click on the "ajouter" button
    // if the collection does not yet have as many elements as the maximum allowed
    // if($collection.data('index') < $("input[maxNb]").val()){
    // we call the function which adds a new field
    addCollectionForm($collection, $newLinkLi);
    //  }
    //   else alert("Nb max atteint !")
  });
});
