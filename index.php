<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.unisal_general
 * 
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author      Pixelfabrica Lab SRL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */
$app   = Factory::getApplication();
$input = $app->getInput();
// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Detecting Active Variables
$option   = $input->getCmd('option', '');
$view     = $input->getCmd('view', '');
$layout   = $input->getCmd('layout', '');
$task     = $input->getCmd('task', '');
$itemid   = $input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$t = $app->getTemplate(true);
$template = Uri::root(true) . '/templates/' . $t->template;
// Logo file or site title param
if ($this->params->get('logoFile')) {
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES), $sitename, ['loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($this->params->get('siteTitle')) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
}

$hasClass = '';

if ($this->countModules('sidebar-left', true)) {
    $hasClass .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right', true)) {
    $hasClass .= ' has-sidebar-right';
}

// Container
$wrapper = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

$stickyHeader = $this->params->get('stickyHeader') ? 'position-sticky sticky-top' : '';
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
    <?php if (is_file(__DIR__ . '/assets/css/local.css')) : ?>
    <link rel="stylesheet" href="<?php echo $template . '/assets/css/local.css' ?>" />
    <?php endif; ?>
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>

<body class="site <?php echo $option
    . ' ' . $wrapper
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : ' no-layout')
    . ($task ? ' task-' . $task : ' no-task')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($pageclass ? ' ' . $pageclass : '')
    . $hasClass
    . ($this->direction == 'rtl' ? ' rtl' : '');
    

?>">
    <header class="custom-header header container-header full-width sticky-lg-top">
    <?php include(__DIR__ . "/common_menu.php");
    ?>
    <div class="container-fluid px-fluid">
      <div class="row">
        <div class="col-md-3 col-sm-12 col-lg-3">
          <div class="logo py-3">
          <?php if ($this->countModules('logo')) : ?>
            <jdoc:include type="modules" name="logo" style="none" />
          <?php endif; ?>
          </div>
        </div>
        <div class="col-md-9 col-sm-12 col-lg-9">
          <div class="wrapper-right social cerca">
            <div class="row justify-content-end">
              <div class="col-md-5 col-sm-12 col-lg-4">
                <div class="my-2">
                  <?php if ($this->countModules('social')) : ?>
                    <jdoc:include type="modules" name="social" style="none" />
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-md-5 col-sm-12 col-lg-4">
                <div class="cerca">
                  <?php if ($this->countModules('search')) : ?>
                    <jdoc:include type="modules" name="search" style="none" />
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-12">
              <div class="menu-contestuale d-flex justify-content-end">
                <!-- Desktop menu (navbar) -->
                <nav class="navbar navbar-expand-lg navbar-dark bg-white d-none d-lg-block">
                  <div class="container-fluid nav-contestuale">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse-facolta" data-bs-target="#facolta_nav">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse-facolta navbar-collapse" id="facolta_nav">
                      <?php if ($this->countModules('menu')) : ?>
                        <jdoc:include type="modules" name="menu" style="none" />
                      <?php endif; ?>
                    </div>
                  </div>
                </nav>
                
                <!-- Mobile menu (accordion) -->
                <div class="d-lg-none w-100">
                  <button class="btn btn-outline-secondary w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuAccordion" aria-expanded="false" aria-controls="mobileMenuAccordion">
                    <i class="fa-solid fa-bars me-2"></i> Menu
                  </button>
                  <div class="collapse mt-2" id="mobileMenuAccordion">
                    <div class="menu-accordion">
                      <?php if ($this->countModules('offcanvas')) : ?>
                        <jdoc:include type="modules" name="offcanvas" style="none" />
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </header>
    <?php if ($this->countModules('slideshow', true)) : ?>
    <div class="container-fluid">
    	<div class="d-none slideshow flexslider d-md-block">
          <jdoc:include type="modules" name="slideshow" style="none" />
	    </div>
    </div>
    <?php endif; ?>
    <?php if ($this->countModules('opac', true)) : ?>
    <div class="container">
    	<div class="row">
          <div class="col">
            <jdoc:include type="modules" name="opac" style="none" />
          </div>
	    </div>
    </div>
    <?php endif; ?>
    <main class="container-fluid">
      <section id="articles" class="container home-articles">
        <?php $hasSidebar = $this->countModules('sidebar-right', true); ?>
        <div class="row justify-content-center">
          <div class="<?php echo $hasSidebar ? 'col-md-8 col-sm-12' : 'col-12'; ?>">
            <jdoc:include type="component" />

            <?php if ($this->countModules('below-article', true)) : ?>
            <hr />
            <div class="below-article my-5">
              <jdoc:include type="modules" name="below-article" style="none" />
            </div>
            <?php endif; ?>

            <?php if (false) : ?>
            <div class="col-12">
              <a class="btn btn-primary btn-lg altre-notizie" href="/notizie">Altre notizie</a>
            </div>
            <?php endif; ?>
          </div>
          <?php if ($hasSidebar) : ?>
          <div class="col-md-4 col-sm-12 mt-md-0 mt-5">
            <aside class="right-sidebar">
              <jdoc:include type="modules" name="sidebar-right" style="none" />
            </aside>
          </div>
          <?php endif; ?>
        </div>
        <!-- videogallery dinamica -->
        <?php if ($this->countModules('video', true)) : ?>
        <div class="row">
          <div class="col-12">
            <jdoc:include type="modules" name="video" style="none" />
          </div>
        </div>
        <?php endif; ?>
        <!-- gallery dinamica -->
        <?php if ($this->countModules('gallery', true)) : ?>
        <div class="row">
          <div class="col-12">
            <jdoc:include type="modules" name="gallery" style="none" />
          </div>
        </div>
        <?php endif; ?>
  	</section>
		<hr />
    <?php if ($this->countModules('newsletter', true)) : ?>
    <section id="newsletter" class="container pb-0">
      <div class="row newsletter">
        <div class="col-md-6 col-sm-12">	
          <jdoc:include type="modules" name="newsletter" style="none" />
        </div>
      </div>
    </section>
      <?php endif; ?>
    </main>

  <?php if ($this->countModules('footer-left', true) || $this->countModules('footer-right', true)) : ?>
      <footer class="p-5">
        <div class="container">
          <div class="row">
            <div class="col-md-12 col-sm-12">
                <?php if ($this->countModules('footer-left', true)) : ?>
                      <jdoc:include type="modules" name="footer-left" style="none" />
                <?php endif; ?>
                <?php if ($this->countModules('footer-right', true)) : ?>
                <div class="col-md-2 col-sm-12 mt-md-5 mt-lg-0 mt-sm-2">
                  <div class="social footer text-center text-md-start">
                      <jdoc:include type="modules" name="footer-right" style="none" />
                  </div>
                </div>
                <?php endif; ?>
            </div>
          </div>
          <?php if ($this->countModules('copy', true)) : ?>
          <div class="row">
            <div class="col-12">
              <div class="end-footer">
                <jdoc:include type="modules" name="copy" style="none" />
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </footer>
  <?php endif; ?>


    <jdoc:include type="modules" name="debug" style="none" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js" integrity="sha512-HvOjJrdwNpDbkGJIG2ZNqDlVqMo77qbs4Me4cah0HoDrfhrbA+8SBlZn1KrvAQw7cILLPFJvdwIgphzQmMm+Pw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="<?php echo $template . '/assets/js/custom.js' ?>"></script>
</body>
</html> 
