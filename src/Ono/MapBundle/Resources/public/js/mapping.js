//Fonction d'initialisation de la carte
function initMap() {
  mapGestion.map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: 30, lng: 2.4},//Centre sur la france
    // center: {lat: -26, lng: 28},//Centre sur la france

    zoom: 3,
    mapTypeControlOptions: {
      mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
    },
    scrollwheel:false,
    draggable: true,
    disableDefaultUI: true
  });
  mapGestion.init();
}

toHtml = function(str){
  var fragment = document.createElement("fragment");
  fragment.innerHTML = str;
  console.log(fragment);
  return fragment.firstChild;
}

//////////////////////////////
//
//      Sidebar manage
//
///////////////////////////////

function ProtoGen(args){
  this.events={};
  if(args.parent){
    this.parent = args.parent;
  }
  if(args.datas){
    this.datas = args.datas;
  }
  if(args.onclick){
    this.events.onclick = args.onclick;
  }
  this.prototype = this.parent.getAttribute("data-prototype");
  this.gen();
}

ProtoGen.prototype.gen = function(){
  var output = this.prototype;
  var datas = this.datas;
  for( data in datas ){
    output = output.replace(data, datas[data])
  }
  this.result = toHtml(output);
  this.html = output;
  this.initEvent();
}

ProtoGen.prototype.initEvent = function(){
  if(this.result){
    var self = this;
    this.result.onclick = function(){
      self.events.onclick(this);
    }
  }
}


