/////////////////////////////////////////////
//
//         FONCTION GÉNÉRIQUE
//
/////////////////////////////////////////////

//Met un élément à la taille de la fenêtre
function setToWindowHeight(el){
  el.style.height = window.innerHeight+'px';
}

//Test si un objet est un élément HTML
function isElement(obj) {
  try {
    //Using W3 DOM2 (works for FF, Opera and Chrom)
    return obj instanceof HTMLElement;
  }
  catch(e){
    //Browsers not supporting W3 DOM2 don't have HTMLElement and
    //an exception is thrown and we end up here. Testing some
    //properties that all elements have. (works on IE7)
    return (typeof obj==="object") &&
      (obj.nodeType===1) && (typeof obj.style === "object") &&
      (typeof obj.ownerDocument ==="object");
  }
}


// Envoie la XHR
function callScript (scriptName, args, form, isJsonEncode){
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
  console.log(scriptName);
	xhr_object.open("POST", scriptName, true);

	//  Définition du comportement à adopter sur le changement d’état de l’objet XMLHttpRequest
	xhr_object.onreadystatechange = function() {
	  if(this.readyState == 4 && this.status === 200) {
			// alert(xhr_object.responseText); // DEBUG MODE
			// document.write(xhr_object.responseText);
      if(form) {
        if(form.getAttribute("data-action")) {
          XHRResponse.init(xhr_object.responseText, form, args);
        }
      } else {
        // alert(xhr_object.responseText);
        XHRResponse.response = xhr_object.responseText;
      }
      sendMAJ(xhr_object.responseText);
    }
		return xhr_object.readyState;
	}
	xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  if(isJsonEncode){
    console.log("Is Json");
    xhr_object.send("json="+JSON.stringify(args));
  } else {
    var str = "";
    for(arg in args){
      str+=arg+"="+args[arg]+"&"
    }
    str = str.replace(/&$/, "&isXHR=1");
    // console.log(str);

    xhr_object.send(str);
  }
}

// Renvoi le temps écoulé depuis une date
function getAgeResponse(date){
  date*=1000;
  var actualDate = Date.now();
  var diff = actualDate-date;
  //Miliseconde

  diff = Math.floor(diff/1000/60/60/24/365);
  return diff;
}

//Crée un élément HTML de manière aisé
function createElement(type, classname, attributes){
  var el = document.createElement(type);
  if(classname){
    el.className+=classname;
  }
  for(attribute in attributes){
    el.setAttribute(attribute, attributes[attribute]);
  }
  return el;
}

///////////////////////////////////////////////////////
//
//          XHR POST AUTO
//
///////////////////////////////////////////////////////


XHRformAuto = {
 forms : document.querySelectorAll("form[data-xhrpost='true']"),
 main : function(form){
   form.onsubmit = function(e){
     e.preventDefault;
     return false;
   }
   var submitButton = form.querySelectorAll("input[type='submit'], button[type='submit']")[0];
   submitButton.addEventListener("click", function(e){
     var arg = {};
     var inputs = form.getElementsByTagName("input");
     var textarea = form.getElementsByTagName("textarea");
     var select = form.getElementsByTagName("select");

     for(i=0; i<select.length; i++){
       if(select[i]){
         arg[select[i].name]= select[i].value;
       }
     }     for(i=0; i<textarea.length; i++){
       if(textarea[i]){
         arg[textarea[i].name]= textarea[i].value;
       }
     }
     for(i=0; i<inputs.length; i++){
       if(inputs[i]){
         if(inputs[i].value && inputs[i].name){
           arg[inputs[i].name] = inputs[i].value;
         }
       }
     }
    //  console.log(this.action, );
    if(form.getAttribute("data-json")){
      var test = callScript(form.action, arg, form,  true);
    } else {
      var test = callScript(form.action, arg, form);
    }
   }, false)
 },
 realiseAction(txt, form, content){
   function removeForm(form){
     var form = form.parentNode;
     var parent = form.parentNode;
     parent.removeChild(form);
    //  console.log(parent);
     console.log("Supprimer formulaire");
   }
   var action = form.getAttribute("data-action");
   if(action){
     switch(action) {
       case "remove" :
         removeForm(form);
         break;
       default :
        break;
     }
   }
 },
 init : function(){
   forms = XHRformAuto.forms;
   for(i=0; i<forms.length; i++){
     XHRformAuto.main(forms[i]);
   }
 }
}

