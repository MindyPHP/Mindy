<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 23:09
 */

namespace Mindy\Bundle\AdminBundle\View;

use Symfony\Component\HttpFoundation\Request;

class DetailView extends AbstractView
{
    /**
     * @param Request $request
     */
    public function handleRequest(Request $request)
    {

    }

    protected function getObject()
    {

    }

    /**
     * @return array
     */
    public function getContextData()
    {
        return [
            'instance' => $this->getObject()
        ];
    }
}