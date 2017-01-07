<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 20:50.
 */

namespace Mindy\Bundle\AdminBundle;

use Mindy\Bundle\AdminBundle\DependencyInjection\Compiler\AdminPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AdminPass());
    }
}
