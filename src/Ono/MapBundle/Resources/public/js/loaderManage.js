var prefix = ["webkit", "o", "moz"];
function listenAnimationIteration(prefix){
  loaderManage.el.antenne.addEventListener(prefix+"Animationiteration", function(){
    loaderManage.animationStart = Date.now();
  }, false);
}

loaderManage = {
  loaderContainer : document.getElementById("loader"),
  loaderContainerManage : document.getElementById("loaderContent"),
  config: {
    duration : { //ms
      classic : 4200,
      speed : 4200,
      skew : 3000
    }
  },
  el: {
    antenne : document.getElementById("antenne"),
    interieur : document.getElementById("interieur"),
    exterieur : document.getElementById("exterieur"),
    oeil : document.getElementById("oeil")
  },
  button : {
    speed : document.getElementById("speedAction"),
    slow : document.getElementById("slowAction"),
    // out : document.getElementById("outAction"),
    skew : document.getElementById("skewAction"),
    simulateLoading: document.getElementById('simulateLoading')
  },

  setToOutAnimation:function(){
    for(el in loaderManage.el){
      loaderManage.el[el].className = loaderManage.el[el].className.replace(/speed|classic|skew/, "out");
    }
  },

  stopAnimation:function(){
    loaderManage.loaderContainerManage.style.opacity = 0;
    setTimeout(function(){
      document.body.removeChild(loaderManage.loaderContainerManage);
    }, 1600);
  },
  initEvent: function(){


    //Stocke à chaque itération d'animation, le début de cette dernière pour chaque prefix
    for(i=0; i<prefix.length; i++){
      listenAnimationIteration(prefix[i]);
    }
    loaderManage.el.antenne.addEventListener("animationiteration", function(){
      loaderManage.animationStart = Date.now();
      console.log(loaderManage.animationStart);
    }, false);


  },
  init : function(){
    loaderManage.initEvent();
    loaderManage.animationStart = Date.now();
  }
}

loaderManage.init();
