<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Pagination\Handler;

use Mindy\Pagination\Exception\IncorrectPageException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RequestPaginationHandler.
 */
class RequestPaginationHandler implements PaginationHandlerInterface
{
    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * RequestPaginationHandler constructor.
     *
     * @param RequestStack          $requestStack
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageSize($key)
    {
        return $this->request->query->getInt($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getPage($key)
    {
        if ($this->request->query->has($key)) {
            return $this->request->query->getInt($key);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlForQueryParam($key, $value)
    {
        $attributes = $this->request->attributes;
        if ($attributes->has('_forwarded')) {
            $attributes = $attributes->get('_forwarded');
        }

        $params = $attributes->all();
        unset($params['_route']);

        return $this->urlGenerator->generate($attributes->get('_route'), array_merge(
            $params,
            $this->request->query->all(),
            [$key => $value]
        ));
    }

    /**
     * @param callable $callback
     */
    public function setIncorrectPageCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Throw exception or redirect user to correct page.
     */
    public function wrongPageCallback()
    {
        if (is_callable($this->callback)) {
            $this->callback->__invoke($this);
        } else {
            throw new IncorrectPageException();
        }
    }
}
