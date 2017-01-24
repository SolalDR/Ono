
/////////////////////////////////////////////
//
//         FONCTION GÉNÉRIQUE
//
/////////////////////////////////////////////


function Request(args){
  if(args.url){
    this.target = args.target;
    this.url = args.url;
  }
  if(args.method){
    this.method = args.method;
  } else {
    this.method = "GET";
  }
  if(args.json){
    this.json = args.json;
  } else {
    this.json = true;
  }
  if(args.callback){
    this.callback = args.callback;
  }
  if(args.target){
    this.target = args.target;
  }
  if(args.data){
    this.data = args.data;
  } else if(args.formData){
    this.formData = args.formData;
  }
  this.additionnalData = args.additionnalData;
  this.xhr = getXhrObject();
}

Request.prototype.open = function(){
  var self = this;
  this.xhr.open(this.method, this.url, true);
  this.opened = true;
  this.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

  this.xhr.onreadystatechange = function() {
    if(this.readyState === 4 && this.status === 200) {
      var response = this.responseText;
      self.success = true;
      if(typeof self.callback === "function") {
        self.callback(response);
      } else {
        console.log("Response successfull but no callback");
      }
    }
  }
  return this;
}

Request.prototype.send = function(){
  if(!this.opened){
    console.warn("Objet XHR ouvert automatiquement");
    this.open();
  }

  //Si données en string ou en json
  // if(this.data){
    var data = "";
    if(this.data && this.json){
      console.log(this.data);
      data = "json="+JSON.stringify(this.data);
    } else {
      console.log(typeof this.data);
      if(this.data && typeof this.data === "string"){
        data = this.data;
      }
    }

  this.xhr.send(data);
  return this;
}

//S'applique à un élément du DOM, le Supprime du DOM
Node.prototype.remove = function(){
  var parent = this.parentNode;
  if(parent){
    parent.removeChild(this);
  }
}
Node.prototype.setStyle = function(obj){
  var s="";
  for(sProp in obj){
    s+=sProp+":"+obj[sProp]+";";
  }
  var a = this.getAttribute("style");
  s+=a;
  this.setAttribute("style", s);

  return s;
}

function toggleFullScreen() {
  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }
}


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

function toggleClassSidebarOpen(el, classOpen, classClose){
  if(el.className.match(classOpen)){
    el.className = el.className.replace(classOpen, classClose)
  } else if(el.className.match(classClose)){
    el.className = el.className.replace(classClose, classOpen)
  }
}

