window.onload = function(){
  var elements = document.getElementsByClassName("eachword")
  for (var i=0; i<elements.length; i++){
    elements[i].innerHTML = elements[i].innerHTML.replace(/\\b([a-z])([a-z]+)?\\b/gim, "<span class='flh'>$1</span>$2")
  }
}