<?php
error_reporting(E_ALL);
?>
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

            border-radius: 3px;

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
        input#slide-id {
            display: block;
            margin: 0 auto;
            width: 94%;
            line-height: 80px;
            padding-top: 20px;
            padding-bottom: 20px;
            font-size: 20px;
            text-align: center;
            font-weight: 900;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 3px;

            -webkit-transition: all ease 0.3s;
            -moz-transition: all ease 0.3s ;
            -ms-transition: all ease 0.3s ;
            -o-transition: all ease 0.3s ;
            transition: all ease 0.3s ;
            border: 1px solid #ccc;
        }
    </style>
    <script>
        window.onload = function() {
            registerPresentationSelectListeners();

            document.querySelector("#start-button").addEventListener('click', function() {
                sendRestRequest('command', 'start');
            });
            document.querySelector("#back-button").addEventListener('click', function() {
                sendRestRequest('command', 'back');
            });
            document.querySelector("#next-button").addEventListener('click', function() {
                sendRestRequest('command', 'next');
            });
            document.querySelector("#jump-to-button").addEventListener('click', function() {
                sendRestRequest('command', 'slide:'+document.querySelector('#slide-id').value);
            });
            document.querySelector("#force-reload-button").addEventListener('click', function() {
                sendRestRequest('command', 'force-reload');
            });


            <!-- ACHTUNG DAS IS NOCH MEGA HÄSSLICH -->
            <!-- HIER WERDEN ALLE OPTIONS AUF DEFAULT GESTELLT -->
            document.querySelector("#disable-user-navigation-button").addEventListener('click', function() {
                sendRestRequest('options', JSON.stringify({"allowUserNavigation":false}));
            });
            document.querySelector("#allow-user-navigation-button").addEventListener('click', function() {
                sendRestRequest('options', JSON.stringify({"allowUserNavigation":true}));
            });
            document.querySelector("#show-trollface-button").addEventListener('click', function() {
                sendRestRequest('command', 'popup:trollface');
            });
        };
        function sendRestRequest(whatToUpdate, payload) {
            var params = "admin=true&whatToUpdate="+whatToUpdate+"&payload="+payload;
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
            xhttp.open("POST", "rest.php", true);
            //Send the proper header information along with the request
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
        function registerPresentationSelectListeners() {
            var options = document.querySelectorAll(".option-presentation");
            var select = document.querySelector("#select-presentation");
            select.addEventListener('change', function () {
                options.forEach(function(option) {
                   if (option.selected) {
                       sendRestRequest("presentationIdentifier", option.value);
                       //sendRestRequest("command", "force-reload");
                       sendRestRequest("command", "slide:1");
                   }
                });
            })
        }
    </script>
    <body>
        <div id="container">
            <div class="row clearfix">
                <div class="col">Präsentation auswählen:</div>
                <div class="col">
                    <select id="select-presentation">
                        <?php
                        $presentations = array();
                        $dir = dir(__DIR__.'/private/src/html/');

                        $activePresentation = 'default';
                        include __DIR__.'/private/php/pdoConfig.inc.php';
                        try { // Konnte Verbindung zu MySQL-Server herstellen
                            $pdo = new PDO($dsn, $mysqlCredentials->username, $mysqlCredentials->password, $opt);
                        } // MySQL-Server nicht erreichbar.
                        catch (PDOException $ex) {
                            $jsonData->setData("message", "error");
                            $jsonData->setData("error_detail", "couldn't connect to MySQL-Server");
                            die($jsonData->getJsonAsString());
                        }
                        # DATEN DER REST-ABFRAGE HOLEN
                        try {
                            $sql = file_get_contents(__DIR__.'/private/src/sql/getPresentationToDisplayIdentifier.sql');
                            $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
                            $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);
                            $stmt = $pdo->query($sql);
                            // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist relevant
                            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                                $activePresentation = $row[0];
                            }
                        } catch (PDOException $ex) {

                        }
                        if ($dir != null) {
                            while (false !== ($entry = $dir->read())) {
                                if (preg_match('/presentation-([A-z0-9-])+/', $entry)) {
                                    $entry = str_replace('presentation-', '', $entry);
                                    $entry = str_replace('.html', '', $entry);
                                    $presentations[] = $entry;
                                }
                            }
                            $dir->close();
                        };
                        if (count($presentations) > 0) {
                            foreach ($presentations as $presentation) {
                                echo '<option class="option-presentation" value="'.$presentation.'"';
                                if ($presentation == $activePresentation) {
                                    echo 'selected="selected"';
                                }
                                echo '>'.$presentation.'</option>';
                            }
                        } else {
                            echo '<option value="default">Default</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row clearfix">
                <button type="button" id="start-button">Start</button>
            </div>
            <div class="row clearfix">
                <div class="col">
                    <button type="button" id="back-button"><- Zurück</button>
                </div>
                <div class="col">
                    <button type="button" id="next-button">Weiter -></button>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col">
                    <input type="number" name="slide-id" id="slide-id" value="1"/>
                </div>
                <div class="col">
                    <button type="button" id="jump-to-button">Zur Seite springen</button>
                </div>
            </div>
            <div class="row clearfix">
                <button type="button" id="force-reload-button">Neu laden erzwingen</button>
            </div>
            <div class="row clearfix">
                <div class="col">
                    <button type="button" id="disable-user-navigation-button">DISABLE User Navigation</button>
                </div>
                <div class="col">
                    <button type="button" id="allow-user-navigation-button">ALLOW User Navigation</button>
                </div>
            </div>
            <div class="row clearfix">
                <button type="button" id="show-trollface-button">Show Trollface</button>
            </div>
        </div>
    </body>
</html>