## Teatro Marigliano
Progetto universitario per la creazione di un prototipo di sito pensato come soluzione alla digitalizzazione di un processo aziendale.

Per far partire questo progetto sulla propria macchina, sono necessari due passaggi.
Primo, bisogna clonare main da questa repo in una cartella della vostra macchina utilizzando git,
Secondo, bisogna installare Docker e far partire il comando "make install" in un terminale aperto nella directory del progetto.

Dopo di questo, sarà possibile usare il comando "make up" seguito da "make build" e "make dev" per far partire il server.
Una volta partito il server, si può navigare su http://localhost per visualizzare il sito.

Utenze per testare:
Utenza admin
email: admin@gmail.com
password: password

Utenza membro
email: member@gmail.com
password: password

Per visualizzare le notifiche email una volta che una prenotazione avviene, si può navigare a http://localhost:8025
Si può usare il comando "make test" per eseguire la suite di test creati nel progetto.
