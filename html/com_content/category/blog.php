<?php
/**
 * Override Layout: HTML Custom per Joomla 5.3.x
 * Risolve il conflitto row-cols / col-12
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Uniamo gli articoli (lead + intro)
$items = array_merge($this->lead_items, $this->intro_items);
$totalItems = count($items);

// In homepage: niente paginazione, mostra il pulsante "Altre notizie" verso /notizie.
// Sulle altre pagine (es. /notizie): paginazione normale, nessun pulsante.
$app         = Factory::getApplication();
$activeMenu  = $app->getMenu()->getActive();
$isHomepage  = $activeMenu !== null && $activeMenu->home;

?>

<div class="row lista-articoli">
    
    <?php foreach ($items as $key => $item) : ?>
        <?php
        // --- LOGICA VARIABILI ---
        $isFirst = ($key === 0);
        
        /**
         * LOGICA COLONNE JOOMLA 5.3 + BOOTSTRAP 5
         * Il primo articolo occupa 12 colonne (100%).
         * Gli altri occupano 6 colonne su desktop (50%) e 12 su mobile.
         */
        $colClass = $isFirst ? 'col-12' : 'col-md-6 col-12';
        
        $firstClassString = $isFirst ? ' first' : '';
        $articleClassAttr = $isFirst ? ' class="first"' : '';

        // Preparazione Link
        $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
        $item->cat_slug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
        $link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->cat_slug, $item->language));
        
        // Preparazione Immagine
        $images = json_decode($item->images);
        $imageSrc = '';
        $imageAlt = htmlspecialchars($item->title);
        
        if (isset($images->image_intro) && !empty($images->image_intro)) {
            $imageSrc = $images->image_intro;
            if (!empty($images->image_intro_alt)) $imageAlt = htmlspecialchars($images->image_intro_alt);
        } elseif (isset($images->image_fulltext) && !empty($images->image_fulltext)) {
            $imageSrc = $images->image_fulltext;
            if (!empty($images->image_fulltext_alt)) $imageAlt = htmlspecialchars($images->image_fulltext_alt);
        }
        
        // Preparazione Abstract
        $abstractText = $item->introtext;
        ?>

        <div class="<?php echo $colClass; ?> mb-4">
            <article data-total="<?php echo $totalItems; ?>" data-num="<?php echo $key; ?>"<?php echo $articleClassAttr; ?>>
                
                <figure class="immagine-articolo<?php echo $firstClassString; ?>">
                    <a href="<?php echo $link; ?>">
                        <?php if ($imageSrc) : ?>
                            <img src="<?php echo $imageSrc; ?>" alt="<?php echo $imageAlt; ?>" data-cmp-info="10" class="img-fluid w-100">
                        <?php endif; ?>
                    </a>
                    <figcaption class="didascalia "><?php echo htmlspecialchars($item->category_title); ?></figcaption>
                </figure>
                
                <div class="heading">
                    <div class="headline<?php echo $firstClassString; ?>">
                        <a <?php echo $isFirst ? 'class="article-title-link"' : ''; ?> href="<?php echo $link; ?>">
                            <?php echo htmlspecialchars($item->title); ?>
                        </a>
                        
                        <?php if ($abstractText) : ?>
                            <div class="abstract"><?php echo $abstractText; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($item->params->get('show_readmore') && $item->readmore) : ?>
                <p class="readmore">
                    <a class="btn btn-unisal" href="<?php echo $link; ?>">
                        <?php echo empty($item->alternative_readmore) ? 'Continua a leggere' : htmlspecialchars($item->alternative_readmore); ?>
                    </a>
                </p>
                <?php endif; ?>

            </article>
        </div>

    <?php endforeach; ?>
</div>

<?php if ($isHomepage) : ?>
    <?php
    $notizieItem = $app->getMenu()->getItems('alias', 'notizie', true);
    $notizieLink = $notizieItem ? Route::_('index.php?Itemid=' . $notizieItem->id) : Route::_(Uri::root(true) . '/notizie');
    ?>
    <div class="col-12">
        <a class="btn btn-unisal btn-lg altre-notizie" href="<?php echo $notizieLink; ?>">Altre notizie</a>
    </div>
<?php elseif ($this->pagination->getPagesLinks()) : ?>
    <div class="com-content-footer pagination-wrapper">
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>
