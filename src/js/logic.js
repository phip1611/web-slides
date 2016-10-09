var currentlyShownSlideIdentifier = '';
var nextSlideToShowIdentifier = '';
var lastRestDataTimestamp = '';
var slideCount = 1; // without the default Pause slide

window.onload = function() {
    // determine how many slides there are
    slideCount = document.querySelectorAll(".slide").length -1 /*Pause-Seite zählt nicht in die numerische Zählung der #IDS mit rein*/;
    // Starteinstellung
    currentlyShownSlideIdentifier = "pause";
    calcSlideHeight();
    getRestData();

    setInterval(function(){
     getRestData();
     }, 500);
    window.onresize = function () {
        calcSlideHeight();
    }
};

function calcSlideHeight() {
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
                    showSlide(responseJson.currentPageIdentifier);
                }
            } else {
                console.log("REST-Daten empfangen, KEINE Veränderung!");
            }
        }
    };
    xhttp.open("GET", "rest_get.php", true);
    xhttp.send();
}

function showSlide(identifier) {
    // wenn die gleiche Seite erneut angeschaut werden soll, abbrechen, sonst würde man die ausblendenf
    if (identifier == currentlyShownSlideIdentifier) return;

    // in wenigen Fällen kann der Server auch mal sagen jetzt soll nichts passieren // die Anweisung werden!
    if (identifier == "skip") return;
    else if (identifier == "pause") {
        nextSlideToShowIdentifier = identifier;
    }
    else if (identifier == "next" && currentlyShownSlideIdentifier == "pause") {
        nextSlideToShowIdentifier = 1;
    }
    else if (identifier == "next" && currentlyShownSlideIdentifier < slideCount) {
        nextSlideToShowIdentifier = currentlyShownSlideIdentifier+1;
    }
    else if (identifier == "back" && currentlyShownSlideIdentifier > 1) {
        nextSlideToShowIdentifier = currentlyShownSlideIdentifier-1;
    }
    else if (identifier == "back" && currentlyShownSlideIdentifier == "pause") {
        return;
    }
    else if (identifier == "start") {
        nextSlideToShowIdentifier = 1;
    }
    else if (identifier >= 1 && identifier <= slideCount) {
        nextSlideToShowIdentifier = identifier;
    } else {
        return;
    }


    // vorheriges verstecken
    document.querySelector("#slide-"+currentlyShownSlideIdentifier).classList.toggle("visible");
    // aktuelels anzeigen
    document.querySelector("#slide-"+nextSlideToShowIdentifier).classList.toggle("visible");

    currentlyShownSlideIdentifier = nextSlideToShowIdentifier;
}