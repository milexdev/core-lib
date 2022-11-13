<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://milex.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div>
<?php echo $view->render('MauticAssetBundle:Asset:preview.html.php', ['activeAsset' => $event['extra']['asset'], 'assetDownloadUrl' => $view['router']->url(
    'milex_asset_action',
    ['objectAction' => 'preview', 'objectId' => $event['extra']['asset']->getId()]
)]); ?>
</div>