var REST_URL = "rest.php";
var REST_URL_POLLING = "rest.php?requestPolling=true";

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
    if (activeSlideIdentifier == id) return false;

    // Beim aller ersten Seitenaufruf wird noch nichts angezeigt
    // id daher == -1
    if (activeSlideIdentifier != -1) {
        // vorheriges verstecken
        document.querySelector("#slide-"+activeSlideIdentifier).classList.toggle("visible");
    }
    var elem = document.querySelector("#slide-"+id);
    if (elem == undefined) {
        console.error("Ungültige Slide-ID!");
        return false;
    }
    document.querySelector("#slide-"+id).classList.toggle("visible");
    activeSlideIdentifier = id;
};
function showSlideByCommand(command) {
    var nextSlideIdentifier = -1;// default

    console.log("command: "+command);

    // wenn die gleiche Seite erneut angeschaut werden soll, abbrechen
    if (command == activeSlideIdentifier) return;
    // derzeit unwahrscheinlich aber so ist die anwendung für die zukunft gerüstet
    if (command == "skip") return;
    if (command == "force-reload") {
        location.reload(true);
        return;
    }
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
        return false;
    }
    showSlideById(nextSlideIdentifier);
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
function restDataHandler(responseText) {

    var responseJson;
    try {
        responseJson = JSON.parse(responseText);
    } catch (ex) {
        console.error("invalid JSON response!");
        console.dir(ex);
        return false;
    }


    console.log("referenceDatetimeLocal: "+restData.referenceDatetime);
    console.log("referenceDatetimeServer: "+responseJson.referenceDatetime);

    if (!restData.options.followServerCommands) {
        console.log("Es kam ein neues Kommando rein, aber followServerCommands ist false!");
    }

    if (responseJson.message == "success") {
        // Gab eine Veränderung
        if (responseJson.referenceDatetime != restData.referenceDatetime) {
            console.log("REST-Daten empfangen, Veränderung!");
            restData = responseJson;
            showSlideByCommand(restData.command);

            // Beim Polling muss nach dem Ende einer Anfrage eine neue gestartet werden!
            if (restData.options.usePolling) {
                getRestData(REST_URL_POLLING);
            }
        }
    }
    else {
        console.error("Server gab \"error\" zurück");
        if (responseJson.errorDetail != undefined) {
        console.error("Error-Details: "+responseJson.errorDetail);
        }
    }
}
function getRestData(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            restDataHandler(this.responseText);
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
};
function setRestDataListener() {
    if (restData.options.usePolling) {
        getRestData(REST_URL_POLLING);
    }
}
function registerWindowResizeListener() {
    window.onresize = function() {
        calc16to9SlideDimension();
    }
}

window.onload = function() {
    calcSlideCount();
    registerWindowResizeListener();
    calc16to9SlideDimension();
    //showSlideById(1);// Startseite
    registerKeyListener();
    getRestData(REST_URL);
    //setRestDataListener();
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