<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 14/11/2016
 * Time: 19:57.
 */

namespace Mindy\Bundle\AdminBundle\Admin;

use Exception;

/**
 * Class AdminRegistry
 * @package Mindy\Bundle\AdminBundle\Admin
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
     * @return string|void
     */
    public function resolveAdmin($slug)
    {
        if (isset($this->controllers[$slug])) {
            return $this->controllers[$slug];
        }

        return;
    }
}
