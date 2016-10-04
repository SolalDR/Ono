
document.body.onload = function(){
  setTimeout(function(){
    mapGestion.createAllMarkers();
  }, 1000)
  setTimeout(function(){
    loaderManage.stopAnimation();
  }, 1200)
}
