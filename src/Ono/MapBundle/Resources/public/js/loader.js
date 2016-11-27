
window.onload = function(){
    setTimeout(function(){
      if(typeof mapGestion !== "undefined" ){
        mapGestion.createAllMarkers();
      }
    }, 1000)
    loaderManage.stopAnimation();
}
