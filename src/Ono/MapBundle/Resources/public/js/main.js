function setToWindowHeight(el){
  el.style.height = window.innerHeight+'px';
}

// Envoie la XHR
function callScript (scriptName, args){
	var xhr_object = null;

	// ### Construction de l’objet XMLHttpRequest selon le type de navigateur
	if(window.XMLHttpRequest){
    xhr_object = new XMLHttpRequest();
  }	else if(window.ActiveXObject) {
	  	 xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
                // XMLHttpRequest non supporté par le navigateur
	   	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
    		 return;
	}

	xhr_object.open("POST", scriptName, true);

	//  Définition du comportement à adopter sur le changement d’état de l’objet XMLHttpRequest
	xhr_object.onreadystatechange = function() {
	  if(this.readyState == 4 && this.status === 200) {
      mapGestion.updateQuestionFromJson(xhr_object.responseText);
    }

		return xhr_object.readyState;
	}
	xhr_object.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr_object.send("json="+JSON.stringify(args)+"&xhr=true");

}

///////////////////////////////////////////////////////
//
//          Capte Philter
//
///////////////////////////////////////////////////////

filter = {
  filterThemeActive : [],
  filterTab : {
    themes : [],
    age : null
  },

  sendModification:function(){
    callScript(config.rootPath+"update", filter.filterTab)
  },

  addClickEvent:function(el, rank){
    el.addEventListener("click", function(){
      if(el.className.match("active")){
        el.className = el.className.replace("active", "");
        filter.filterThemeActive[parseInt(el.getAttribute("data-id"))] = false
      } else {
        el.className+= " active";
        filter.filterThemeActive[parseInt(el.getAttribute("data-id"))] = true;
      }
      filter.updateFilterTheme();
    }, false)
  },
  clearFilterTab: function(){
    filter.filterTab.themes =  [];
  },
  updateFilterTheme:function(){
    filter.clearFilterTab();
    for(i=0; i<filter.filterThemeActive.length; i++){
      if(filter.filterThemeActive[i]){
        filter.filterTab.themes.push(i);
      }
    }
    filter.sendModification();
  },
  initEvent: function(){
    for(i=0; i<filter.themes.length; i++){
      filter.addClickEvent(filter.themes[i], i);
    }
    filter.age.addEventListener("change", function(){
      if(filter.ageControl.checked){
        filter.filterTab.age = this.value;
      }
      filter.sendModification();
    }, false)
    filter.ageControl.addEventListener("change", function(){
      if(this.checked){
        filter.filterTab.age = filter.age.value;
      } else {
        filter.filterTab.age = null;
      }
      filter.sendModification();
    }, false)
  },
  init: function(){
    filter.themes = document.getElementsByClassName("filter-theme");
    filter.age = document.getElementById("rangeAge");
    filter.ageControl = document.getElementById("rangeAgeActive");
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


sidebarGestion = {
  initEvent:function(){
    sidebarGestion.burger.addEventListener("click", function(){
      if(sidebarGestion.container.className.match("sidebar-open")){
        sidebarGestion.container.className = sidebarGestion.container.className.replace("sidebar-open", "sidebar-close")
      } else if(sidebarGestion.container.className.match("sidebar-close")){
        sidebarGestion.container.className = sidebarGestion.container.className.replace("sidebar-close", "sidebar-open")
      }
    }, false)
  },
  initSidebarSize: function(){
    setToWindowHeight(sidebarGestion.container);
    window.addEventListener("resize", function(){
      setToWindowHeight(sidebarGestion.container);
    }, false)
  },
  init:function(query){
    sidebarGestion.container = document.querySelector(query);
    sidebarGestion.burger = sidebarGestion.container.getElementsByClassName("burger-button")[0];
    sidebarGestion.initSidebarSize();
    sidebarGestion.initEvent();
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


burgerGestion.init("#sidebarLeft")
sidebarGestion.init("#sidebarRight")

displayToolDev.init();
filter.init();
