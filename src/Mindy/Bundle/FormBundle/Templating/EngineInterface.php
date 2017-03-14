<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FormBundle\Templating;

/**
 * Interface EngineInterface
 */
interface EngineInterface
{
    /**
     * @return string The evaluated template as a string
     */
    public function render($name, array $parameters = []);

    /**
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name);
}
