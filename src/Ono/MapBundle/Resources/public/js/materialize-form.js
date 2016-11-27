
materializeForm = {
    elements: {
        input: [],
        label: []
    },

    // Récupére les éléments
    getElements: function() {
        for (i = 0; i < materializeForm.elements.container.length; i++) {
            materializeForm.elements.input.push(materializeForm.elements.container[i].getElementsByTagName("input")[0]);
            materializeForm.elements.label.push(materializeForm.elements.container[i].getElementsByTagName("label")[0]);
        }
    },

    //
    setReset:function(){
      for (i = 0; i < materializeForm.elements.label.length; i++) {
          if (!materializeForm.elements.input[i].value) {
              materializeForm.elements.label[i].className = "";
          }
      }
    },

    setFocus:function(rank){
      for (i = 0; i < materializeForm.elements.label.length; i++) {
          if (i === rank) {
              materializeForm.elements.label[i].className = "focus";
          } else if (!materializeForm.elements.input[i].value) {
              materializeForm.elements.label[i].className = "";
          }
      }
    },

    setLabelPosition: function(rank, reset) {
        if (reset) {
          materializeForm.setReset();
        } else {
          materializeForm.setFocus(rank);
        }
    },

    // Initialise un évenement de clique
    initEventClick: function(el, rank) {
        el.addEventListener("focus", function() {
            materializeForm.setLabelPosition(rank);
        }, false)
        el.addEventListener("blur", function() {
            materializeForm.setLabelPosition(rank, true);
        }, false)
    },
    // Parcours les input et initialise les éléments de click
    initEventsFocus: function() {
        for (i = 0; i < materializeForm.elements.input.length; i++) {
            materializeForm.initEventClick(materializeForm.elements.input[i], i)
        }
    },
    init: function() {
        materializeForm.elements.container = document.getElementsByClassName("textfieldContainer");
        if(!materializeForm.elements.container.length){
          materializeForm.elements.container = document.getElementsByClassName("form-group");
        }
        materializeForm.getElements();
        materializeForm.initEventsFocus();
        // materializeForm.initEventKey();
    }
}

materializeForm.init();
