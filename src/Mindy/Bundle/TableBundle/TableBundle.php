<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 17:29.
 */

namespace Mindy\Bundle\TableBundle;

use Mindy\Bundle\TableBundle\DependencyInjection\Compiler\TablePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TableBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TablePass());
    }
}
