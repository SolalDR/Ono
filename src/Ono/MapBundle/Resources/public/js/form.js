
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
      if(el){
        el.addEventListener("focus", function() {
            materializeForm.setLabelPosition(rank);
        }, false)
        el.addEventListener("blur", function() {
            materializeForm.setLabelPosition(rank, true);
        }, false)
      }
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
        if(materializeForm.elements.container.length){
          materializeForm.getElements();
          materializeForm.initEventsFocus();
          // materializeForm.initEventKey();
        }
    }
}



dynamicTag = {
  els: [],
  initEvent:function(){
    dynamicTag.addbutton.addEventListener("click", function(){
      dynamicTag.create();
      console.log("Bonjour");
    }, false)
  },
  create:function(){
    var id = dynamicTag.lgt;
    var formGroup = document.createElement("div");
    var label = document.createElement("label");
    var input = document.createElement("input");
    var buttonSupp = document.createElement("button");
    buttonSupp.className= "btn supp-tag";
    buttonSupp.innerHTML = "Supprimer";
    buttonSupp.setAttribute("data-id", id);
    input.setAttribute("required", "requires")
    input.className = "tag-control";
    input.name="article[tags]["+id+"][libTag]";
    formGroup.className = "tag-group";
    label.className="control-label required";
    label.innerHTML = "Tag : "+id;

    formGroup.appendChild(label);
    formGroup.appendChild(input);
    formGroup.appendChild(buttonSupp);

    dynamicTag.initSuppButtonEvent(buttonSupp);

    dynamicTag.container.appendChild(formGroup);

    dynamicTag.els.push({
      "group" : formGroup,
      "input" : input,
      "label": label,
      "suppEl" : buttonSupp,
      "id" : id
    });
    dynamicTag.lgt ++;

  },
  indent:function(){
    for(i=0; i<dynamicTag.els.length; i++){
      dynamicTag.els[i].input.name = dynamicTag.els[i].input.name.replace("article[tags]["+dynamicTag.els[i].id+"][libTag]", "article[tags]["+i+"][libTag]")
      dynamicTag.els[i].label.innerHTML = "Tag : "+i;
      dynamicTag.els[i].suppEl.setAttribute("data-id", i);
    }
  },
  initSuppButtonEvent:function(el){
    el.addEventListener("click", function(){
      var id = this.getAttribute("data-id");
      dynamicTag.els[id].group.remove();
      dynamicTag.els.splice(id, 1);
      dynamicTag.indent();
      dynamicTag.lgt --;
    }, false);
  },
  init:function(queryContainer){
    dynamicTag.container = document.querySelector(queryContainer);
    dynamicTag.queryId = queryContainer;
    if(dynamicTag.container){
      dynamicTag.lgt = dynamicTag.container.getElementsByTagName("input").length;
      dynamicTag.prototype = dynamicTag.container.getAttribute("data-prototype");
      dynamicTag.addbutton=document.getElementById("add_item_dynamic");
      if(dynamicTag.addbutton){
        dynamicTag.initEvent();
      }
    }
  }
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

materializeForm.init();
