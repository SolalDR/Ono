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

function getAgeResponse(date){
  date*=1000;
  var actualDate = Date.now();
  var diff = actualDate-date;
  //Miliseconde

  diff = Math.floor(diff/1000/60/60/24/365);
  return diff;
}

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

mapGestion = {
  //Elements
  mapEl : document.getElementById("map"),
  sidebar : document.getElementById("sidebarRight"),
  //Sidebar de droite
  sidebarQuestionsList : document.getElementById("sidebarQuestionsList"),
  sidebarQuestionView : document.getElementById("sidebarQuestionView"),

  questionViewEls: {
    question : document.getElementById("response-title"),
    questionForm : document.getElementById("response-title-form"),
    author : document.getElementById("response-author"),
    dtcreation : document.getElementById("response-date-creation"),
    dtnaissance : document.getElementById("response-date-naissance"),
    content : document.getElementById("response-content"),
    country : document.getElementById("response-country"),
    themeContainer : document.getElementById("response-list-themes-container"),
    otherResponseContainer : document.getElementsByClassName("other-responses")[0],
    otherResponse : document.getElementsByClassName("other-responses")[0].getElementsByTagName("ul")[0]
  },
  sidebarFormContainer : document.getElementById("sidebarFormContainer"),
  sidebarForm : document.getElementById("sidebarFormContainer").getElementsByTagName("form")[0],

  comebackQuestionView : document.getElementById("comebackQuestionView"),
  comebackQuestionsList : document.getElementById("comebackQuestionsList"),
  goFormView : document.getElementById("goFormView"),
  //Config
  zoom : 3,
  markers: [],
  questions:[],
  previousQuestion: [],


  //////////////////////////////////////////////////
  //        Markers et gestions des questions     //
  //////////////////////////////////////////////////

    //Rajoute les question ainsi que leurs réponse dans le tableau questions
  addQuestions: function(json){
    for(i=0; i<json.length; i++){
      mapGestion.questions.push(json[i]);
    }
  },

  // Met à jour les markeurs de la cartes au changement de filtres
  updateQuestionFromJson: function(json){
    var questions = JSON.parse(json);
    var age;

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

  //Rajoute un marker depuis une réponse
  addMarkerFromResonse: function(response, question){
    mapGestion.markers.push(new google.maps.Marker({
      position: {lat: response.country.lat, lng: response.country.ln},
      map: mapGestion.map,
      title: response.question.libQuestion,
      id: response.id
    }));

    var actualRank = mapGestion.markers.length-1;
    google.maps.event.addListener(mapGestion.markers[actualRank], 'click', function() {

      //On stocke l'id du pays
      var idCountry = response.country.id;

      //On doit tester si il y a plusieurs markers correspondant à ces coordonnées
      var questionsFromPays = mapGestion.getQuestionsFromPays(idCountry);
      
      //Si il y a plusieurs questions :
      if(questionsFromPays.length>1){
        //On affiche la liste
        mapGestion.displayPanelQuestions();
        mapGestion.updateQuestionsOnSidebar(questionsFromPays, idCountry);
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

  initQuestionClickInSidebar :function(el, question, idCountry){
    var responses = mapGestion.getResponseViewFromQuestion(question, idCountry);
    el.addEventListener("click", function(){
      mapGestion.updateSidebarResponsesView(responses, question);
      mapGestion.displayPanelResponses();
    }, false)
  },
  //Met à jour la liste des questions clickable depuis des objets questions
  updateQuestionsOnSidebar:function(questions, idCountry){
    var listContainer = mapGestion.sidebarQuestionsList.getElementsByTagName("ul")[0];
    listContainer.innerHTML = "";
    console.log("List des questions :", listContainer);
    var questionsEl = [];

    for(var i=0; i<questions.length; i++){
      var length = questionsEl.push(createElement("li"));
      questionsEl[i].innerHTML = questions[i].libQuestion;
      listContainer.appendChild(questionsEl[i]);
    }

    for(var i=0; i<questionsEl.length; i++){
      mapGestion.initQuestionClickInSidebar(questionsEl[i], questions[i], idCountry);
    }
  },

  updateSidebarTransitionState: function(){
    mapGestion.sidebarQuestionView.className = mapGestion.sidebarQuestionView.className.replace("state-fixe", "state-transition");
    setTimeout(function(){
      mapGestion.sidebarQuestionView.className = mapGestion.sidebarQuestionView.className.replace("state-transition", "state-fixe");
    }, 500);
  },

  initEventClickOtherResponse:function(el, responses, question, idEl){
    el.addEventListener("click", function(){
      mapGestion.updateSidebarResponsesView(responses, question, idEl);
    }, false)
  },

  //Met à jour le contenu d'une réponse
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

var stylesArray = [
    {
        "featureType": "all",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#6582be"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#444444"
            }
        ]
    },
    {
        "featureType": "administrative.country",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "administrative.country",
        "elementType": "labels.text",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative.country",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative.country",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "visibility": "off"
            },
            {
                "lightness": "0"
            },
            {
                "gamma": "1.00"
            },
            {
                "saturation": "1"
            },
            {
                "weight": "1.35"
            }
        ]
    },
    {
        "featureType": "administrative.province",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative.land_parcel",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "administrative.land_parcel",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "color": "#ffffff"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#131c32"
            }
        ]
    },
    {
        "featureType": "landscape.natural.landcover",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "all",
        "stylers": [
            {
                "saturation": -100
            },
            {
                "lightness": 45
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.text",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "saturation": "4"
            },
            {
                "hue": "#ff009a"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "weight": "7.71"
            },
            {
                "gamma": "10.00"
            },
            {
                "lightness": "100"
            },
            {
                "visibility": "simplified"
            },
            {
                "hue": "#ff0000"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "weight": "9.46"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "weight": "7.17"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "transit.line",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#5c7fb2"
            }
        ]
    }
]
