window.onload=function(){
    var appointments = {{ appointments }};

    var table = document.getElementById('appointmentsTable');

    var start = new Date('2019-06-11T08:00');
    var start = start.getHours()*60+start.getMinutes();
    var now = new Date();
    var now = now.getHours()*60+now.getMinutes();
    var end = new Date('2019-06-11T21:00');               //Beispielhaft
    var end = end.getHours()*60+end.getMinutes();
    var zeitleiste = document.createElement("tr"); 
    
    var intervall = 15;
    var zeit = document.createElement("td"); 
    zeit.classList.add("date");
    zeitleiste.appendChild(zeit);
    for(var i=start; (i%intervall)!=0; i++){
        var zeit = document.createElement("td"); 
        zeit.classList.add("shortTd");
        zeitleiste.appendChild(zeit);
    }
    for(var i=(start+intervall-1)-((start+intervall-1)%intervall); i<end; i=i+intervall){
        var zeit = document.createElement("td");
        zeit.colSpan=intervall;
        zeit.classList.add("td");
        zeit.innerHTML = (Math.floor(i/60))+":"+(i%60);
        zeitleiste.appendChild(zeit);
    }
    table.appendChild(zeitleiste);

    var date = null;
    var eDate = document.createElement("tr");
    var minutes=start;
    for(var i = 0; i<appointments.length; i++){
        var a=appointments[i];
        var aDate = a.start.substring(0,10);
        if(date==null || date!=aDate){
            if(date!=null){
                while(minutes<end){
                    var termin = document.createElement("td"); 
                    termin.classList.add("shortTd");
                    eDate.appendChild(termin);
                    minutes++;
                }
                minutes=aStart;
                table.appendChild(eDate);
                eDate = document.createElement("tr");
            }
            date=aDate;
            var dateTd = document.createElement("td"); 
            dateTd.classList.add("date");
            dateTd.innerHTML=date;
            eDate.appendChild(dateTd);
        }
        var aStart = a.start.substring(11);
        var aEnd = a.end.substring(11);
        aStart = parseInt(aStart.substring(0,2))*60+parseInt(aStart.substring(3).substring(0,2));
        aEnd = parseInt(aEnd.substring(0,2))*60+parseInt(aEnd.substring(3).substring(0,2));
        //alert("m: "+minutes+" s: "+aStart);
        while(minutes<aStart){
            var termin = document.createElement("td"); 
            termin.classList.add("shortTd");
            eDate.appendChild(termin);
            minutes++;
        }
        var termin = document.createElement("td"); 
        termin.colSpan = (aEnd-aStart);
        termin.innerHTML="Raum: "+a.number+"<br>Patient: "+a.lastname+"<br>Behandlung: "+a.name;
        eDate.appendChild(termin);
        minutes=aEnd;
    }
    //alert("minutes: "+minutes+" end: "+end);
    while(minutes<end){
        var termin = document.createElement("td"); 
        termin.classList.add("shortTd");
        eDate.appendChild(termin);
        minutes++;
    }
    table.appendChild(eDate);
}