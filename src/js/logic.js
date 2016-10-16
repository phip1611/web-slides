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

    // das kann nur sein, wenn der Server warum auch immer -1 sendet
    // sozusagen übervorsichtiges Error-Handling :D
    if (id == -1) return false;
    var elem = document.querySelector("#slide-"+id);
    if (elem == undefined) {
        console.error("Ungültige Slide-ID!");
        return false;
    }

    // Beim aller ersten Seitenaufruf wird noch nichts angezeigt
    // id daher == -1 (lokaler default wert)
    if (activeSlideIdentifier != -1) {
        // vorheriges verstecken
        document.querySelector("#slide-"+activeSlideIdentifier).classList.toggle("visible");
    }
    elem.classList.toggle("visible");
    activeSlideIdentifier = id;
};
function commandHandler(command) {
    // BUGFIX
    //  aus irgend einem Grund wurden ID's plötzlich konkateniert
    //  statt adiert, das hier fixt es...
    activeSlideIdentifier = parseInt(activeSlideIdentifier);

    if (command == activeSlideIdentifier) return false;
    if (!restData.options.followServerCommands) return false;

    // beim Laden der Seite // bevor irgendeine Slide zu sehen ist
    if (activeSlideIdentifier == -1) {
        // Wenn nicht explizit eine slide-id angegeben wird
        // beim ersten anschauen der Seite, dann wird die Slide 1 angezeigt
        if (!(/^(slide:)([A-z0-9-]+)$/.test(command))) {
            showSlideById(1);
            return true;
        }
    }
    else {
        if (/^(slide:)([A-z0-9-]+)$/.test(command)) {
            showSlideById(command.split("slide:")[1]);
        }
        else if (command == "force-reload" || command == "force-refresh") {
            location.reload(true);
        }
        // Fallback, da es das pause-Command mal gab
        else if (command == "pause" || command == "begin" || command == "start") {
            showSlideById(1);
        }
        else if (command == "next" && activeSlideIdentifier < slideCount) {
            showSlideById(activeSlideIdentifier+1);
        }
        else if (command == "back" && activeSlideIdentifier > 1) {
            showSlideById(activeSlideIdentifier-1);
        }
        else if (/^(popup:)([A-z0-9-]+)$/.test(command)) {
            showPopup(command.split("popup:")[1]);
        }
        else if (command == "skip") {
            return false;
        }
        else {
            console.error("Unbekanntes Server-Kommando: "+command);
            return false;
        }
    }
};
function registerKeyListener() {
    window.onkeyup = function(e) {
        if (restData.options.allowUserNavigation) {
            // left arrow
            if (e.keyCode == 37) {
                commandHandler("back");
            }
            // right arrow
            else if (e.keyCode == 39) {
                commandHandler("next");
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

    if (responseJson.message == "success") {
        // Gab eine Veränderung
        if (responseJson.referenceDatetime != restData.referenceDatetime) {
            console.log("REST-Daten empfangen, Veränderung!");
            restData = responseJson;
            commandHandler(restData.command);
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
function showPopup(id) {
    var elem = document.querySelector("#popup-"+id);
    var elem2;
    if (elem == undefined) return false;
    if (activeSlideIdentifier != -1) {
        elem2 = document.querySelector("#slide-"+activeSlideIdentifier);
        elem2.classList.toggle("visible");
    }
    elem = document.querySelector("#popup-"+id);
    elem.classList.toggle("visible");
    elem.addEventListener('click', function () {
        console.log("hide popup");
        elem.classList.toggle("visible");
        if (elem != undefined) {
            elem2.classList.toggle("visible");
        }
    })
}
function hideAllPopups() {
    var elements = document.querySelectorAll(".popup");
}

window.onload = function() {
    calcSlideCount();
    registerWindowResizeListener();
    calc16to9SlideDimension();
    getRestData(REST_URL); // aktuelle Kinfoguration abrufen + aktuelles Kommando
    registerKeyListener();
    //setRestDataListener();
};