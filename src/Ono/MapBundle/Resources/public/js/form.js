
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


function DynamicFormPrototype(args){
  if(args.form) {
    this.form = args.form;
    this.prototype = this.form.getAttribute("data-prototype");
  } else {
    return false;
  }
  if(args.label){
    this.label = args.label;
  }
  this.childrens = [];
  this.index = this.form.children.length ? this.form.children.length : 0;
  this.init();
}

DynamicFormPrototype.prototype.generatePrototype = function(){
  var prototype = this.prototype.replace(/__name__label__/g, this.label).replace(/__name__/g, this.index)
  var test = document.createElement("template");
  test.innerHTML = prototype;
  prototype = test.content.firstChild;
  prototype.className += " dynamic-form-group";
  this.form.appendChild(prototype);
  this.index++
  this.childrens.push(this.form.children[this.form.children.length-1]);
  this.generateUpdateButton(this.form.children[this.form.children.length-1]);
}

DynamicFormPrototype.prototype.generateAddButton = function(){
  var button = document.createElement("span");
  button.className = "btn";
  button.innerHTML = "Ajouter";
  this.button = button
  this.form.parentNode.appendChild(this.button);
}

DynamicFormPrototype.prototype.generateUpdateButton = function(el, notRemovable){
  var buttonDelete = document.createElement("span");
  var vichType = el.getElementsByClassName("vich-type");
  var distantType = el.getElementsByClassName("distant-type")[0];
  var fileType = vichType[0].firstChild;
  var deleteCheckBox = vichType[0].lastChild;
  var urlName = el.getElementsByClassName("url-type")[0];
  distantType.parentNode.parentNode.parentNode.className += " form-right";
  buttonDelete.className = "btn btn-danger";


  if(notRemovable) {
    vichType[0].remove();
    distantType.parentNode.parentNode.parentNode.className += " form-hide";
    urlName.className = urlName.className.replace("form-hide", "form-display");
    urlName.setAttribute("disabled", true);

  } else {
    distantType.onclick = function(){
      if(this.checked){
        urlName.className = urlName.className.replace("form-hide", "form-display");
        vichType[0].className = vichType[0].className.replace("form-display", "form-hide");
      } else {
        urlName.className = urlName.className.replace("form-display", "form-hide");
        vichType[0].className = vichType[0].className.replace("form-hide", "form-display");
      }
    }
  }

  buttonDelete.innerHTML = "";
  el.appendChild(buttonDelete);

  buttonDelete.onclick = function(){
    this.parentNode.remove();
  }
}

DynamicFormPrototype.prototype.init = function(){
  this.generateAddButton();
  this.initEvents();
  for(i=0; i<this.form.children.length; i++){
    this.form.children[i].className += " dynamic-form-group";
    this.generateUpdateButton(this.form.children[i], true);
    this.childrens.push(this.form.children[i]);
  }
}

DynamicFormPrototype.prototype.initEvents = function(){
  var self = this;
  this.button.addEventListener("click", function(e){
    e.preventDefault();
    self.generatePrototype();
    return false;
  }, false)
}

var dynamic =  new DynamicFormPrototype({
  form: document.getElementById("article_resources"),
  label: "Fichier"
});


dynamicTag = {
  els: [],
  initEvent:function(){
    dynamicTag.addbutton.addEventListener("click", function(){
      dynamicTag.create();
      console.log("Bonjour");
    }, false)
  },
  create:function(inputValue = null){
    var id = dynamicTag.lgt;
    var formGroup = document.createElement("div");
    var label = document.createElement("label");
    var input = document.createElement("input");
    if (inputValue){
      input.value = inputValue;
    }
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
  initChildren:function(){
    dynamicTag.lgt = 0;
    if(dynamicTag.container.getElementsByTagName("input")){
      var inputs = dynamicTag.container.getElementsByTagName("input");
      var value = [];
      for(i=0; i<inputs.length; i++){
        value.push(inputs[i].value)
      }
      dynamicTag.container.innerHTML = "";
      for(i=0; i<value.length; i++){
        dynamicTag.create(value[i]);
      }
    }
  },
  init:function(queryContainer){
    dynamicTag.container = document.querySelector(queryContainer);
    dynamicTag.queryId = queryContainer;
    if(dynamicTag.container){
      dynamicTag.initChildren()
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
