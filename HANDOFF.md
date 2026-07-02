# Handoff — Template unisal_general (Joomla 4 / 5 / 6)

**Autore:** Andrea Coi — Pixelfabrica Lab SRL  
**Ultimo aggiornamento:** 2026-07-01  
**Origine:** `bibunisal` (Joomla 5) → `csdb` (Joomla 6) → `unisal_general`  
**Compatibilità:** Joomla 4.x, 5.x, 6.x (base derivata da `cassiopeia`, senza API esclusive di una singola major)

---

## Ruolo di questo template nell'ecosistema Unisal

> **`unisal_general` è il template di riferimento generico per tutti i portali Unisal**, installabile via zip su qualsiasi sito Joomla dalla versione 4 alla 6.

Non è legato a un portale specifico: va copiato e rinominato per ogni nuovo portale figlio (vedi procedura sotto). Non contiene più riferimenti hardcoded a `csdb` o `bibunisal`.

**Portali Unisal esistenti o derivati:**
| Portale | Dominio | Stato |
|---------|---------|-------|
| Biblioteca Unisal | biblioteca.unisal.it | Origine (ex `bibunisal`, Joomla 5) |
| CSDB | csdb.unisal.it | Derivato Joomla 6 (ex `csdb`) |

### Procedura per creare un nuovo portale figlio
1. Copiare la cartella `templates/unisal_general/` e rinominarla con il nome del nuovo portale (rinominare anche la cartella stessa)
2. Aggiornare `templateDetails.xml`: `<name>`, `<description>`, `<creationDate>`
3. Installare via CLI Joomla: `php cli/joomla.php extension:discover && php cli/joomla.php extension:discover:install --eid=<ID>` — oppure via zip da Sistema → Installa estensione (compatibile Joomla 4/5/6)
4. Caricare il logo nel backend (Sistema → Template → parametri) — la pagina offline e l'header lo leggono entrambi dai parametri
5. Per eventuali differenze specifiche del portale (logo fuori misura, richieste di palette diversa, ecc.) creare `assets/css/local.css` sul server — non modificare `custom.css` (§4)

---

## 1. Struttura del template

```
templates/unisal_general/
├── index.php                          # Template principale
├── offline.php                        # Pagina offline/login
├── common_menu.php                    # Topbar Unisal (menu centralizzato via API)
├── component.php
├── error.php
├── templateDetails.xml                # Manifest Joomla
├── assets/
│   ├── css/
│   │   ├── custom.css                 # Stili condivisi del template — applicati sempre a tutti i portali, non modificare per esigenze di una singola installazione
│   │   ├── pixelfabrica.css           # Stili base condivisi — non modificare
│   │   ├── colors.css                 # Variabili colore
│   │   ├── local.css                  # Override opzionale per singola installazione — non versionato, vedi §4
│   │   └── fonts/                     # D-DIN Condensed e Source Sans Pro
│   └── js/
│       └── custom.js
└── html/                              # Override Joomla
    ├── com_content/
    │   ├── article/default.php
    │   ├── article/default_links.php
    │   └── category/blog.php
    ├── com_finder/search/default_form.php
    ├── layouts/
    │   ├── chromes/card.php
    │   ├── chromes/noCard.php
    │   └── joomla/content/intro_image.php
    │   └── joomla/content/full_image.php
    ├── mod_articles/
    │   ├── default.php
    │   ├── default_items.php          # Override estetica blog Unisal
    │   └── default_titles.php
    ├── mod_menu/
    │   ├── default.php                # Override con Bootstrap dropdown
    │   ├── default_url.php            # Override con nav-link e dropdown-toggle
    │   └── collapse-metismenu.php / dropdown-metismenu*.php
    └── mod_custom/banner.php
```

---

## 2. `index.php` — punti chiave

### 2.1 Percorso CSS dinamico
```php
$t = $app->getTemplate(true);
$template = Uri::root(true) . '/templates/' . $t->template;
```
**Non usare mai percorsi hardcoded** tipo `/biblioteca/templates/...`.

