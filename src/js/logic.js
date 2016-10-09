var slideIdentifier = "pause";
var slideCount = 1; // without the default Pause slide

window.onload = function() {
        // determine how many slides there are
    slideCount = document.querySelectorAll(".slide").length;

    /*setInterval(function(){
     getRestData();
     }, 500);*/

    window.onresize = function () {
        calcSlideHeight();
    }
};

function calcSlideHeight() {
    var outerMargin = 14; // Nur für Links und Rechts

    var windowAvailableWidth = document.querySelector('body').clientWidth;
    var windowAvailableHeight = document.querySelector('body').clientHeight;
    var slideWidth, slideHeight;
    slideWidth = windowAvailableWidth-2*outerMargin; // da links und rechts 14px Margin

    // Fenster nicht hoch genug um bei voller Breite 16:9 darzustellen
    // von Höhe ausgehend Breite berechnen

    console.log("Verfügbare Breite: "+windowAvailableWidth);
    console.log("Verfügbare Höhe: "+windowAvailableHeight);

    // Höhe ist ausreichend um bei dieser Breite eine auf die volle breite gespannte Slide anzuzeigen
    if ((windowAvailableWidth-2*outerMargin)/16*9 > windowAvailableHeight) {
        slideWidth = windowAvailableWidth;
        slideHeight = slideWidth/16*9;
    } else {
        slideHeight = windowAvailableHeight-2*outerMargin;
        slideWidth = slideHeight/9*16;
    }

    // Verfügbare Breite minus der abstand der links und Rechts sein soll = Breite der Slide!
    slideWidth  = windowAvailableWidth-2*outerMargin;
    slideHeight = slideWidth/16*9;
    if (slideHeight+2*outerMargin > windowAvailableHeight) {
        console.error(1897130);
        slideHeight = windowAvailableHeight-2*outerMargin;
        slideWidth = slideHeight/9*16;
    }

    console.log("Berechnete Breite: "+slideWidth);
    console.log("Berechnete Höhe: "+slideHeight);

    document.querySelector('#container').style.margin = outerMargin+"px auto"
    document.querySelector('#container').style.height = slideHeight;
    document.querySelector('#container').style.width  = slideWidth;
}

function getRestData() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var responseJson = JSON.parse(this.responseText);
            if (responseJson.message == "success") {
                showSlide(responseJson.currentPageIdentifier);
            }
        }
    };
    xhttp.open("GET", "rest_get.php", true);
    xhttp.send();
}

function showSlide(identifier) {
    if (identifier == "start") {
        identifier = 1;
    }
    // vorheriges verstecken
    console.log("#slide-"+slideIdentifier);
    document.querySelector("#slide-"+slideIdentifier).classList.toggle("visible");
    document.querySelector("#slide-"+identifier).classList.toggle("visible");
    slideIdentifier = identifier;
}