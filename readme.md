# Web-Slides

## Über das Projekt
Das ist ein einfaches Freizeit-Projekt aus Spaß um bisschen mit MySQL, JSON und REST zu spielen. Gedacht ist es, dass X Clients auf eine Präsentations-Webseite gehen und ich über eine weitere Steuerungsseite einstellen kann, was diese zu sehen bekommen.

## TODO
- admin.php mit Passwort schützen!
- normales GET-Abfragen ohne Polling noch implementieren. Lohnt sich das überhaupt? Überdenken, vllt streichen...
- das mit den Popups verbessern

## Entwicklerinformationen
In Tabelle steht nur eine einzige Zeile, es ist nicht geplant mehrere zu haben.
Dort steht die aktuelle Slide-ID, die über REST abgefragt wird. So muss ich keine 
eigene XML/JSON/Whatever-Datei-Verwaltung schreiben und kann On-Update-Current-Timestamp nutzen.

## REST-Schnittstelle
#### GET:  REST-Schnittstelle
- **/rest.php** liefert das komplette, für die Anwendung relevante JSON
- **/rest.php?requestPolling=true** entsprechend mit HTTP-Polling

#### POST: REST-Schnittstelle
- **/rest.php** mit POST-Daten
```
Alle Daten updaten:
  admin=true&payload=%KOMPLETTES JSON%
    alias
  admin=true&whatToUpdate=all&payload=%KOMPLETTES JSON%
  
Nur command updaten:
  admin=true&whatToUpdate=command&payload=%KOMMANDO%
  
Nur options updaten:
  admin=true&whatToUpdate=options&payload=%OPTIONS_JSON_OHNE_OPTIONS_KEYWORD%
  
Nur presentationIdentifier updaten:
  admin=true&whatToUpdate=presentationIdentifier&payload=%PRESENTATION_IDENTIFIER%
```

### REST-JSON-Vorlage
Hier eine Vorlage für ein JSON, wie es der Client vom Server abruft. Das ist die verbindliche Vorlage,
 aus der die Funktionalitäten der App im Back- und Frontend ableiten lassen.

#####Hinweise:
- **refreshRate** ist die Anzahl der Millisekunden, nach denen im normalen GET-REST-Modus nach Aktualisierungen angefragt werden soll
- **allowUserNavigation** bedeutet, dass der User mit den Pfeiltasten durch die Präsentation navigieren darf
- **followServerCommands** gibt an, ob der Client auf z.B. "next"-Anweisungen vom Server reagieren soll oder nicht.
- **message** entweder *success* oder *error*
- **errorDetail** nur vorhanden, wenn message=="error"

Hilfreich, wenn der Nutzer die Präsentation selbst erkunden darf und man ihn dabei nicht stören möchte.
```json
{
    "message": "success",
    "errorDetail": "",
    "lastCommandDatetime": "2016-10-23 20:56:13",
    "command": "slide:5",
    "options": {
       "usePolling": true,
       "useRegularGet": true,
       "refreshRate": 500,
       "allowUserNavigation": false,
       "followServerCommands": true 
    }
}
```

##### Liste von Kommandos (für das JSON-"command"-Feld) die vom Front-End unterstützt werden (müssen)
- "force-refresh"
- "begin"
- "next"
- "back"
- "slide:([A-z0-9-:])+"