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


mapGestion = {

  //Elements
  mapEl : document.getElementById("map"),
  sidebar : document.getElementById("sidebarRight"),

  //Sidebar de droite
  sidebarQuestionsList : document.getElementById("sidebarQuestionsList"),
  sidebarQuestionView : document.getElementById("sidebarQuestionView"),

  //Element propre au panneau de droite
  questionViewEls: {
    question : document.getElementById("response-title"),
    questionForm : document.getElementById("response-title-form"),
    author : document.getElementById("response-author"),
    dtcreation : document.getElementById("response-date-creation"),
    dtnaissance : document.getElementById("response-date-naissance"),
    content : document.getElementById("response-content"),
    likeButton : document.getElementById("response-like"),
    country : document.getElementById("response-country"),
    themeContainer : document.getElementById("response-list-themes-container"),
    otherResponseContainer : document.getElementsByClassName("other-responses")[0],
    otherResponse : document.getElementsByClassName("other-responses")[0].getElementsByTagName("ul")[0]
  },

  //Element de formulaire d'ajout de réponse
  sidebarFormContainer : document.getElementById("sidebarFormContainer"),
  sidebarForm : document.getElementById("sidebarFormContainer").getElementsByTagName("form")[0],

  //Bouton de retour au différent panneau
  comebackQuestionView : document.getElementById("comebackQuestionView"),
  comebackQuestionsList : document.getElementById("comebackQuestionsList"),
  goFormView : document.getElementById("goFormView"),

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
    var age;

    //On transpose les nouvelles questions et les anciennes
    mapGestion.previousQuestion = mapGestion.questions;
    mapGestion.questions = questions;

    if(filter.filterTab.age !== null){
      for(i=0; i<mapGestion.questions.length; i++){
        var responses = [];
        for(j=0; j<mapGestion.questions[i].responses.length; j++){
          age = getAgeResponse(mapGestion.questions[i].responses[j].dtnaissance.timestamp);
          if(age<filter.filterTab.age){
            responses.push(mapGestion.questions[i].responses[j]);
          }
        }
        mapGestion.questions[i].responses = responses;
      }
    }

    mapGestion.deleteAllMarkers();
    mapGestion.createAllMarkers();
  },


    //Rajoute un marker et ses évenements depuis une réponse
    addMarkerFromResonse: function(response, question){
      var image = {
        url : config.assetPath+"/img/marker.png",

        size: new google.maps.Size(40, 44),
         // The origin for this image is (0, 0).
        origin: new google.maps.Point(0, 0),
       // The anchor for this image is the base of the flagpole at (0, 32).
        anchor: new google.maps.Point(20, 20)

      }
      mapGestion.markers.push(new google.maps.Marker({
        position: {lat: response.country.lat, lng: response.country.ln},
        map: mapGestion.map,
        title: response.question.libQuestion,
        icon: image,
        id: response.id
      }));
      console.log(config.assetPath+"/img/marker2.png");

      var actualRank = mapGestion.markers.length-1;
      google.maps.event.addListener(mapGestion.markers[actualRank], 'click', function() {
        //On récupère les question apparaissant sur les autres pays
        var idCountry = response.country.id;
        var questionsFromPays = mapGestion.getQuestionsFromPays(idCountry);

        //Si il y a plusieurs questions :
        if(questionsFromPays.length>1){
          //On affiche la liste
          mapGestion.displayPanelQuestions();
          mapGestion.updateSidebarQuestionsView(questionsFromPays, idCountry);
          mapGestion.comebackQuestionsList.style.display = "inline-block";
        } else {
          //Sinon on affiche la question
          var responses = mapGestion.getResponseViewFromQuestion(questionsFromPays[0], idCountry);
          mapGestion.updateSidebarResponsesView(responses, questionsFromPays[0]);
          mapGestion.displayPanelResponses();
          mapGestion.comebackQuestionsList.style.display = "none";
        }
        if(!mapGestion.testSidebarOpen()){
          mapGestion.openSideBar();
        }
        mapGestion.map.panTo(mapGestion.markers[actualRank].getPosition());
      })
    },

  //Parcour les réponses et rajoutes les markers
  createAllMarkers:function(){
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
  //     Mise à jour des contenu du panneau de droite      //
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

  //Met à jour la liste des questions clickable depuis des objets questions
  updateSidebarQuestionsView:function(questions, idCountry){
    var listContainer = mapGestion.sidebarQuestionsList.getElementsByTagName("ul")[0];
    listContainer.innerHTML = "";
    console.log("List des questions :", listContainer);
    var questionsEl = [];

    for(i=0; i<questions.length; i++){
      var length = questionsEl.push(createElement("li"));
      questionsEl[i].innerHTML = questions[i].libQuestion;
      listContainer.appendChild(questionsEl[i]);
    }

    for(i=0; i<questionsEl.length; i++){
      mapGestion.initQuestionClickInSidebar(questionsEl[i], questions[i], idCountry);
    }
  },

  //Met à jour le contenu du panneau de réponse après clique;
  updateSidebarResponsesView:function(responses, question, id){
      if(!id){
        id= responses[0].id;
      }
      var otherResponse = [];
      for(i=0; i<responses.length; i++){
        if(id === responses[i].id){
          var response = responses[i];
        } else {
          otherResponse.push(responses[i]);
        }
      }

      mapGestion.questionViewEls.question.innerHTML = question.libQuestion;
      mapGestion.questionViewEls.questionForm.innerHTML = question.libQuestion;
      mapGestion.questionViewEls.author.innerHTML = response.author;
      mapGestion.questionViewEls.country.innerHTML = response.country.libCountry;
      mapGestion.questionViewEls.dtcreation.innerHTML = response.dtcreation.timestamp;
      mapGestion.questionViewEls.dtnaissance.innerHTML = response.dtnaissance.timestamp;
      mapGestion.questionViewEls.content.innerHTML = response.content;
      mapGestion.questionViewEls.content.innerHTML = response.content;

      mapGestion.questionViewEls.themeContainer.innerHTML = "";
      mapGestion.questionViewEls.otherResponse.innerHTML = "";

      mapGestion.sidebarForm.action = mapGestion.sidebarForm.action.replace(/\d+$/, question.id);

      var themesCreate = [];
      for(i=0; i<question.themes.length; i++){
        themesCreate.push(createElement("li"));
        themesCreate[i].innerHTML = question.themes[i].libTheme;
        mapGestion.questionViewEls.themeContainer.appendChild(themesCreate[i]);
      }

      var responsesCreate = [];
      if(otherResponse.length > 0){
        mapGestion.questionViewEls.otherResponseContainer.style = "display : block;";
        for(i=0; i<otherResponse.length; i++){
          responsesCreate.push(createElement("li"));
          responsesCreate[i].innerHTML = otherResponse[i].id;
          mapGestion.questionViewEls.otherResponse.appendChild(responsesCreate[i]);
          mapGestion.initEventClickOtherResponse(responsesCreate[i], responses, question, responsesCreate[i].id);
        }
      } else {
        mapGestion.questionViewEls.otherResponseContainer.style = "display : none;";
      }
  },



  //Initialise l'évenement de clique sur une question dans la liste des question
  initQuestionClickInSidebar :function(el, question, idCountry){
    var responses = mapGestion.getResponseViewFromQuestion(question, idCountry);
    el.addEventListener("click", function(){
      mapGestion.updateSidebarResponsesView(responses, question);
      mapGestion.displayPanelResponses();
    }, false)
  },

  //Initialise
  initEventClickOtherResponse:function(el, responses, question, idEl){
    el.addEventListener("click", function(){
      mapGestion.updateSidebarResponsesView(responses, question, idEl);
    }, false)
  },

  //Test si la sidebar de droite est ouverte
  testSidebarOpen: function(){
    if(mapGestion.sidebar.className.match("sidebar-open")){
      return true;
    } else {
      return false;
    }
  },

  ////////////////////////////////
  // Position sidebar et panel  //
  ////////////////////////////////

  closeSideBar:function(){
    mapGestion.sidebar.className = mapGestion.sidebar.className.replace("sidebar-open", "sidebar-close");
  },

  openSideBar:function(){
    mapGestion.sidebar.className = mapGestion.sidebar.className.replace("sidebar-close", "sidebar-open");
  },
  movePanelToLeft:function(el){
    el.className = el.className.replace("sidebar-panel-position-center", "sidebar-panel-position-left");
    el.className = el.className.replace("sidebar-panel-position-right", "sidebar-panel-position-left");
  },
  movePanelToRight:function(el){
    el.className = el.className.replace("sidebar-panel-position-left", "sidebar-panel-position-right");
    el.className = el.className.replace("sidebar-panel-position-center", "sidebar-panel-position-right");
  },
  movePanelToCenter:function(el){
    el.className = el.className.replace("sidebar-panel-position-left", "sidebar-panel-position-center");
    el.className = el.className.replace("sidebar-panel-position-right", "sidebar-panel-position-center");
  },
  displayPanelForm : function(){
    mapGestion.movePanelToLeft(mapGestion.sidebarQuestionsList);
    mapGestion.movePanelToLeft(mapGestion.sidebarQuestionView);
    mapGestion.movePanelToCenter(mapGestion.sidebarFormContainer);
  },
  displayPanelQuestions : function(){
    mapGestion.movePanelToRight(mapGestion.sidebarQuestionView);
    mapGestion.movePanelToCenter(mapGestion.sidebarQuestionsList);
    mapGestion.movePanelToRight(mapGestion.sidebarFormContainer);
  },
  displayPanelResponses : function(){
    mapGestion.movePanelToRight(mapGestion.sidebarFormContainer);
    mapGestion.movePanelToCenter(mapGestion.sidebarQuestionView);
    mapGestion.movePanelToLeft(mapGestion.sidebarQuestionsList);
  },

  ////////////////////////////////
  // Fonction d'initialisation  //
  ////////////////////////////////

  initEventSidebar :function(){
    mapGestion.comebackQuestionView.addEventListener("click", function(){
      mapGestion.displayPanelResponses();
    }, false)
    mapGestion.comebackQuestionsList.addEventListener("click", function(){
      mapGestion.displayPanelQuestions();
    }, false)
    mapGestion.goFormView.addEventListener("click", function(){
      mapGestion.displayPanelForm();
    }, false)
  },

  initSizeMap: function(){
    setToWindowHeight(mapGestion.mapEl);
    window.addEventListener("resize", function(){
      setToWindowHeight(mapGestion.mapEl);
    }, false)
  },

  initListenerMap:function(){
    mapGestion.map.addListener("zoom_changed", function(){
      mapGestion.zoom = mapGestion.map.getZoom();
    });
    mapGestion.map.addListener("center_changed", function(){
      mapGestion.center = mapGestion.map.getCenter();
    });
  },

  initStyleMap: function(){
    //On génère l'objet de style
    mapGestion.styledMap = new google.maps.StyledMapType(stylesArray,
    {name: "Styled Map"});

    mapGestion.map.mapTypes.set('map_style', mapGestion.styledMap);
    mapGestion.map.setMapTypeId('map_style');
  },

  init:function(){
    mapGestion.initEventSidebar();
    mapGestion.initSizeMap();
    mapGestion.initStyleMap();
    mapGestion.initListenerMap();
  }
}

var stylesArray = [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"administrative.land_parcel","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":"25"},{"lightness":"-3"},{"gamma":"1"},{"weight":"1"}]},{"featureType":"landscape.man_made","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#085076"}]}]
