# unisal_general

Template Joomla di riferimento per i portali dell'ecosistema **Unisal**, compatibile con **Joomla 4, 5 e 6**. Installabile via zip su qualsiasi sito Joomla senza modifiche: basta caricare il logo e personalizzare un unico file CSS.

Deriva dalla linea `bibunisal` (Joomla 5) → `csdb` (Joomla 6), ora generalizzata e priva di riferimenti hardcoded a un portale specifico.

## Caratteristiche

- ✅ Compatibile Joomla 4 / 5 / 6 — nessuna API esclusiva di una singola major
- ✅ Base derivata da `cassiopeia` (template ufficiale Joomla)
- ✅ Topbar Unisal centralizzata, popolata dinamicamente dall'API `https://api.unisal.it/menu/it`
- ✅ Layout responsive con Bootstrap 5 (navbar desktop + menu ad accordion mobile)
- ✅ Logo, pagina offline e percorsi asset gestiti da parametri backend — nessun path hardcoded
- ✅ Override dei layout core (`com_content`, `com_finder`, `mod_menu`, `mod_articles`) per un'estetica coerente su tutti i portali
- ✅ Un solo file da toccare per personalizzare un portale: `assets/css/custom.css`

## Struttura

```
unisal_general/
├── index.php              # Template principale
├── offline.php            # Pagina offline/login
├── common_menu.php        # Topbar Unisal (menu centralizzato via API)
├── component.php
├── error.php
├── templateDetails.xml    # Manifest Joomla
├── assets/
│   ├── css/
│   │   ├── custom.css         # Stili custom del portale — unico file da modificare
│   │   ├── pixelfabrica.css   # Stili base condivisi — non modificare
│   │   ├── colors.css         # Variabili colore
│   │   └── fonts/             # D-DIN Condensed, Source Sans Pro
│   └── js/custom.js
└── html/                  # Override dei layout core Joomla
    ├── com_content/
    ├── com_finder/
    ├── mod_articles/
    ├── mod_menu/
    ├── mod_custom/
    └── layouts/chromes/
```

Documentazione tecnica completa, dettagli implementativi e procedure operative in [`HANDOFF.md`](HANDOFF.md).

## Installazione

### Da zip
1. Comprimere il contenuto della cartella `unisal_general/` in un file `.zip`
2. Sistema → Installa estensione → carica il pacchetto

### Da CLI Joomla
```bash
php cli/joomla.php extension:discover
php cli/joomla.php extension:discover:install --eid=<ID>
```

Dopo l'installazione:
1. Impostare il template come predefinito (Sistema → Template)
2. Caricare il logo del portale nei parametri del template — viene usato automaticamente in header e pagina offline
3. Assegnare i moduli alle posizioni desiderate (vedi elenco in `templateDetails.xml`)

## Creare un nuovo portale figlio

1. Copiare la cartella `unisal_general/` e rinominarla con il nome del nuovo portale
2. Aggiornare `templateDetails.xml`: `<name>`, `<description>`, `<creationDate>`
3. Installare (via zip o CLI, come sopra)
4. Caricare il logo dal backend
5. Personalizzare solo `assets/css/custom.css` per le differenze specifiche del portale

## Override CSS locale

`assets/css/local.css` è un file **opzionale, escluso dal repo** (`.gitignore`): se lo si crea manualmente sul server, viene caricato automaticamente dal template come ultimo foglio di stile, dopo `custom.css`, quindi ha sempre la precedenza.

Serve per applicare modifiche puntuali direttamente su un'installazione (hotfix, test, personalizzazioni specifiche di un ambiente) senza toccare i file versionati nel repo — utile in particolare con gli aggiornamenti OTA (vedi `HANDOFF.md` §10), perché un aggiornamento del template sovrascrive solo i file presenti nel pacchetto e non tocca `local.css`, che quindi sopravvive agli update.

Per crearlo basta caricare via FTP/SFTP un file `assets/css/local.css` nella cartella del template installato; se il file non esiste il template funziona normalmente ignorandolo.

Checklist completa in [`HANDOFF.md` §8](HANDOFF.md#8-checklist-nuovo-portale-figlio-da-unisal_general).

## Regole di portabilità

| ✅ Corretto | ❌ Da evitare |
|------------|--------------|
| `Uri::root(true) . '/templates/' . $t->template` | Path assoluti hardcoded (`/biblioteca/templates/...`) |
| `$logo` da parametri template | Logo hardcoded nel PHP |
| Selettori CSS generici (`input.js-finder-search-query`) | ID di istanza (`#mod-finder-searchword118`) |
| Logo caricato dal backend | File immagine committato nel repo |

Il manifest `templateDetails.xml` deve elencare **tutti** i file e le cartelle reali del template in `<files>` — altrimenti l'installer Joomla copia solo quanto dichiarato e il pacchetto risulta rotto.

## Palette colori

```css
--unisal-red:       #ac2433;   /* rosso primario */
--unisal-red-dark:  #8e1d29;   /* rosso hover/focus */
--unisal-red-light: #c4374a;   /* rosso accordion aperto */
```

## Dipendenze esterne

| Dipendenza | Come viene caricata |
|------------|---------------------|
| Bootstrap 5.3 | CDN |
| Font Awesome 6 | CDN |
| D-DIN Condensed / Source Sans Pro | Font custom in `assets/css/fonts/` |
| Awesomplete | Caricato da `com_finder` |
| Menu Unisal | API `https://api.unisal.it/menu/it` |

## Portali derivati

| Portale | Dominio | Note |
|---------|---------|------|
| Biblioteca Unisal | biblioteca.unisal.it | Origine (ex `bibunisal`, Joomla 5) |
| CSDB | csdb.unisal.it | Derivato Joomla 6 (ex `csdb`) |

## Autore

Andrea Coi — Pixelfabrica Lab SRL
