<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.unisal_general
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\ErrorDocument $this */

$app   = Factory::getApplication();
$input = $app->getInput();

// Detecting Active Variables
$option    = $input->getCmd('option', '');
$view      = $input->getCmd('view', '');
$layout    = $input->getCmd('layout', '');
$task      = $input->getCmd('task', '');
$itemid    = $input->getCmd('Itemid', '');
$sitename  = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu      = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$t         = $app->getTemplate(true);
$template  = Uri::root(true) . '/templates/' . $t->template;

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Logo file or site title param
if ($this->params->get('logoFile')) {
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES), $sitename, ['loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($this->params->get('siteTitle')) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
}

// Container
$wrapper = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

// Get the error code
$errorCode = $this->error->getCode();

// The module renderer will not work properly due to incomplete Application initialisation
$renderModules = $app->getIdentity() && $app->getLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

<head>
    <jdoc:include type="metas" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" integrity="sha512-2bBQCjcnw658Lho4nlXJcc6WkV/UxpE/sAokbXPxQNGqmNdQrWqtw26Ns9kFF/yG792pKR1Sx8/Y1Lf1XN4GKA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo $template . '/assets/css/pixelfabrica.css' ?>" />
    <link rel="stylesheet" href="<?php echo $template . '/assets/css/colors.css' ?>" />
    <link rel="stylesheet" href="<?php echo $template . '/assets/css/custom.css' ?>" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>

<body class="site error_site <?php echo $option
    . ' ' . $wrapper
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : ' no-layout')
    . ($task ? ' task-' . $task : ' no-task')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($pageclass ? ' ' . $pageclass : '')
    . ($this->direction == 'rtl' ? ' rtl' : '');
?>">
    <header class="custom-header header container-header full-width">
        <div class="container-fluid px-fluid">
            <div class="row align-items-center py-3">
                <div class="col-md-3 col-sm-12">
                    <div class="logo">
                        <a class="brand-logo" href="<?php echo $this->baseurl; ?>/">
                            <?php echo $logo; ?>
                        </a>
                    </div>
                </div>
                <?php if ($renderModules && ($this->countModules('menu') || $this->countModules('search'))) : ?>
                <div class="col-md-9 col-sm-12">
                    <?php if ($this->countModules('menu')) : ?>
                        <jdoc:include type="modules" name="menu" style="none" />
                    <?php endif; ?>
                    <?php if ($this->countModules('search')) : ?>
                        <jdoc:include type="modules" name="search" style="none" />
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <?php if ($renderModules && $this->countModules('error-' . $errorCode)) : ?>
            <jdoc:include type="message" />
            <jdoc:include type="modules" name="error-<?php echo $errorCode; ?>" style="none" />
        <?php else : ?>
            <h1 class="page-header"><?php echo Text::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
            <div class="card">
                <div class="card-body">
                    <jdoc:include type="message" />
                    <p><strong><?php echo Text::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
                    <p><?php echo Text::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
                    <ul>
                        <li><?php echo Text::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                        <li><?php echo Text::_('JERROR_LAYOUT_MISTYPED_ADDRESS'); ?></li>
                        <li><?php echo Text::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                        <li><?php echo Text::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
                    </ul>
                    <p><?php echo Text::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
                    <p><a href="<?php echo $this->baseurl; ?>/index.php" class="btn btn-unisal"><i class="fa-solid fa-house" aria-hidden="true"></i> <?php echo Text::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
                    <hr>
                    <p><?php echo Text::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
                    <blockquote>
                        <span class="badge bg-secondary"><?php echo $this->error->getCode(); ?></span> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
                    </blockquote>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->debug) : ?>
            <div>
                <?php echo $this->renderBacktrace(); ?>
                <?php // Check if there are more Exceptions and render their data as well ?>
                <?php if ($this->error->getPrevious()) : ?>
                    <?php $loop = true; ?>
                    <?php // Reference $this->_error here and in the loop as setError() assigns errors to this property and we need this for the backtrace to work correctly ?>
                    <?php // Make the first assignment to setError() outside the loop so the loop does not skip Exceptions ?>
                    <?php $this->setError($this->_error->getPrevious()); ?>
                    <?php while ($loop === true) : ?>
                        <p><strong><?php echo Text::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
                        <p><?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php echo $this->renderBacktrace(); ?>
                        <?php $loop = $this->setError($this->_error->getPrevious()); ?>
                    <?php endwhile; ?>
                    <?php // Reset the main error object to the base error ?>
                    <?php $this->setError($this->error); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php if ($renderModules && ($this->countModules('footer-left', true) || $this->countModules('footer-right', true))) : ?>
    <footer class="p-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <?php if ($this->countModules('footer-left', true)) : ?>
                        <jdoc:include type="modules" name="footer-left" style="none" />
                    <?php endif; ?>
                    <?php if ($this->countModules('footer-right', true)) : ?>
                        <jdoc:include type="modules" name="footer-right" style="none" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <?php if ($renderModules) : ?>
        <jdoc:include type="modules" name="debug" style="none" />
    <?php endif; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js" integrity="sha512-HvOjJrdwNpDbkGJIG2ZNqDlVqMo77qbs4Me4cah0HoDrfhrbA+8SBlZn1KrvAQw7cILLPFJvdwIgphzQmMm+Pw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
