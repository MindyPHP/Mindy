<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 20:02
 */

namespace Mindy\Bundle\AdminBundle\Admin;

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