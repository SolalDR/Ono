function setToWindowHeight(el){
  el.style.height = window.innerHeight+'px';
}

///////////////////////////////////////////////////////
//
//          Capte Philter
//
///////////////////////////////////////////////////////

filter = {
  filterActive : [],
  addClickEvent:function(el, rank){
    el.addEventListener("click", function(){
      if(el.className.match("active")){
        el.className = el.className.replace("active", "");
      } else {
        el.className+= " active";
      }
    }, false)
  },
  initEvent: function(){
    for(i=0; i<filter.themes.length; i++){
      filter.addClickEvent(filter.themes[i], i);
    }
  },
  init: function(){
    filter.themes = document.getElementsByClassName("filter-theme");
    filter.initEvent();
  }
}



///////////////////////////////////////////////////////
//
//          Gestion du menu burger
//
///////////////////////////////////////////////////////

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

///////////////////////////////////////////////////////
//
//          Afficher les outils de développement
//
///////////////////////////////////////////////////////

displayToolDev = {
  button : document.getElementById("display-dev"),
  el : document.getElementsByClassName("devEnv")[0],
  init:function(){
    displayToolDev.button.addEventListener("click", function(){
      if(displayToolDev.el.className.match("visible")){
        displayToolDev.el.className = displayToolDev.el.className.replace("true-visible", "true-hidden");
      } else {
        displayToolDev.el.className = displayToolDev.el.className.replace("true-hidden", "true-visible");

      }
    }, false)
  }
}


burgerGestion.init("#sidebar")
displayToolDev.init();
filter.init();
