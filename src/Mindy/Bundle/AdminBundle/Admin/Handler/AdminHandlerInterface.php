<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin\Handler;

use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;

interface AdminHandlerInterface
{
    /**
     * @param QuerySet|Manager $qs
     */
    public function handle($qs);

    /**
     * @return array|string
     */
    public function getValue();
}
