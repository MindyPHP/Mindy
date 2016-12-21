<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:04.
 */

namespace Mindy\Component\Table\Column;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractColumn implements ColumnInterface
{
    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|PropertyPathInterface
     */
    protected $path;

    /**
     * @var bool
     */
    protected $sorting = false;

    protected $requestStack;
    protected $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    protected function getRequest()
    {
        return $this->requestStack->getMasterRequest();
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function getValue($row)
    {
        if (null === ($path = $this->path)) {
            $path = is_array($row) ? sprintf('[%s]', $this->name) : $this->name;
        }

        return $this->accessor->getValue($row, $path);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param PropertyAccessor $accessor
     */
    public function setPropertyAccessor(PropertyAccessor $accessor)
    {
        $this->accessor = $accessor;
    }

    public function getLabel()
    {
        if (null === $this->label) {
            $label = $this->name;
        } else {
            $label = $this->label;
        }

        if ($this->sorting) {
            $request = $this->getRequest();
            $currentOrder = $request->query->get('order', '');
            $column = $currentOrder == $this->name ? '-'.$this->name : $this->name;

            $url = $this->router->generate(
                $request->attributes->get('_route'),
                array_merge($request->query->all(), ['order' => $column])
            );

            return sprintf('<a href="%s">%s</a>', $url, $label);
        }

        return $label;
    }
}
