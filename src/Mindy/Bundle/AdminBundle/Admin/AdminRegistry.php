<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin;

use Exception;

/**
 * Class AdminRegistry
 */
class AdminRegistry
{
    /**
     * @var array
     */
    protected $controllers = [];

    /**
     * @param $id
     * @param $slug
     *
     * @throws Exception
     */
    public function addAdmin($id, $slug)
    {
        if (array_key_exists($slug, $this->controllers)) {
            throw new Exception(sprintf(
                'Admin controller with slug %s already registered', $slug
            ));
        }
        $this->controllers[$slug] = $id;
    }

    /**
     * @param $slug
     *
     * @return string|void
     */
    public function resolveAdmin($slug)
    {
        if (isset($this->controllers[$slug])) {
            return $this->controllers[$slug];
        }
    }
}
