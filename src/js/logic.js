var defaultData = {
    "command": "slide:1",
    "referenceDatetime": "",
    "options": {
        "usePolling": true,
        "useRegularGet": true,
        "refreshRate": 500,
        "allowUserNavigation": true,
        "followServerCommands": true
    }
};
var restData = defaultData;
var activeSlideIdentifier = -1;

function calcSlideCount() {
    slideCount = document.querySelectorAll(".slide").length;
};
function calc16to9SlideDimension() {
    var outerMargin = 14; // Nur für Links und Rechts

    var windowAvailableWidth = document.querySelector('body').clientWidth;
    var windowAvailableHeight = document.querySelector('body').clientHeight;
    var slideWidth, slideHeight;

    // Verfügbare Breite minus der abstand der links und Rechts sein soll = Breite der Slide!
    slideWidth  = windowAvailableWidth-2*outerMargin;
    slideHeight = slideWidth/16*9;
    if (slideHeight+2*outerMargin > windowAvailableHeight) {
        slideHeight = windowAvailableHeight-2*outerMargin;
        slideWidth = slideHeight/9*16;
    }

    document.querySelector('#container').style.margin = outerMargin+"px auto"
    document.querySelector('#container').style.height = slideHeight;
    document.querySelector('#container').style.width  = slideWidth;
};
function showSlideById(id) {
    // Der aller erste Seitenaufruf, es wird noch nicths angezeigt
    if (activeSlideIdentifier == -1) {
        document.querySelector("#slide-"+id).classList.toggle("visible");
    } else {
        // vorheriges verstecken
        document.querySelector("#slide-"+activeSlideIdentifier).classList.toggle("visible");
        // aktuelels anzeigen
        document.querySelector("#slide-"+id).classList.toggle("visible");
    }
    activeSlideIdentifier = id;
};
function showSlideByCommand(command) {
    var nextSlideIdentifier = -1;// default

    // wenn die gleiche Seite erneut angeschaut werden soll, abbrechen
    if (command == activeSlideIdentifier) return;
    // derzeit unwahrscheinlich aber so ist die anwendung für die zukunft gerüstet
    if (command == "skip") return;
    // Fallback, da es das pause-Command mal gab
    if (command == "pause") {
        nextSlideIdentifier = 1;
    }
    else if (command == "begin" || command == "start") {
        nextSlideIdentifier = 1;
    }
    else if (/^(slide:)([A-z0-9-]+)$/.test(command)) {
        nextSlideIdentifier = command.split("slide:")[1];
    }
    else if (command == "next" && activeSlideIdentifier < slideCount) {
        nextSlideIdentifier = activeSlideIdentifier+1;
    }
    else if (command == "back" && activeSlideIdentifier > 1) {
        nextSlideIdentifier = activeSlideIdentifier-1;
    }
    else {
        return;
    }

    // zu dev zwecken
    if (nextSlideIdentifier == -1) {
        console.error("nextSlideIdentifier ist -1, das sollte aber nicht passieren!");
    }

    // vorheriges verstecken
    document.querySelector("#slide-"+activeSlideIdentifier).classList.toggle("visible");
    // aktuelels anzeigen
    document.querySelector("#slide-"+nextSlideIdentifier).classList.toggle("visible");

    activeSlideIdentifier = nextSlideIdentifier;
};
function registerKeyListener() {
    window.onkeyup = function(e) {
        if (restData.options.allowUserNavigation) {
            // left arrow
            if (e.keyCode == 37) {
                showSlideByCommand("back");
            }
            // right arrow
            else if (e.keyCode == 39) {
                showSlideByCommand("next");
            }
        }
    }
};

/*
 TODO
   - <strike>Wenn im Polling-Modus, dann wird nach erhaltener antwrort keine weitere Anfrage gestellt</strike>
   - Es werden zwei Polling Request Anfragen gleichzeitig gesendet, das ist nicht okay!
   - wenn man serverseitig eine Änderung des netzwerkmethode im laufendne betrieb vornimmt dann
     passiert auch was blödes

 */

function getRestData(usePolling) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var responseJson = JSON.parse(this.responseText);
            // Gab eine Veränderung
            if (responseJson.message == "success") {
                // aktuelels datum != vorheriges datum
                if (responseJson.referenceDatetime != restData.referenceDatetime) {
                    console.log("REST-Daten empfangen, Veränderung!");
                    restData.referenceDatetime = responseJson.referenceDatetime;
                    // Wenn es eine Veränderung des Kommandos gab
                    if (responseJson.comand != restData.command) {
                        showSlideByCommand(responseJson.command);
                    }
                    if (responseJson.options != restData.options) {
                        restData.options = responseJson.options;
                    }
                }
            }
            else {
                console.error("Server gab \"error\" zurück");
                if (responseJson.errorDetail != undefined) {
                    console.error("Error-Details: "+responseJson.errorDetail);
                }
            }


            if (restData.options.usePolling) {
                // die nächste anfrage starten!
                getRestData(true);
            }
        }
    };
    if (usePolling) {
        xhttp.open("GET", "rest.php?requestPolling=true", true);
    }
    else {
        xhttp.open("GET", "rest.php", true);
    }
    xhttp.send();
};
function setRestDataListener() {
    if (restData.options.usePolling) {
        getRestData(true);
    }
    else {
        window.setInterval(function() {
            getRestData(false);
        }, restData.options.refreshRate);
    }

    /*var internalCallback = function() {
        window.setTimeout(function() {

        }, restData.options.refreshRate);
    };*/
}

window.onload = function() {
    calcSlideCount();
    calc16to9SlideDimension();
    showSlideById(1);// Startseite
    registerKeyListener();
    getRestData(false);
    setRestDataListener();
};




















/*OLD*/


// Init vars
/*
var currentlyShownSlideIdentifier = '';
var nextSlideToShowIdentifier = '';
var lastRestDataTimestamp = '';
var slideCount = 0; // without the default Pause slide
var options = {
    allowUserNavigation: true
};

window.onload = function() {
    calcSlideCount();
    // Starteinstellung
    currentlyShownSlideIdentifier = "1";
    showSlide(1);
    calc16to9SlideDimension();
    // Sofort Daten abfragen
    //getRestData();
    // alle 500ms weitere  Daten abfragen
    /*setInterval(function(){
        getRestData();
     }, 500);*//*
    window.onresize = function () {
        calc16to9SlideDimension();
    }
    processOptions();
};

function processOptions() {
    /* Key-Navigation through the slides *//*
    window.onkeyup = function(e) {
        if (options.allowUserNavigation) {
            // left error
            if (e.keyCode == 37) {
                showSlide("back");
            }
            else if (e.keyCode == 39) {
                showSlide("next");
            }
        }
    }
}




function getRestData() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var responseJson = JSON.parse(this.responseText);
            // Gab Veränderung
            console.log("lastRestDataTimestamp:"+lastRestDataTimestamp);
            console.log("updated last from server:"+responseJson.lastUpdated);
            if (responseJson.lastUpdated != lastRestDataTimestamp) {
                console.log("REST-Daten empfangen, Veränderung!");
                lastRestDataTimestamp = responseJson.lastUpdated;
                if (responseJson.message == "success") {
                    showSlide(responseJson.command);
                }
            } else {
                console.log("REST-Daten empfangen, KEINE Veränderung!");
            }
        }
    };
    xhttp.open("GET", "rest.php", true);
    xhttp.send();
}*/