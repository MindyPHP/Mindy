<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Model;

use Mindy\Orm\TreeManager;

/**
 * Class PageManager.
 */
class PageManager extends TreeManager
{
    /**
     * @return \Mindy\Orm\TreeQuerySet
     */
    public function published()
    {
        return $this->filter(['is_published' => true]);
    }
}
