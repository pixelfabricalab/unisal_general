# CLAUDE.md

Template Joomla `unisal_general` — riferimento generico per tutti i portali Unisal, installabile via zip su qualsiasi sito Joomla dalla versione 4 alla 6. Deriva dalla linea `bibunisal` (Joomla 5) → `csdb` (Joomla 6), ora generalizzata e senza riferimenti hardcoded a un portale specifico. Vedi `HANDOFF.md` per la documentazione completa e aggiornata; questo file è un riassunto rapido per orientarsi.

## Cos'è

Template Joomla base per l'ecosistema Unisal, compatibile Joomla 4/5/6 (nessuna API esclusiva di una singola major, base derivata da `cassiopeia`). Non è legato a un portale: ogni nuovo portale figlio va creato copiando `templates/unisal_general/` e rinominando la cartella.

## Struttura

- `index.php` — template principale (navbar, layout contenuto/sidebar, posizione `below-article`)
- `offline.php` — pagina offline/login, con logo dinamico da parametri backend
- `common_menu.php` — topbar Unisal centralizzata, letta da API `https://api.unisal.it/menu/it`
- `component.php`, `error.php` — layout Joomla standard
- `templateDetails.xml` — manifest Joomla (nome, descrizione, data da aggiornare per ogni portale figlio)
- `assets/css/custom.css` — **unico file da modificare** per personalizzare un portale
- `assets/css/pixelfabrica.css` — stili base condivisi, **non modificare**
- `assets/css/colors.css` — variabili colore
- `html/` — override dei layout core Joomla (com_content, com_finder, mod_menu, mod_articles, layouts/chromes)

## Regole chiave (portabilità)

- Mai path hardcoded: usare `Uri::root(true) . '/templates/' . $t->template`
- Logo: sempre da variabile `$logo` / parametri template backend, mai hardcoded
- Selettori CSS: usare classi generiche (`input.js-finder-search-query`), mai ID specifici di istanza (`#mod-finder-searchword118`)
- Override `html/mod_menu/` sono critici per il dropdown Bootstrap — non rimuovere
- Palette: `--unisal-red: #ac2433` (primario), varianti dark/light in HANDOFF.md §9

## Creare un nuovo portale figlio

Procedura completa in `HANDOFF.md` §"Procedura per creare un nuovo portale figlio" e checklist §8. In sintesi: copiare `templates/unisal_general/` e rinominare la cartella, aggiornare `templateDetails.xml`, installare via zip o `php cli/joomla.php extension:discover(:install)`, caricare logo dal backend, personalizzare solo `custom.css`.

## Compatibilità Joomla 4-6

Il manifest `templateDetails.xml` deve elencare **tutti** i file/cartelle reali del template in `<files>` (index.php, offline.php, component.php, error.php, common_menu.php, templateDetails.xml, assets/, html/) — altrimenti l'installer Joomla copia solo i file dichiarati e lo zip risulta rotto all'installazione.