### 2.2 Struttura navbar contestuale
```html
<nav class="navbar navbar-expand-lg navbar-dark bg-white d-none d-lg-block">
  <div class="container-fluid nav-contestuale">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse-facolta" data-bs-target="#facolta_nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse-facolta navbar-collapse" id="facolta_nav">
      <jdoc:include type="modules" name="menu" style="none" />
    </div>
  </div>
</nav>
```
Il modulo menu usa il layout `_:default` con gli override in `html/mod_menu/`.

### 2.3 Posizione `below-article`
Posizione custom sotto il component, utile per moduli `mod_articles` con estetica blog:
```php
<?php if ($this->countModules('below-article', true)) : ?>
<div class="below-article mt-4">
  <jdoc:include type="modules" name="below-article" style="none" />
</div>
<?php endif; ?>
```

### 2.4 Layout contenuto/sidebar dinamico
```php
<?php $hasSidebar = $this->countModules('sidebar-right', true); ?>
<div class="<?php echo $hasSidebar ? 'col-md-8 col-sm-12' : 'col-12'; ?>">
```

---

## 3. Override Joomla (`html/`)

### 3.1 `html/com_content/category/blog.php`
- Merge `lead_items` + `intro_items` in unico array
- Primo articolo `col-12`, gli altri `col-md-6`
- Immagine intro (fallback su fulltext) con `img-fluid w-100`
- Abstract da `$item->introtext` in un `<div class="abstract">` (non `<p>`, per evitare `<p>` annidati quando l'introtext contiene già paragrafi propri)
- Readmore con `btn btn-unisal`
- **Paginazione vs "Altre notizie":** questo layout è condiviso tra homepage e pagina `/notizie` (stesso componente Categoria/Blog). Se il menu attivo è la homepage (`$activeMenu->home`), la lista mostra solo gli articoli configurati nel modulo/menu item (lead+intro, come da parametri backend) seguiti da un pulsante `Altre notizie` che punta al menu item con alias `notizie`; la paginazione nativa di Joomla viene nascosta. Su tutte le altre pagine (inclusa `/notizie`) vale il comportamento standard: paginazione visibile, nessun pulsante. Se non esiste un menu item con alias `notizie`, il link ricade su `/notizie` relativo alla root del sito.

### 3.2 `html/mod_menu/default.php` e `default_url.php`
Override critici per il menu Bootstrap. Senza di questi il dropdown si posiziona male.

- `default.php`: aggiunge `navbar-nav` all'`<ul>`, `dropdown` ai `<li>` padre, `dropdown-menu` al sottomenu di primo livello
- `default_url.php`: aggiunge `nav-link` su tutti i link, `dropdown-toggle` + `data-bs-toggle="dropdown"` sui link padre

> **Attenzione:** Il core Joomla genera `mod-menu__sub` come classe del sottomenu — Bootstrap non la riconosce. L'override la sostituisce con `dropdown-menu` al primo livello.

### 3.3 `html/mod_articles/default_items.php`
Override che replica l'estetica del blog Unisal per i moduli `mod_articles`:
- Griglia row: primo item `col-12`, altri `col-md-6`
- Immagine da `$item->imageSrc` (già escaped dall'helper — **non ri-escapare**)
- Alt immagine estratto da `json_decode($item->images)` → `image_intro_alt`
- Abstract da `$item->displayIntrotext` con fallback su `strip_tags($item->introtext)`
- Readmore con `btn btn-unisal`

**Parametri da abilitare nel modulo backend:**
- `img_intro_full` → `intro`
- `show_introtext` → `1`
- `introtext_limit` → `0` (illimitato)
- `show_readmore` → `1`

### 3.4 `html/com_finder/search/default_form.php`
Fix layout barra di ricerca con `awesomplete`. Il label è rimosso dall'override del modulo (`mod_finder/default.php` NON è presente — il label si gestisce dal backend con "Nascondi label").

### 3.5 `html/layouts/chromes/card.php` e `noCard.php`
`<span>` attorno al titolo modulo per targeting CSS (es. `.notizie-flash > h3 span`).

---

## 4. Stili (`assets/css/custom.css`)

**Importante:** `custom.css`, come `pixelfabrica.css`, fa parte del pacchetto del template e viene applicato **sempre e allo stesso modo su tutti i portali** che usano `unisal_general`. Non va modificato per esigenze di una singola installazione (logo con proporzioni diverse dallo standard, richieste di virare la palette da parte del referente del portale, ecc.) — quelle vanno sempre in `assets/css/local.css` (vedi sotto), l'unico file pensato per differenziare un'installazione dal resto dell'ecosistema.

| Classe | Descrizione |
|--------|-------------|
| `.px-fluid` | Padding orizzontale responsive con `clamp()` per l'header |
| `.btn-unisal` | Tasto rosso Unisal (`#ac2433`) |
| `.notizie-flash` | Box notizie flash con titolo rosso e lista hover |
| `.accordion-unisal` | Accordion con sfondo rosso Unisal |
| `.feature-block` / `.feature-icon` / `.feature-link` | Blocchi homepage (4 colonne) |
| `.menu-accordion` | Menu mobile a fisarmonica |
| `.offline-logo` | Logo nella pagina offline: `width: 325px; max-width: 100%; height: auto !important;` |
| `.pagination-wrapper` | Stile della paginazione `com_content` (pagina `/notizie`) con palette Unisal — sovrascrive lo stile Bootstrap di default su `.pagination`/`.page-item`/`.page-link` |

### Fix CSS da non perdere
- `input.js-finder-search-query` — selettore generico per l'input Smart Search (non usare `#mod-finder-searchword<ID>` che è specifico dell'istanza)
- `div.awesomplete { flex: 1; min-width: 0; }` — evita che awesomplete rompa il flex dell'input-group
- `.offline-logo { height: auto !important; }` — sovrascrive `height: 150px` hardcoded in `pixelfabrica.css`
- `.nav-contestuale .dropdown-menu { --bs-dropdown-spacer: 0; margin-top: 0; }` — avvicina la tendina alla voce di menu

### Override locale (`assets/css/local.css`)

File **opzionale, escluso dal repo** via `.gitignore`. Se presente sul filesystem del server, `index.php`, `offline.php`, `error.php` e `component.php` lo includono automaticamente come ultimo `<link>` in `<head>`, dopo `custom.css` (quindi vince su tutto il resto):

```php
<?php if (is_file(__DIR__ . '/assets/css/local.css')) : ?>
<link rel="stylesheet" href="<?php echo $template . '/assets/css/local.css' ?>" />
<?php endif; ?>
```

Uso previsto: personalizzazioni/hotfix specifici di una singola installazione senza toccare i file versionati. Poiché non fa parte del pacchetto zip né del repo, **sopravvive agli aggiornamenti OTA** (§10) — l'installer Joomla sostituisce solo i file dichiarati in `templateDetails.xml`, mai file estranei presenti sul server.

Casi tipici in cui serve `local.css` (documentati anche in `README.md` "Adeguamenti frequenti in fase di installazione"):

- **Logo fuori misura** — il logo caricato da backend eredita le dimensioni pensate per il logo Unisal "standard"; se quello del portale è più largo/stretto o con proporzioni diverse va ridefinito `div.logo #logo-img` (dimensioni, margini) verificando anche il breakpoint mobile.
- **Richieste di virare la palette** — capita che il referente del portale chieda una variante cromatica sui pulsanti rispetto al rosso Unisal ufficiale (§9). Prima di assecondarla va fatto presente che si perde la coerenza visiva tra i portali; se confermata, si ridefinisce localmente (es. `.btn-unisal`) senza toccare il template condiviso.

---

## 5. `offline.php` — pagina offline/login

- Percorso CSS: stesso sistema di `index.php` con `Uri::root(true)`
- Logo: usa la variabile `$logo` già costruita (legge `logoFile` dai parametri del template, poi `siteTitle`, poi `logo.svg`) — **nessun percorso hardcoded**

---

## 6. Portabilità — regole da rispettare

| ✅ Corretto | ❌ Da evitare |
|------------|--------------|
| `Uri::root(true) . '/templates/' . $t->template` | `/biblioteca/templates/bibunisal` |
| `$logo` (variabile da parametri template) | `/images/assets/logo.png` hardcoded |
| `input.js-finder-search-query` | `input#mod-finder-searchword118` |
| Logo caricato dal backend | Logo hardcoded nel PHP |

---

## 7. Dipendenze esterne

| Dipendenza | Come viene caricata |
|------------|---------------------|
| Bootstrap 5.3 | CDN in `index.php` e `offline.php` |
| Font Awesome 6 | CDN in `index.php` e `offline.php` |
| D-DIN Condensed | Font custom in `assets/css/fonts/` |
| Source Sans Pro | Font custom in `assets/css/fonts/` |
| Awesomplete | Caricato da `com_finder` — non richiede azione |
| Menu Unisal | API `https://api.unisal.it/menu/it` in `common_menu.php` |

---

## 8. Checklist nuovo portale figlio da `unisal_general`

- [ ] Copiare `templates/unisal_general/` e rinominare
- [ ] Aggiornare `templateDetails.xml` (nome, descrizione, data)
- [ ] Installare via CLI: `extension:discover` + `extension:discover:install`
- [ ] Caricare il logo nel backend → appare automaticamente in header e pagina offline
- [ ] Verificare `common_menu.php`: aggiungere il nuovo portale nella funzione `getBaseLink()` se necessario
- [ ] Per differenze specifiche del portale (logo fuori misura, palette diversa, ecc.) creare `assets/css/local.css` sul server — non toccare `custom.css` né `pixelfabrica.css` (§4)
- [ ] Abilitare immagini e introtext nel modulo `mod_articles` se usato in `below-article`

---

## 9. Palette colori Unisal

```css
--unisal-red:       #ac2433;   /* rosso primario */
--unisal-red-dark:  #8e1d29;   /* rosso hover/focus */
--unisal-red-light: #c4374a;   /* rosso accordion aperto */
```

---

## 10. Release e aggiornamenti

Il workflow `.github/workflows/release.yml` pubblica automaticamente una GitHub Release con lo zip installabile ogni volta che `<version>` in `templateDetails.xml` cambia su `main`.

### Aggiornamenti OTA

`templateDetails.xml` dichiara un update server Joomla nativo:

```xml
<updateservers>
    <server type="extension" priority="1" name="unisal_general Update Site">https://raw.githubusercontent.com/pixelfabricalab/unisal_general/main/update.xml</server>
</updateservers>
```

`update.xml` (root del repo) contiene tre blocchi `<update>`, uno per major Joomla (`4\.[0-9]+`, `5\.[0-9]+`, `6\.[0-9]+`), ciascuno con `<version>`, `<downloadurl>`, `<sha256sum>` e `<changelogurl>` relativi all'ultima release GitHub.

- `<sha256sum>` — checksum SHA-256 dello zip, verificato da Joomla dopo il download: senza questo tag l'update manager mostra l'avviso "Questa estensione non fornisce un checksum per la convalida dell'integrità del file scaricato"
- `<changelogurl>` — link alla pagina della release GitHub corrispondente (mostrato come "Changelog" nella schermata di aggiornamento)

Ad ogni release, il workflow:
1. crea il tag e la GitHub Release con lo zip, con nel corpo gli ultimi 5 commit come changelog
2. calcola lo sha256 dello zip
3. aggiorna `<version>`, `<downloadurl>`, `<sha256sum>` e `<changelogurl>` in tutti i blocchi di `update.xml`
4. committa `update.xml` su `main` con `[skip ci]` nel messaggio (evita di far ripartire il workflow in loop)

Su ogni portale derivato con il template installato, Joomla legge `update.xml` da `raw.githubusercontent.com` e mostra la nuova versione in Sistema → Gestione aggiornamenti → Template, senza bisogno di caricare manualmente lo zip.

**Attenzione:** `<name>` in `templateDetails.xml` e `<element>` in `update.xml` devono restare `unisal_general` — se si rinomina il template per un portale figlio, va aggiornato anche `update.xml` (o rimosso l'update server, dato che i portali figli in genere non sono aggiornati OTA da questo repo).
