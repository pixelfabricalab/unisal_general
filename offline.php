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
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
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
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES), $sitename, ['class' => 'offline-logo', 'loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($this->params->get('siteTitle')) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'offline-logo', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="site d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="outer container">
        <div class="offline-card card shadow-sm mx-auto" style="max-width: 400px;">
            <div class="card-body p-4">

                <div class="header text-center mb-4">
                    <div class="logo mb-3">
                      <?php echo $logo; ?>
                    </div>
                    <?php if ($app->get('offline_image')) : ?>
                        <div class="offline-image mb-3">
                            <?php echo HTMLHelper::_('image', $app->get('offline_image'), $sitename, ['class' => 'img-fluid'], false, 0); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
                        <p class="text-muted small"><?php echo $app->get('offline_message'); ?></p>
                    <?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
                        <p class="text-muted small"><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="login">
                    <jdoc:include type="message" />

                    <form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
                        <fieldset class="border-0 p-0 m-0">

                            <div class="mb-3">
                                <label for="username" class="form-label small fw-bold"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                                <input name="username" class="form-control" id="username" type="text" placeholder="Username">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label small fw-bold"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                                <input name="password" class="form-control" id="password" type="password" placeholder="Password">
                            </div>

                            <?php foreach ($extraButtons as $button) :
                                $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                                    return substr($key, 0, 5) == 'data-';
                                });
                                ?>
                                <div class="mod-login__submit mb-2">
                                    <button type="button"
                                            class="btn btn-outline-secondary w-100 <?php echo $button['class'] ?? '' ?>"
                                            <?php foreach ($dataAttributeKeys as $key) : ?>
                                                <?php echo $key ?>="<?php echo $button[$key] ?>"
                                            <?php endforeach; ?>
                                            <?php if ($button['onclick']) : ?>
                                                onclick="<?php echo $button['onclick'] ?>"
                                            <?php endif; ?>
                                            title="<?php echo Text::_($button['label']) ?>"
                                            id="<?php echo $button['id'] ?>"
                                    >
                                        <?php if (!empty($button['icon'])) : ?>
                                            <span class="<?php echo $button['icon'] ?> me-1"></span>
                                        <?php elseif (!empty($button['image'])) : ?>
                                            <?php echo $button['image']; ?>
                                        <?php elseif (!empty($button['svg'])) : ?>
                                            <?php echo $button['svg']; ?>
                                        <?php endif; ?>
                                        <?php echo Text::_($button['label']) ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" name="Submit" class="btn btn-primary btn-lg">
                                    <?php echo Text::_('JLOGIN'); ?>
                                </button>
                            </div>

                            <input type="hidden" name="option" value="com_users">
                            <input type="hidden" name="task" value="user.login">
                            <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
                            <?php echo HTMLHelper::_('form.token'); ?>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
