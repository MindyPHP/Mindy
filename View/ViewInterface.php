<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
     * @return string
     */
    public function renderTemplate();

    /**
     * @return array
     */
    public function getContextData();
}
