<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 00:32.
 */

namespace Mindy\Bundle\SitemapBundle;

use Mindy\Bundle\SitemapBundle\DependencyInjection\Compiler\SitemapPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SitemapBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SitemapPass());
    }
}