///////////////////////////////////////////////////////
//
//                  XHR LIKE OBJECT
//
//   gère les test et requête xhr pour like & dislike
//
///////////////////////////////////////////////////////

likeManage = {

}

///////////////////////////////////////////////////////
//
//          Capte filter
//
///////////////////////////////////////////////////////

filter = {
  filterThemeActive : [],
  filterTab : {
    themes : [],
    age : null
  },

  updateQuestionCallscript: function(scriptName, args){
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
  },

  sendModification:function(){
    filter.updateQuestionCallscript(config.rootPath+"update", filter.filterTab)
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
    if(filter.age){
      filter.age.addEventListener("change", function(){
        if(filter.ageControl.checked){
          filter.filterTab.age = this.value;
        }
        filter.sendModification();
      }, false)
    }
    if(filter.ageControl){
      filter.ageControl.addEventListener("change", function(){
        if(this.checked){
          filter.filterTab.age = filter.age.value;
        } else {
          filter.filterTab.age = null;
        }
        filter.sendModification();
      }, false)
    }
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

///////////////////////////////////////////////////////
//
//          DISPLAY THEME PANNEL
//
///////////////////////////////////////////////////////


themeGestion = {
  initEvent:function(){
    themeGestion.closebutton.addEventListener("click", function(){
      themeGestion.container.className = themeGestion.container.className.replace("sidebar-open", "sidebar-close")
    }, false)
    themeGestion.openbutton.addEventListener("click", function(e){
      e.preventDefault();
      if(themeGestion.container.className.match("sidebar-open")){
        themeGestion.container.className = themeGestion.container.className.replace("sidebar-open", "sidebar-close")
      } else if(themeGestion.container.className.match("sidebar-close")){
        themeGestion.container.className = themeGestion.container.className.replace("sidebar-close", "sidebar-open")
      }
    }, false)
  },
  init:function(){
    themeGestion.container = document.getElementById("theme-list-container");
    themeGestion.closebutton = document.getElementById("theme-list-container-close");
    themeGestion.openbutton = document.getElementById("theme-list-container-open");
    themeGestion.initEvent();
  }
}

///////////////////////////////////////////////////////
//
//          UTILISATION DES ALERT
//
///////////////////////////////////////////////////////

// Dépendance :
// - isElement();
// - createElement();

//Envoie une mise à jour
function sendMAJ(response){
  response = JSON.parse(response);
  response.parent = document.getElementById("alerts-container");
  var message = new Message(response);
  message.run();
}

function Message(args, timelife = null){
  console.log(args);
  this.config = {
    "closeOnclick" : true
  }
  if(args.type && args.title){
    this.type = args.type;
    this.title = args.title;

    if(args.message){
      this.message = args.message;
    }
    if(args.parent && isElement(args.parent)){
      this.parent = args.parent;
    } else {
      this.parent = null;
    }
    if(timelife === null){
      this.timelife = 5000;
    }
    this.generateHtml();
  } else {
    return false;
  }
}

Message.prototype.generateHtml = function(){
  var container = createElement("div", "alert-container "+"alert-status-"+this.type);
  var title = createElement("p", "alert-title");
  title.innerHTML = this.title;
  container.appendChild(title);
  if(this.message){
    var message = createElement("p", "alert-content");
    message.innerHTML = this.message;
    container.appendChild(message);
  }
  if(this.config.closeOnclick){
    var self = this;
    container.addEventListener("click", function(){
      html = self.html;
      self.html.parentNode.removeChild(html);
      self.isRunning = false;
    }, false);
  }

  this.html = container;
  console.log("Html crée : ", this.html);
}

Message.prototype.append = function(){
  if(this.html){
    this.parent.appendChild(this.html);
  }
}

Message.prototype.remove = function(){
  this.parent.removeChild(this.html);
}

Message.prototype.run = function () {
  this.append();
  this.isRunning = true;
  var html = this.html;
  var self = this;
  setTimeout(function(){
    if(self.isRunning) {
      html.parentNode.removeChild(html);
    }
  }, this.timelife)
};





// burgerGestion.init("#sidebarLeft")
burgerGestion.init("#sidebarRight")

filter.init();
XHRformAuto.init();
themeGestion.init();
