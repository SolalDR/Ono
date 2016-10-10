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
  el.className+=classname;
  for(attribute in attributes){
    el.setAttribute(attribute, attributes[attribute]);
  }
  return el;
}

mapGestion = {
  mapEl : document.getElementById("map"),
  zoom : 3,
  sidebar : document.getElementById("sidebarRight"),
  elements: {
    question : document.getElementById("response-title"),
    author : document.getElementById("response-author"),
    dtcreation : document.getElementById("response-date-creation"),
    dtnaissance : document.getElementById("response-date-naissance"),
    content : document.getElementById("response-content"),
    country : document.getElementById("response-country")
  },
  markers: [],
  questions:[],
  previousQuestion: [],

  //Rajoute les question ainsi que leurs réponse dans le tableau questions
  addQuestions: function(json){
    for(i=0; i<json.length; i++){
      mapGestion.questions.push(json[i]);
    }
  },

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

  // generateHtmlInfoBulle:function(responseObj, questionObj){
  //   console.log(questionObj);
  //   var container = createElement("div", "container-info", {"data-responseId": responseObj.id});
  //   var question = createElement("p", "question");
  //   var response = createElement("p", "content");
  //   var author = createElement("p", "author");
  //   var date = createElement("p", "date");
  //
  //   // console.log(questionObj.themes)
  //
  //   var themes = [];
  //   var themesContainer = createElement("ul", "thematic-contain");
  //   for(var j=0; j<questionObj.themes.length; j++){
  //     themes.push(createElement("li", "themeItem"));
  //     themes[j].innerHTML = questionObj.themes[j].libTheme;
  //     themesContainer.appendChild(themes[j])
  //   }
  //
  //   question.innerHTML = questionObj.libQuestion;
  //   response.innerHTML = responseObj.content;
  //   author.innerHTML = responseObj.author;
  //   date.innerHTML = responseObj.dtcreation.timestamp;
  //
  //   container.appendChild(themesContainer);
  //   container.appendChild(question);
  //   container.appendChild(author);
  //   container.appendChild(date);
  //   container.appendChild(response);
  //
  //   return container.outerHTML;
  // },

  //Parcour les réponses et rajoutes les markers
  createAllMarkers:function(){
    for(i=0; i<mapGestion.questions.length; i++){
      for(j=0; j<mapGestion.questions[i].responses.length; j++){
        mapGestion.addMarkerFromResonse(mapGestion.questions[i].responses[j], mapGestion.questions[i]);
      }
    }
  },

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
      // infoBulle : new google.maps.InfoWindow({
      // 	content: mapGestion.generateHtmlInfoBulle(response, question)
      // })
      // parentNode : document.querySelector("*[data-responseid='"+response.id+"']").parentNode.parentNode.parentNode.parentNode
    }));

    // mapGestion.map.addListener('center_changed', function() {
    // // 3 seconds after the center of the map has changed, pan back to the
    // // marker.
    //   window.setTimeout(function() {
    //     mapGestion.map.panTo(mapGestion.markers[actualRank].getPosition());
    //   }, 3000);
    // });


    var actualRank = mapGestion.markers.length-1;
    google.maps.event.addListener(mapGestion.markers[actualRank], 'click', function() {
      if(mapGestion.testSidebarOpen()){
        mapGestion.closeSideBar();
        setTimeout(function(){
          mapGestion.updateSidebarContent(response, question);
          mapGestion.openSideBar();
        }, 1000);
      } else {
        mapGestion.updateSidebarContent(response, question);
        mapGestion.openSideBar();
      }
      mapGestion.map.panTo(mapGestion.markers[actualRank].getPosition());

    })
  },

  //Gere la sidebar sur le côté.
  updateSidebarContent:function(response, question){
    // question : document.getElementById("response-title"),
    // author : document.getElementById("response-author"),
    // dtcreation : document.getElementById("response-date-creation"),
    // dtnaissance : document.getElementById("response-date-naissance"),
    // content : document.getElementById("response-content"),
    // country : document.getElementById("response-country")
      mapGestion.elements.question.innerHTML = question.libQuestion;
      mapGestion.elements.author.innerHTML = response.author;
      mapGestion.elements.country.innerHTML = response.country.libCountry;
      mapGestion.elements.dtcreation.innerHTML = response.dtcreation.timestamp;
      mapGestion.elements.dtnaissance.innerHTML = response.dtnaissance.timestamp;
      mapGestion.elements.content.innerHTML = response.content;

  },

  testSidebarOpen: function(){
    if(mapGestion.sidebar.className.match("sidebar-open")){
      return true;
    } else {
      return false;
    }
  },

  closeSideBar:function(){
    mapGestion.sidebar.className = mapGestion.sidebar.className.replace("sidebar-open", "sidebar-close");
  },

  openSideBar:function(){
    mapGestion.sidebar.className = mapGestion.sidebar.className.replace("sidebar-close", "sidebar-open");
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