mapSidebarGestion = {
  btn:{},
  lists:{},
  secure : {
    state: [
      "show-state",
      "list-state"
    ],
  },

  hide:function(){
    this.sidebar.className = this.sidebar.className.replace("sidebar-open", "sidebar-close");
  },

  display:function(){
    this.sidebar.className = this.sidebar.className.replace("sidebar-close", "sidebar-open");
  },



  toggleState:function(actual, next){
    if(this.secure.state.indexOf(actual) === -1 || this.secure.state.indexOf(next) === -1 ){
      console.warn(actual+" ou "+next+" ne sont pas des états sécurisé");
      return false;
    }
    this.body.className = this.body.className.replace(actual, next);
  },

  isOpen:function(){
    if(this.sidebar.className.match("sidebar-open")){
      return true;
    } else {
      return false;
    }
  },

  initEvents:function(){
    var self = this;
    this.btn.close.addEventListener("click", function(e){
      e.preventDefault();
      self.hide();
      return false;
    }, false)
  },

  initItems:function(){
    for(i=0; i<this.items.length; i++){
      this.initClickItem(this.items[i]);
    }
  },

  getParentState:function(el){
    var test = false;
    var testEl = el;
    var state = null;
    while(test === false && testEl.tagName != "BODY"){
      for(i=0; i<this.secure.state.length; i++){
        if(testEl.className.match(this.secure.state[i])) {
          state = this.secure.state[i];
          test=true;
        }
      }
      if(test === false){
        testEl = testEl.parentNode;
      }
    }
    return state;
  },

  getNextState:function(actual){
    for(i=0; i<this.secure.state.length; i++){
      if(actual != this.secure.state[i]){
        return this.secure.state[i];
      }
    }
  },

  getQuestion:function(id, isBest=false){
    id= parseInt(id);
    var question, response;
    var object = {};
    for(i=0; i<this.questions.length; i++){
      if(this.questions[i].id===id){
        question = this.questions[i];
        response = question.responses[0];
      }
    }
    if(question && response){
      object.title = question.libQuestion;
      object.author = response.author;
      object.dtNaissance = response.dtnaissance.timestamp;
      object.content = response.content;
      object.resource = response.resource ? response.resource : "";
      object.resourceLegend = response.resource ? response.resource : "";
      object.country = response.country.libCountry;
      object.likes = response.nbLikes;
      object.id = response.id;
      object.likePath = "";
      object.showPath = config.responseShowPath.replace(/(\d)$/, response.id);
      return object;
    }
    return null;
  },

  getArticle:function(id, isBest=false){
    id= parseInt(id);
    var article;
    var object = {};

    for(i=0; i<this.articles.length; i++){
      if(this.articles[i].id===id){
        article = this.articles[i];
      }
    }
    if(article){
      object.title = article.title;
      object.author = "";
      object.dtNaissance = "";
      object.content = article.content;
      object.resource = article.resource ? article.resource : "";
      object.resourceLegend = article.resource ? article.resource : "";
      object.country = article.country.libCountry;
      object.likes = article.nbLikes;
      object.id = article.id;
      object.likePath = "";
      object.showPath = config.articleShowPath.replace(/(\d)$/, article.id);
      return object;
    }
    return null;
  },

  initClickItem:function(el){
    var self = mapSidebarGestion;
    el.onclick = function(){

      //On cherche dans l'object questions
      if(this.getAttribute("data-object")==="questions"){
        var object = self.getQuestion(this.getAttribute("data-href"));
        self.updateShow(object);
      } else if(this.getAttribute("data-object")==="articles") {
        var object = self.getArticle(this.getAttribute("data-href"));
        self.updateShow(object);
      }

      //Bouge le panneau
      var actual = self.getParentState(this);
      setTimeout(function(){
        self.toggleState(actual, self.getNextState(actual));
      }, 100)
    }
  },


  genLists:function(questions, articles){
    var questionsItem = [];
    var question;
    for(i=0; i<questions.length; i++){
      question = questions[i];
      var proto = new ProtoGen({
        parent : this.lists.questions.container,
        datas: {
            __object__ : "questions",
            __id__: question.id,
            __lib__ : question.libQuestion,
            __count__: question.responses.length
          },
        onclick: this.initClickItem
      });
      questionsItem.push(proto.result);
    }
    this.lists.questions.els = questionsItem;
    this.appendList(this.lists.questions.container, this.lists.questions.els);

    var articlesItem = [];
    var article;
    for(i=0; i<articles.length; i++){
      article = articles[i];
      var proto = new ProtoGen({
        parent : this.lists.articles.container,
        datas: {
            __object__ : "articles",
            __id__: article.id,
            __lib__ : article.title,
            __count__: article.nbLikes
          },
        onclick: this.initClickItem
      });
      articlesItem.push(proto.result);
    }
    this.lists.articles.els = articlesItem;
    this.appendList(this.lists.articles.container, this.lists.articles.els);


  },

  resetList:function(){
    this.lists.questions.container.innerHTML="";
    this.lists.articles.container.innerHTML="";
  },

  appendList:function(container, array){
    for(i=0; i<array.length; i++){
      container.appendChild(array[i]);
      console.log("Elements Add", array[i]);
    }
  },

  setCountry: function(str){
    this.sidebar.getElementsByClassName("sidebar-title")[0].innerHTML = str;
  },

  manageListTitle:function(qCount, aCount){
    this.lists.questions.title.className = qCount ? "list-title visible" : "list-title hidden";
    this.lists.articles.title.className = aCount ? "list-title visible" : "list-title hidden";
  },

  updatePanel:function(questions, articles=null){
    //Met à jour les données
    this.questions = questions;
    this.articles = articles;

    this.manageListTitle(questions.length, articles.length);

    this.toggleState("show-state", "list-state");
    if(questions.length){
      this.setCountry(questions[0].responses[0].country.libCountry);
    } else {
      this.setCountry(articles[0].country.libCountry);
    }

    this.resetList();
    this.genLists(questions, articles);
  },

  initShowEvent:function(){
    var items = this.show.querySelectorAll("*[data-href]");
    for(i=0; i<items.length; i++){
      this.initClickItem(items[i]);
    }
  },

  updateShow:function(object){
    this.show.innerHTML ="";
    var proto = new ProtoGen({
      parent : this.show,
      datas: {
          __id__: object.id,
          __title__: object.title,
          __author__ : object.author,
          __dt_naissance__: object.dtNaissance,
          __content__: object.content,
          __resource__: object.resource,
          __resource_legend__: object.resourceLegend,
          __country__: object.country,
          __likes__: object.likes,
          __is_liked__: "",
          __like_path__: object.likePath,
          __show_path__ : object.showPath
        }
    });
    this.show.innerHTML = proto.html;
    this.initShowEvent();
  },


  init:function(){
    this.sidebar = document.getElementById("sidebarRight");
    this.btn.close = this.sidebar.getElementsByClassName("sidebar-close-button")[0];
    this.body = this.sidebar.getElementsByClassName("sidebar-body")[0];

    this.show = document.getElementById("show-item");

    this.lists.questions = {
      container : this.sidebar.getElementsByClassName("list-question")[0],
      title: this.sidebar.getElementsByClassName("list-title")[0]
    };
    this.lists.articles = {
      container : this.sidebar.getElementsByClassName("list-article")[0],
      title: this.sidebar.getElementsByClassName("list-title")[1]
    }

    //Devrons être généré après chaque mise à jour
    this.items = this.sidebar.querySelectorAll("[data-href]");
    this.initItems();

    if(this.sidebar && this.btn.close){
      this.initEvents();
    }
  }
}






