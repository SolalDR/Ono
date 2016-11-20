
document.body.onload = function(){
  setTimeout(function(){
    if(typeof mapGestion != "undefined" ){
      mapGestion.createAllMarkers();
    }
  }, 1000)
  setTimeout(function(){
    loaderManage.stopAnimation();
  }, 1200)
}
console.log("Load");

window.onload = function(){
    setTimeout(function(){
      if(typeof mapGestion != "undefined" ){
        mapGestion.createAllMarkers();
      }
    }, 1000)
    setTimeout(function(){
      loaderManage.stopAnimation();
    }, 1200)
}
