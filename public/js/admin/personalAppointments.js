window.onload=function() {
    let termin;
    let i;
    let appointments = {{ appointments }};

    let table = document.getElementById('appointmentsTable');

    let start = new Date('2019-06-11T08:00');
    start = start.getHours() * 60 + start.getMinutes();
    let now = new Date();
    now = now.getHours() * 60 + now.getMinutes();
    let end = new Date('2019-06-11T21:00');               //Beispielhaft
    end = end.getHours() * 60 + end.getMinutes();
    const zeitleiste = document.createElement("tr");

    const interval = 15;
    let zeit = document.createElement("td");
    zeit.classList.add("date");
    zeitleiste.appendChild(zeit);
    for(i = start; (i%interval)!==0; i++){
        zeit = document.createElement("td");
        zeit.classList.add("shortTd");
        zeitleiste.appendChild(zeit);
    }
    for(i = (start+interval-1)-((start+interval-1)%interval); i<end; i=i+interval){
        zeit = document.createElement("td");
        zeit.colSpan=interval;
        zeit.classList.add("td");
        zeit.innerHTML = (Math.floor(i/60))+":"+(i%60);
        zeitleiste.appendChild(zeit);
    }
    table.appendChild(zeitleiste);

    let date = null;
    let eDate = document.createElement("tr");
    let minutes = start;
    for(i = 0; i<appointments.length; i++){
        let aStart;
        const a = appointments[i];
        const aDate = a.day;
        if(date==null || date!==aDate){
            if(date!=null){
                while(minutes<end){
                    termin = document.createElement("td");
                    termin.classList.add("shortTd");
                    eDate.appendChild(termin);
                    minutes++;
                }
                minutes=aStart;
                table.appendChild(eDate);
                eDate = document.createElement("tr");
            }
            date=aDate;
            const dateTd = document.createElement("td");
            dateTd.classList.add("date");
            dateTd.innerHTML=date;
            eDate.appendChild(dateTd);
        }
        aStart = a.start;
        let aEnd = a.end;
        aStart = parseInt(aStart.substring(0,2))*60+parseInt(aStart.substring(3).substring(0,2));
        aEnd = parseInt(aEnd.substring(0,2))*60+parseInt(aEnd.substring(3).substring(0,2));
        //alert("m: "+minutes+" s: "+aStart);
        while(minutes<aStart){
            termin = document.createElement("td");
            termin.classList.add("shortTd");
            eDate.appendChild(termin);
            minutes++;
        }
        termin = document.createElement("td");
        termin.colSpan = (aEnd-aStart);
        termin.className = "termin"
        termin.innerHTML="Raum: "+a.number+"<br>Patient: "+a.lastname+"<br>Behandlung: "+a.name;
        eDate.appendChild(termin);
        minutes=aEnd;
    }
    //alert("minutes: "+minutes+" end: "+end);
    while(minutes<end){
        termin = document.createElement("td");
        termin.classList.add("shortTd");
        eDate.appendChild(termin);
        minutes++;
    }
    table.appendChild(eDate);
}