mapGestion = {

  //Elements
  mapEl : document.getElementById("map"),

  //Variable initialisé
  zoom : 3,
  markers: [],
  questions:[],
  articles:[],
  previousQuestion: [],


  //////////////////////////////////////////////////
  //        Mise à jour des markers               //
  //////////////////////////////////////////////////


  //Rajoute les question ainsi que leurs réponse dans le tableau questions
  //Post load
  addQuestions: function(json){
    var questions = json.questions;
    var articles = json.articles;
    for(i=0; i<questions.length; i++){
      mapGestion.questions.push(questions[i]);
    }
    for(i=0; i<articles.length; i++){
      mapGestion.articles.push(articles[i]);
    }
  },

  // Met à jour les markeurs de la cartes au changement de filtres
  // Post Request
  updateQuestionFromJson: function(json){
    json = JSON.parse(json);
    var questions = json.questions;
    var articles = json.articles

    //On transpose les nouvelles questions et les anciennes
    mapGestion.previousQuestion = mapGestion.questions;
    mapGestion.questions = questions;
    mapGestion.articles = articles;

    //On supprime tout, on recré
    mapGestion.deleteAllMarkers();
    mapGestion.createAllMarkers();
  },


  //Rajoute un objet Google Marker dans le tableau de marker
  pushMarkerResponse:function(response, question){
    this.markers.push(new google.maps.Marker({
      position: {lat: response.country.lat, lng: response.country.ln},
      map: mapGestion.map,
      title: response.question.libQuestion,
      icon: mapGestion.image,
      id: response.id
    }));

    return this.markers[this.markers.length-1];
  },

  pushMarkerArticle:function(article){
    this.markers.push(new google.maps.Marker({
      position: {lat: article.country.lat, lng: article.country.ln},
      map: mapGestion.map,
      title: article.title,
      icon: mapGestion.image,
      id: article.id
    }));

    return this.markers[this.markers.length-1];
  },

  initMarkerEvent :function(marker, object){
    google.maps.event.addListener(marker, 'click', function() {

      if(!mapSidebarGestion.isOpen()){
        mapSidebarGestion.display();
        mapSidebarGestion.toggleState("show-state", "list-state");
      }
      mapGestion.map.panTo(marker.getPosition());

      var idCountry = object.country.id;
      var questionsFromPays = mapGestion.getQuestionsFromPays(idCountry);
      var articlesFromPays = mapGestion.getArticlesFromPays(idCountry);

      console.log(articlesFromPays);
      mapSidebarGestion.updatePanel(questionsFromPays, articlesFromPays);
      console.log(questionsFromPays);

    })
  },

  //Rajoute un marker et ses évenements depuis une réponse
  addMarkerFromResonse: function(response, question){
    var marker = this.pushMarkerResponse(response, question);
    this.initMarkerEvent(marker, response);
  },

  addMarkerFromArticle:function(article){
    var marker = this.pushMarkerArticle(article);
    this.initMarkerEvent(marker, article);
  },

  //Parcour les réponses et rajoutes les markers
  createAllMarkers:function(){
    mapGestion.image = {
      url : config.assetPath+"/img/marker.png",
      size: new google.maps.Size(40, 44),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(20, 20)
    };
    for(i=0; i<mapGestion.questions.length; i++){
      for(j=0; j<mapGestion.questions[i].responses.length; j++){
        mapGestion.addMarkerFromResonse(mapGestion.questions[i].responses[j], mapGestion.questions[i]);
      }
    }

    for(i=0; i<mapGestion.articles.length; i++){
      mapGestion.addMarkerFromArticle(mapGestion.articles[i]);
    }
  },

  //Supprimer tout les markers
  deleteAllMarkers : function(){
    for(i=0; i<mapGestion.markers.length; i++){
      mapGestion.markers[i].setMap(null);
    }
  },


  ///////////////////////////////////////////////////////////
  //     Traitemnet des données du json      //
  ///////////////////////////////////////////////////////////

  //Récupère les réponse d'une question donnée pour un pays donné
  getResponseViewFromQuestion :function(question, idCountry){
    var responseTab = [];
    for(i=0; i<question.responses.length; i++){
      if(question.responses[i].country.id === idCountry){
        responseTab.push(question.responses[i]);
      }
    }
    return responseTab;
  },

  //Récupère la liste des questions intervenant pour un pays donnée
  getQuestionsFromPays: function(id){
    var questionReturn = [];
    for(test = false, i=0; i<mapGestion.questions.length; i++, test=false){
      for(j=0; j<mapGestion.questions[i].responses.length; j++){
        if(mapGestion.questions[i].responses[j].country.id === id && test=== false){
          questionReturn.push(mapGestion.questions[i]);
          test=true;
        }
      }
    }
    return questionReturn;
  },

  getArticlesFromPays:function(id){
    var articlesReturn =[];
    for(i=0; i<mapGestion.articles.length; i++){
      if(mapGestion.articles[i].country.id === id){
        articlesReturn.push(mapGestion.articles[i]);
      }
    }
    return articlesReturn;
  },

  ///////////////////////////////////////////////////////////
  //     Fonctionnement d'initialisation
  ///////////////////////////////////////////////////////////

  //Gère le resize de la fenêtre
  initSizeMap: function(){
    setToWindowHeight(mapGestion.mapEl);
    window.addEventListener("resize", function(){
      setToWindowHeight(mapGestion.mapEl);
    }, false)
  },

  //Intègre les évenement de la map
  initListenerMap:function(){
    mapGestion.map.addListener("zoom_changed", function(){
      mapGestion.zoom = mapGestion.map.getZoom();
    });
    mapGestion.map.addListener("center_changed", function(){
      mapGestion.center = mapGestion.map.getCenter();
    });
  },

  //Intègre le style à la carte
  initStyleMap: function(){
    mapGestion.styledMap = new google.maps.StyledMapType(stylesArray,
    {name: "Styled Map"});

    mapGestion.map.mapTypes.set('map_style', mapGestion.styledMap);
    mapGestion.map.setMapTypeId('map_style');
  },

  init:function(){
    mapGestion.initSizeMap();
    mapGestion.initStyleMap();
    mapGestion.initListenerMap();
  }
}

var stylesArray = [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"administrative.land_parcel","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":"25"},{"lightness":"-3"},{"gamma":"1"},{"weight":"1"}]},{"featureType":"landscape.man_made","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#085076"}]}]
