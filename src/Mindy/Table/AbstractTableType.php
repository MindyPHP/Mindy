<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 01/12/2016
 * Time: 21:44.
 */

namespace Mindy\Component\Table;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AbstractTableType implements TableTypeInterface
{
    protected $router;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildTable(TableBuilder $tableBuilder, array $options)
    {
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param mixed  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($this->router === null) {
            throw new \LogicException('Please set @router as dependency');
        }

        return $this->router->generate($route, $parameters, $referenceType);
    }
}
