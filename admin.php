<html>
    <head>
        <title>Web-Slides: Administration</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
    </head>
    <style>
        .clearfix:after {
            visibility: hidden;
            display: block;
            font-size: 0;
            content: " ";
            clear: both;
            height: 0;
        }
        .clearfix { display: inline-block; }
        /* start commented backslash hack \*/
        * html .clearfix { height: 1%; }
        .clearfix { display: block; }
        /* close commented backslash hack */

        html,body,div,button {
            margin:0;
            padding:0;
            box-sizing: border-box;
            border: 0;
            font-size: 14px;
            font-family: sans-serif;
            color: #333;
            line-height:1em;
        }
        body {
            background-color: #eee;
        }

        #container {
            margin: 0 auto;
            padding: 7px;
            background-color: #fff;
            margin-top: 10px;
            box-shadow: 0px 0px 10px 3px #ccc;
        }



        @media (max-width:579px) {
            #container {
                width: 95%;
            }
        }
        @media (min-width:950px) {
            #container {
                width: 45%;
            }
        }
        @media (min-width:580px) and (max-width:949px) {
            #container {
                width: 65%;
            }
        }

        .row {
            width: 100%;
            margin: 14px 0;
        }
        .col {
            width: 50%;
            float: left;
        }
        button {
            display: block;
            margin: 0 auto;
            width: 94%;
            line-height: 120px;
            font-size: 20px;
            text-align: center;
            font-weight: 900;
            background-color: #eeeeee;

            -webkit-transition: all ease 0.3s;
            -moz-transition: all ease 0.3s ;
            -ms-transition: all ease 0.3s ;
            -o-transition: all ease 0.3s ;
            transition: all ease 0.3s ;
        }
        button:hover, button:focus{
            background-color: #00ffff;
            box-shadow: 0px 0px 10px 3px #ccc;
            cursor: pointer;
            border: 0;
            outline: 0;
        }
    </style>
    <script>
        window.onload = function() {
            document.querySelector("#start-button").addEventListener('click', function() {
                sendRestRequest('start');
            });
            document.querySelector("#pause-button").addEventListener('click', function() {
                sendRestRequest('pause');
            });
            document.querySelector("#back-button").addEventListener('click', function() {
                sendRestRequest('back');
            });
            document.querySelector("#next-button").addEventListener('click', function() {
                sendRestRequest('next');
            });
        };
        function sendRestRequest(action) {
            var params = "payload={action="+action+"}";
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.dir(this.responseText);
                    if (JSON.parse(this.responseText).message == "success") {
                        console.log("REST-Daten erfolgreich abgesendet");
                    } else {
                        console.log("REST-Daten ohne Erfolg abgesendet");
                    }
                }
            };
            xhttp.open("POST", "rest_post.php", true);
            //Send the proper header information along with the request
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(params);
            console.log("Action:"+action+" an Server gesendet");
        }
    </script>
    <body>
        <div id="container">
            <div class="row clearfix">
                <div class="col">
                    <button type="button" id="start-button">Start</button>
                </div>
                <div class="col">
                    <button type="button" id="pause-button">Pause</button>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col">
                    <button type="button" id="back-button"><- Zurück</button>
                </div>
                <div class="col">
                    <button type="button" id="next-button">Weiter -></button>
                </div>
            </div>
        </div>
    </body>
</html>