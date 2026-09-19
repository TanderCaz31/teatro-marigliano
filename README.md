## Teatro Marigliano
Progetto universitario per la creazione di un prototipo di sito pensato come soluzione alla digitalizzazione di un processo aziendale.

Per far partire questo progetto sulla propria macchina, sono necessarie poche parti da installare:\
git\
Docker Desktop con integrazione WSL2 backend su Windows. Porte 80, 5173 e 8025 devono essere libere.\
Non è necessario installare php, composer, o Node.

Per installare la repo in se, seguite questi step:\
Primo, bisogna clonare main da questa repo in una cartella della vostra macchina utilizzando git;\
Secondo, bisogna installare Docker e far partire il comando "make install" in un terminale aperto nella directory del progetto.

Al termine dell'installazione il sito sarà attivo e visuallizzabile navigando su http://localhost.\
Si può usare "make down" per disattivare i container Docker e "make up" per riattivarli in caso sia necessario.

Utenze per testare:\
Utenza admin\
email: admin@gmail.com\
password: password

Utenza membro\
email: member@gmail.com\
password: password

Per visualizzare le notifiche email una volta che una prenotazione avviene, si può navigare a http://localhost:8025\
Si può usare il comando "make test" per eseguire la suite di test creati nel progetto.

Per ottenere una versione del database con nuovi dati, si può usare "make freshseed": un comando che fa partire le migrazioni del progetto e i seeder per popolare le tabelle-
