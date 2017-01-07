<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:44
 */

namespace Mindy\Bundle\AdminBundle\View;

use Symfony\Component\HttpFoundation\Request;

interface ViewInterface
{
    /**
     * @param Request $request
     */
    public function handleRequest(Request $request);

    /**
     * @param $view
     * @param array $parameters
     * @return string
     */
    public function renderTemplate($view, array $parameters = array());
}