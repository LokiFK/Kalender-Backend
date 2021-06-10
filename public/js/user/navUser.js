window.onload=function(){
  
  window.onclick = function(event) {
    if(document.getElementById("us").contains(event.target) && document.getElementById("dropdown").style.display != "block"){
      document.getElementById("dropdown").style.display = "block";
    } else if (document.getElementById("dropdown").style.display == "block" && !document.getElementById("dropdown").contains(event.target)) {
      document.getElementById("dropdown").style.display = "none";
    }
  }
  
  /*document.getElementById("us").addEventListener("click", dropdown);
  function dropdown(){
    document.getElementById("dropdown").style.display = "block";
  }*/

}  