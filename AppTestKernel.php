<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://milex.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

class AppTestKernel extends AppKernel
{
    /**
     * {@inheritdoc}
     */
    protected function isInstalled(): bool
    {
        return true;
    }
}
