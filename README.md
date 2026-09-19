## Teatro Marigliano
Progetto universitario per la creazione di un prototipo di sito pensato come soluzione alla digitalizzazione di un processo aziendale.

### Per dare la possibilità all'esaminatore che leggerà questo messaggio di giudicare in modo completo l'elaborato ANCHE se non risulti possibile l'installazione del progetto, Nel report finale è presente un link ad un video che mostra tutte le funzionalità del sito che sia un membro che un admin potrebbe utilizzare.

Per far partire questo progetto sulla propria macchina, sono necessarie poche parti da installare:\
git.\
Docker Desktop.\
Nota bene: Su Windows i comandi di cui parlerò vanno eseguiti da un terminale WSL2 (Ubuntu), dove make è disponibile e Docker Desktop deve avere l'integrazione WSL attiva per quella distribuzione. Su macOS o Linux si può utilizzare il terminale di sistema.\
Non è necessario installare php, composer, o Node.

Nel caso in cui non risulti possibile utilizzare i comandi make, è possibile consultare il file "Makefile" di questa repo per vedere esattamente i comandi di cui make fa da alias.\
È possibile utilizzare quei comandi invece di "make install" se necessario.

Per installare la repo in sé, seguite questi step:\
Primo, bisogna clonare main da questa repo in una cartella della vostra macchina utilizzando git. Se possibile, clonare in WSL stesso permette per load time più veloci;\
Secondo, aprire Docker Desktop e aspettare che il Docker Engine si avvii.\
Terzo, far partire il comando "make install" in un terminale Ubuntu aperto nella directory del progetto. La porta 80 deve essere libera.\
Nota bene: la prima installazione è quella che impiega più tempo, anche alcuni minuti.

Al termine dell'installazione il sito sarà attivo e visualizzabile navigando su http://localhost. Si dovrebbe aprire la pagina di login del sito.\
Per ulteriori conferme, si può usare il comando "make test" per far partire la suite di test.\
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

Per svuotare il database e riempirlo di nuovi dati si può usare "make freshseed": un comando che fa partire le migrazioni del progetto e i seeder per popolare le tabelle.
