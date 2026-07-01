<?php 
  function getBaseLink($dev_mode, $facolta)
  {
    if ($dev_mode === true) {
      switch ($facolta) {
        case "fsc":
          $baselink = "https://betafsc.unisal.it/";
          break;
        case "teologia":
          $baselink = "https://betateologia.unisal.it/";
          break;
        case "filosofia":
          $baselink = "https://betafilosofia.unisal.it/";
          break;
        case "latinitas":
          $baselink = "https://betalatinitas.unisal.it/";
          break;
        case "fse":
          $baselink = "https://betafse.unisal.it/";
          break;
        default:
          $baselink = "https://beta.unisal.it/";
          break;
      }
    } else {
      switch ($facolta) {
        case "fsc":
          $baselink = "https://fsc.unisal.it/";
          break;
        case "teologia":
          $baselink = "https://teologia.unisal.it/";
          break;
        case "filosofia":
          $baselink = "https://filosofia.unisal.it/";
          break;
        case "latinitas":
          $baselink = "https://latinitas.unisal.it/";
          break;
        case "fse":
          $baselink = "https://fse.unisal.it/";
          break;
        case "psicologia":
          $baselink = "https://psicologia.unisal.it/";
          break;
        default:
          $baselink = "https://www.unisal.it/";
          break;
      }
    }

    return $baselink;
  }
?>
      <div id="top-nav-wrapper" class="bg-unisal">
       <div class="container-fluid">
        <div class="wrapper">
          <div class="row">
            <div class="col">
              <div class="top-nav">
                  <nav class="navbar navbar-expand-lg navbar-dark">
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav me-auto topnav mb-2 mb-lg-0">

      <?php
      // Caricamento del file JSON
      $json_data = file_get_contents('https://api.unisal.it/menu/it');
      $menu_items = json_decode($json_data, true);

      // Helper function per generare l'URL corretto
      function renderUrl($item) {
          if (isset($item['external']) && $item['external']) {
              return $item['url'];
          }
          if (isset($item['base_context']) && !empty($item['url'])) {
              // Simula la chiamata a utils->getBaseLink usando il contesto specificato
              $baselink = getBaseLink(false, $item['base_context']);
              return $baselink . $item['url'];
          }
          // Fallback se l'URL è # o vuoto
          return $item['url'] ?: '#';
      }

      // Helper function per generare gli attributi comuni dei link
      function renderLinkAttrs($item) {
          $attrs = '';
          if (isset($item['target'])) {
              $attrs .= ' target="' . $item['target'] . '"';
          }
          if (isset($item['class'])) {
               // Aggiunge classi extra oltre a dropdown-item (es. long-item)
              $attrs .= ' class="dropdown-item ' . $item['class'] . '"';
          } else {
              $attrs .= ' class="dropdown-item"';
          }
          return $attrs;
      }

      foreach ($menu_items as $item):
      ?>

        <?php if ($item['type'] === 'simple'): ?>
            <li class="nav-item <?php echo isset($item['classes']) ? $item['classes'] : ''; ?>">
                <?php
                // Logica specifica per il link Home che stampa il baselink nudo se l'url è vuoto
                $baselink = getBaseLink(false, $item['base_context']);
                $href = empty($item['url']) ? $baselink : $baselink . $item['url'];
                ?>
                <a class="nav-link <?php echo isset($item['active']) && $item['active'] ? 'active' : ''; ?>" aria-current="page" href="<?php echo $href; ?>"><?php echo $item['label']; ?></a>
            </li>

        <?php elseif ($item['type'] === 'mega'): ?>
            <li class="nav-item dropdown custom-dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><?php echo $item['label']; ?></a>
                <div id="<?php echo $item['id']; ?>" class="dropdown-menu shadow" data-bs-popper="none">
                    <div class="mega-content <?php echo $item['width_class']; ?>">
                        <div class="container-fluid">

                            <?php
                            // Gestione wrapper nidificato (caso specifico Offerta Formativa)
                            if (isset($item['has_nested_wrapper']) && $item['has_nested_wrapper']): ?>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="<?php echo $item['wrapper_class']; ?>">
                            <?php else: ?>
                            <div class="row gx-5">
                            <?php endif; ?>

                            <?php foreach ($item['columns'] as $col): ?>
                                <div class="<?php echo $col['class']; ?>">

                                    <?php if (isset($col['title'])): ?>
                                        <h5>
                                            <?php if (isset($col['title_link']) && $col['title_link']):
                                                // Calcola baselink per il titolo se necessario
                                                $titleHref = "#";
                                                if (isset($col['title_url'])) {
                                                     // Se è un URL esplicito (es. Segreteria)
                                                     if (isset($col['external']) && $col['external']) {
                                                         $titleHref = $col['title_url'];
                                                     } else {
                                                         $ctx = isset($col['base_context']) ? $col['base_context'] : 'www';
                                                         $titleHref = getBaseLink(false, $ctx) . $col['title_url'];
                                                     }
                                                } else {
                                                    // Se è solo il baselink della facoltà
                                                    $ctx = isset($col['base_context']) ? $col['base_context'] : 'www';
                                                    $titleHref = getBaseLink(false, $ctx);
                                                }
                                                $target = isset($col['title_target']) ? ' target="'.$col['title_target'].'"' : '';
                                            ?>
                                                <a href="<?php echo $titleHref; ?>"<?php echo $target; ?>><?php echo $col['title']; ?></a>

                                            <?php elseif (isset($col['title_url'])): /* Caso Editrice LAS link esterno diretto su H5 */ ?>
                                                <a target="<?php echo $col['title_target']; ?>" href="<?php echo $col['title_url']; ?>"><?php echo $col['title']; ?></a>
                                            <?php else: ?>
                                                <?php echo $col['title']; ?>
                                            <?php endif; ?>
                                        </h5>
                                    <?php endif; ?>

                                    <div class="item-list">
                                        <?php foreach ($col['items'] as $subItem): ?>
                                            <?php if (isset($subItem['type']) && $subItem['type'] === 'divider'): ?>
                                                <?php if (isset($item['id']) && $item['id'] == 'offerta-formativa'): ?>
                                                     <hr />
                                                <?php else: ?>
                                                     <div class="dropdown-divider"></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="<?php echo renderUrl($subItem); ?>"<?php echo renderLinkAttrs($subItem); ?>>
                                                    <?php echo $subItem['label']; ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (isset($item['has_nested_wrapper']) && $item['has_nested_wrapper']): ?>
                                    </div> </div> </div> <?php else: ?>
                            </div> <?php endif; ?>

                        </div>
                    </div>
                </div>
            </li>

        <?php elseif ($item['type'] === 'dropdown'): ?>
            <li class="nav-item dropdown no-megamenu">
                <?php
                $mainHref = renderUrl($item);
                $toggleAttrs = 'class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"';
                ?>
                <a href="<?php echo $mainHref; ?>" <?php echo $toggleAttrs; ?>>
                    <?php echo $item['label']; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php foreach ($item['items'] as $subItem): ?>
                        <?php if (isset($subItem['type']) && $subItem['type'] === 'divider'): ?>
                            <div class="dropdown-divider"></div>
                        <?php else: ?>
                            <li>
                                <a href="<?php echo renderUrl($subItem); ?>"<?php echo renderLinkAttrs($subItem); ?>>
                                    <?php echo $subItem['label']; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </li>

        <?php endif; ?>

      <?php endforeach; ?>
    </ul>
  </div>
</nav>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
