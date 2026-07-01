<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_articles
 *
 * @copyright   (C) 2024 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="row lista-articoli mt-4">
    <?php foreach ($items as $key => $item) : ?>
        <?php
        $isFirst  = ($key === 0);
        $colClass = $isFirst ? 'col-12' : 'col-md-6 col-12';
        $link     = htmlspecialchars($item->link, ENT_COMPAT, 'UTF-8', false);
        $title    = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);

        // Immagine: imageSrc è già escaped dall'helper. Alt da images JSON.
        $images   = json_decode($item->images ?? '{}');
        $imageAlt = '';
        if (!empty($images->image_intro_alt)) {
            $imageAlt = htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8');
        } elseif (!empty($images->image_fulltext_alt)) {
            $imageAlt = htmlspecialchars($images->image_fulltext_alt, ENT_COMPAT, 'UTF-8');
        } else {
            $imageAlt = $title;
        }

        // Abstract: displayIntrotext (processato) se disponibile, altrimenti introtext grezzo
        $abstract = $item->displayIntrotext ?? strip_tags($item->introtext ?? '');
        ?>
        <div class="<?php echo $colClass; ?> mb-4">
            <article class="<?php echo $isFirst ? 'first' : ''; ?>">

                <?php if (!empty($item->imageSrc)) : ?>
                <figure class="immagine-articolo<?php echo $isFirst ? ' first' : ''; ?>">
                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo $item->imageSrc; ?>"
                             alt="<?php echo $imageAlt; ?>"
                             class="img-fluid w-100">
                    </a>
                    <?php if ($item->displayCategoryTitle) : ?>
                    <figcaption class="didascalia"><?php echo $item->displayCategoryTitle; ?></figcaption>
                    <?php endif; ?>
                </figure>
                <?php endif; ?>

                <div class="heading">
                    <div class="headline<?php echo $isFirst ? ' first' : ''; ?>">
                        <a <?php echo $isFirst ? 'class="article-title-link"' : ''; ?> href="<?php echo $link; ?>">
                            <?php echo $title; ?>
                        </a>

                        <?php if ($abstract) : ?>
                        <div class="abstract"><?php echo $abstract; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
