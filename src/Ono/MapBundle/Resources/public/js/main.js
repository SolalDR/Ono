function setToWindowHeight(el){
  el.style.height = window.innerHeight+'px';
}


burgerGestion = {
  initEvent:function(){
    burgerGestion.burger.addEventListener("click", function(){
      if(burgerGestion.container.className.match("sidebar-open")){
        burgerGestion.container.className = burgerGestion.container.className.replace("sidebar-open", "sidebar-close")
      } else if(burgerGestion.container.className.match("sidebar-close")){
        burgerGestion.container.className = burgerGestion.container.className.replace("sidebar-close", "sidebar-open")
      }
    }, false)
  },
  initSidebarSize: function(){
    setToWindowHeight(burgerGestion.container);
    window.addEventListener("resize", function(){
      setToWindowHeight(burgerGestion.container);
    }, false)
  },
  init:function(query){
    burgerGestion.container = document.querySelector(query);
    burgerGestion.burger = burgerGestion.container.getElementsByClassName("burger-button")[0];
    burgerGestion.initSidebarSize();
    burgerGestion.initEvent();
  }
}

burgerGestion.init("#sidebar")
