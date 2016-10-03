function setToWindowHeight(el){
  el.style.height = window.innerHeight+'px';
}

elHeight = {
  init:function(query){
    elHeight.el = document.querySelector(query);
    setToWindowHeight(elHeight.el);
    window.addEventListener("resize", function(){
      setToWindowHeight(elHeight.el);
    }, false)
  }
}
elHeight.init("#menu");
