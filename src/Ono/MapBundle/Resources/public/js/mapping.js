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


//////////////////////////////
//
//      Sidebar manage
//
///////////////////////////////

mapSidebarGestion = {
  btn:{},
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

  initClickItem:function(el){
    var self = this;
    el.onclick = function(){
      var actual = self.getParentState(this);
      var next = self.getNextState(actual);
      self.toggleState(actual, next);
    }
  },


  init:function(){
    this.sidebar = document.getElementById("sidebarRight");
    this.btn.close = this.sidebar.getElementsByClassName("sidebar-close-button")[0];
    this.body = this.sidebar.getElementsByClassName("sidebar-body")[0];

    //Devrons être généré après chaque mise à jour
    this.items = this.sidebar.querySelectorAll("[data-href]");
    console.log(this.items);
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
  previousQuestion: [],


  //////////////////////////////////////////////////
  //        Mise à jour des markers               //
  //////////////////////////////////////////////////


  //Rajoute les question ainsi que leurs réponse dans le tableau questions
  //Post load
  addQuestions: function(json){
    for(i=0; i<json.length; i++){
      mapGestion.questions.push(json[i]);
    }
  },

  // Met à jour les markeurs de la cartes au changement de filtres
  // Post Request
  updateQuestionFromJson: function(json){
    var questions = JSON.parse(json);

    //On transpose les nouvelles questions et les anciennes
    mapGestion.previousQuestion = mapGestion.questions;
    mapGestion.questions = questions;

    //On supprime tout, on recré
    mapGestion.deleteAllMarkers();
    mapGestion.createAllMarkers();
  },


  //Rajoute un objet Google Marker dans le tableau de marker
  pushMarker:function(response, question){
    this.markers.push(new google.maps.Marker({
      position: {lat: response.country.lat, lng: response.country.ln},
      map: mapGestion.map,
      title: response.question.libQuestion,
      icon: mapGestion.image,
      id: response.id
    }));

    return this.markers[this.markers.length-1];
  },

  initMarkerEvent :function(marker, response, question){
    google.maps.event.addListener(marker, 'click', function() {
      //On récupère les question apparaissant sur les autres pays

      if(!mapSidebarGestion.isOpen()){
        mapSidebarGestion.display();
      }
      mapGestion.map.panTo(marker.getPosition());
      // var idCountry = response.country.id;
      // var questionsFromPays = mapGestion.getQuestionsFromPays(idCountry);
      //
      // //Si il y a plusieurs questions :
      // if(questionsFromPays.length>1){
      //   //On affiche la liste
      //   mapGestion.displayPanelQuestions();
      //   mapGestion.updateSidebarQuestionsView(questionsFromPays, idCountry);
      // } else {
      //   //Sinon on affiche la question
      //   var responses = mapGestion.getResponseViewFromQuestion(questionsFromPays[0], idCountry);
      //   mapGestion.updateSidebarResponsesView(responses, questionsFromPays[0]);
      //   mapGestion.displayPanelResponses();
      // }

    })
  },

  //Rajoute un marker et ses évenements depuis une réponse
  addMarkerFromResonse: function(response, question){
    var marker = this.pushMarker(response, question);
    this.initMarkerEvent(marker, response, question);
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