function getXhrObject(){
  // ### Construction de l’objet XMLHttpRequest selon le type de navigateur
  if(window.XMLHttpRequest){
    return new XMLHttpRequest();
  }	else if(window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  } else {
    // XMLHttpRequest non supporté par le navigateur
    console.log("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
    return;
  }
}

// Envoie la XHR
function callScript (scriptName, args, form, isJsonEncode){
	var xhr_object = getXhrObject();

  console.log(scriptName);
	xhr_object.open("POST", scriptName, true);

	//  Définition du comportement à adopter sur le changement d’état de l’objet XMLHttpRequest
	xhr_object.onreadystatechange = function() {
	  if(this.readyState === 4 && this.status === 200) {
      if(form) {
        if(form.getAttribute("data-action")) {
          XHRResponse.init(xhr_object.responseText, form, args);
        }
      } else {
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

function getArgs(els, arg){
  for(i=0; i<els.length; i++){
    if(els[i]){
      arg[els[i].name]= els[i].value;
    }
  }
  return arg;
}

function getInputs(inputs, arg){
  for(i=0; i<inputs.length; i++){
    if(inputs[i]){
      if(inputs[i].value && inputs[i].name){
        arg[inputs[i].name] = inputs[i].value;
      }
    }
  }
  return arg
}

XHRformAuto = {
 forms : document.querySelectorAll("form[data-xhrpost='true']"),
 main : function(form){
   form.onsubmit = function(e){
     e.preventDefault();
     return false;
   }
   var submitButton = form.querySelectorAll("input[type='submit'], button[type='submit']")[0];
   submitButton.addEventListener("click", function(e){
     var arg = {};
     var inputs = form.getElementsByTagName("input");
     var textarea = form.getElementsByTagName("textarea");
     var select = form.getElementsByTagName("select");
     var test;

     arg = getArgs(select, arg);
     arg = getArgs(textarea, arg);
     arg = getInputs(inputs, arg);

    if(form.getAttribute("data-json")){
      test = callScript(form.action, arg, form,  true);
    } else {
      test = callScript(form.action, arg, form);
    }
   }, false)
 },
 realiseAction(txt, form, content){
   function removeForm(form){
     var formParent = form.parentNode;
     var parent = form.parentNode;
     parent.removeChild(formParent);
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

  updateContent: function(response){
    response = JSON.parse(response);
    var button = likeManage.currentAction.el;
    var id = button.getAttribute("data-id");
    var content = document.querySelector("[data-like-content][data-id='"+id+"']");

    content.innerHTML = response.nbLike;
    button.href = response.nextRoute;

    if(response.liking){
      console.log("Like");
      button.parentNode.className += " liked";
    } else {
      console.log("Unlike");
      button.parentNode.className = button.parentNode.className.replace("liked", "");
    }
  },

  initEvent : function(el){
    var self = this;
    el.addEventListener("click", function(e){

      self.currentAction = {};
      self.currentAction.request = new Request({
        url : this.href,
        method: "GET",
        callback : self.updateContent
      }).send();

      console.log(self.currentAction);

      self.currentAction.el = this;

      e.preventDefault();
      e.stopPropagation();
      return false;
    }, false)
  },

  initEvents:function(){
    for(i=0; i<this.buttons.length; i++){
      this.initEvent(this.buttons[i])
    }
  },

  init:function(){
    this.buttons = document.querySelectorAll("[data-like-action]");
    if(this.buttons.length){
      this.initEvents();
    }
  }
}

///////////////////////////////////////////////////////
//
//      headerComplement
//
///////////////////////////////////////////////////////

headerComplement = {

  display:function(){
    this.menu.className = this.menu.className.replace("hidden", "visible");
  },

  hide:function(){
    this.menu.className = this.menu.className.replace("visible", "hidden");
  },

  isHide:function(){
    return this.menu.className.match("visible") ? false : true;
  },

  initEvents:function(){
    var self = this;
    this.button.addEventListener("click", function(e){
      e.preventDefault();
      if(self.isHide()){
        self.display();
      } else {
        self.hide();
      }
      return false;
    }, false)
  },
  init:function(){
    this.button = document.getElementById("headerComplementButton");
    this.menu = document.getElementById("headerComplementMenu");
    if(this.button && this.menu){
      if(!this.menu.className.match(/(?:visible|hidden)/)){
        this.menu.className+= " hidden";
      }
      this.initEvents();
    }
  }
}

headerComplement.init();

///////////////////////////////////////////////////////
//
//
//
//   gère les test et requête xhr pour like & dislike
//
///////////////////////////////////////////////////////

tagManage = {
  tags : [],
  hydrateObject:function(){
    for(i=0; i<tagManage.tagsEl.length; i++){
      tagManage.tags.push({
        "id":  tagManage.tagsEl[i].getAttribute("data-id"),
        "lib" : tagManage.tagsEl[i].innerHTML
      });
    }
  },
  run:function(){
    var regStr, link, str;
    var regPunctu = "\(\[\\<\\>\\.\\s\\,\\&\]\)" //On récupère les " . , & < > "
    for(i=0; i<tagManage.tags.length; i++){

      regStr = new RegExp(regPunctu+tagManage.tags[i].lib+regPunctu, "g");
      link = tagManage.prototype.replace(/(.+?)\d+$/, "$1"+tagManage.tags[i].id)
      str = tagManage.content.innerHTML
      str = str.replace(regStr, '$1<a class="link-tag" href="'+link+'">'+tagManage.tags[i].lib+'</a>$2')
      tagManage.content.innerHTML = str;
      console.log(str);
      console.log(regStr, '<a class="link-tag" href="'+link+'">'+tagManage.tags[i].lib+'</a>');
    }
  },
  init:function(){
    //On influe sur ce tag car tout les tags ne seront pas traité par le script
    tagManage.tagsEl = document.getElementsByClassName("tag-content-manage");
    tagManage.content = document.getElementById("tagManageContent");
    if(tagManage.content && tagManage.tagsEl){
      tagManage.prototype = tagManage.content.getAttribute("data-prototypetag")
      tagManage.hydrateObject();
      tagManage.run();
    }
  }
}


///////////////////////////////////////////////////////
//
//          Capte filter
//
///////////////////////////////////////////////////////

filter = {
  limit:5,
  filterThemeActive : [],
  filterTab : {
    themes : [],
    age : null
  },

  testCount: function(){
    var count = 0;
    for(i=0; i<this.filterThemeActive.length ; i++){
      if(this.filterThemeActive[i]){
        count++;
      }
    }
    console.log(count, this.limit);
    if(count < this.limit){
      return true;
    } else {
      return false;
    }
  },

  updateQuestionCallscript: function(scriptName, args){
  	var xhr_object = getXhrObject();
  	xhr_object.open("POST", scriptName, true);

  	//  Définition du comportement à adopter sur le changement d’état de l’objet XMLHttpRequest
  	xhr_object.onreadystatechange = function() {
  	  if(this.readyState === 4 && this.status === 200) {
        if(typeof mapGestion !== "undefined"){
          mapGestion.updateQuestionFromJson(xhr_object.responseText);
        } else {
          config.xhrLastResponse = xhr_object.responseText;
        }
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
    var self = this;
    el.addEventListener("click", function(){
      console.log(self.testCount());
      if(el.className.match("active")){
        console.log("Hide");
        el.className = el.className.replace("active", "");
        filter.filterThemeActive[parseInt(el.getAttribute("data-id"))] = false
      } else if(self.testCount()){
        console.log("Display");
        el.className+= " active";
        filter.filterThemeActive[parseInt(el.getAttribute("data-id"))] = true;
      } else {
        console.log("No modif");
        return;
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

  //Se lance au chargement de la page
  initFilterThemeActive : function(){
    if(filter.themes) {
      for(i=0; i<filter.themes.length; i++){
        if(filter.themes[i].className.match("active")){
          filter.filterThemeActive[parseInt(filter.themes[i].getAttribute('data-id'))] = true;
        }
      }
    }
  },
  init: function(){
    filter.themes = document.getElementsByClassName("filter-theme");
    filter.age = document.getElementById("rangeAge");
    filter.ageControl = document.getElementById("rangeAgeActive");
    filter.initFilterThemeActive();
    filter.initEvent();
    console.log(filter.filterThemeActive);
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
      toggleClassSidebarOpen(burgerGestion.container, "sidebar-open", "sidebar-close");
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
    if(burgerGestion.container) {
      burgerGestion.burger = burgerGestion.container.getElementsByClassName("burger-button")[0];
      burgerGestion.initSidebarSize();
      burgerGestion.initEvent();
    }
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
      toggleClassSidebarOpen(themeGestion.container, "sidebar-open", "sidebar-close");
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
  var containerString = "alert-container alert-status-"+this.type
  var container = createElement("div", containerString);
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
likeManage.init();
filter.init();
XHRformAuto.init();
themeGestion.init();
tagManage.init();
