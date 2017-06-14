var galleryCells = document.querySelectorAll('.gallery-cell');
if (galleryCells) {
  for (var i = 0 ; i < galleryCells.length ; i++) {
    if (galleryCells[i].hasAttribute("data-linkproto")) {
      galleryCells[i].addEventListener("click", function(e) {
        document.location.href = e.currentTarget.getAttribute("data-linkproto");
      });
    }
  }
}
