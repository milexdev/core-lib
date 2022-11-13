<?php

/*
 * @copyright   2019 Milex Contributors. All rights reserved
 * @author      Milex
 *
 * @link        http://milex.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

?>
<td class="<?php echo $class; ?> text-center">
    <?php
    $color = $item->getColor();
    $style = !empty($color) ? ' style="background-color: '.$color.';"' : '';
    ?>
    <?php if ($item->getStage()):?>
        <span class="label label-default"<?php echo $style; ?>><?php echo $view->escape($item->getStage()->getName()); ?></span>
    <?php endif; ?>
</td>