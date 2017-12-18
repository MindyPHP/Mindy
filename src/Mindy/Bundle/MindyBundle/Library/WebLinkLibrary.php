<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\MindyBundle\Library;

use Fig\Link\GenericLinkProvider;
use Fig\Link\Link;
use Mindy\Template\Library;
use Symfony\Component\HttpFoundation\RequestStack;

class WebLinkLibrary extends Library
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * WebLinkLibrary constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'link' => [$this, 'link'],
            'preload' => [$this, 'preload'],
            'dns_prefetch' => [$this, 'dnsPrefetch'],
            'preconnect' => [$this, 'preconnect'],
            'prefetch' => [$this, 'prefetch'],
            'prerender' => [$this, 'prerender'],
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }

    /**
     * Adds a "Link" HTTP header.
     *
     * @param string $uri        The relation URI
     * @param string $rel        The relation type (e.g. "preload", "prefetch", "prerender" or "dns-prefetch")
     * @param array  $attributes The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
     *
     * @return string The relation URI
     */
    public function link($uri, $rel, array $attributes = [])
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            return $uri;
        }
        $link = new Link($rel, $uri);
        foreach ($attributes as $key => $value) {
            $link = $link->withAttribute($key, $value);
        }
        $linkProvider = $request->attributes->get('_links', new GenericLinkProvider());
        $request->attributes->set('_links', $linkProvider->withLink($link));

        return $uri;
    }

    /**
     * Preloads a resource.
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "array('as' => true)", "array('crossorigin' => 'use-credentials')")
     *
     * @return string The path of the asset
     */
    public function preload($uri, array $attributes = [])
    {
        return $this->link($uri, 'preload', $attributes);
    }

    /**
     * Resolves a resource origin as early as possible.
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
     *
     * @return string The path of the asset
     */
    public function dnsPrefetch($uri, array $attributes = [])
    {
        return $this->link($uri, 'dns-prefetch', $attributes);
    }

    /**
     * Initiates a early connection to a resource (DNS resolution, TCP handshake, TLS negotiation).
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
     *
     * @return string The path of the asset
     */
    public function preconnect($uri, array $attributes = [])
    {
        return $this->link($uri, 'preconnect', $attributes);
    }

    /**
     * Indicates to the client that it should prefetch this resource.
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
     *
     * @return string The path of the asset
     */
    public function prefetch($uri, array $attributes = [])
    {
        return $this->link($uri, 'prefetch', $attributes);
    }

    /**
     * Indicates to the client that it should prerender this resource .
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
     *
     * @return string The path of the asset
     */
    public function prerender($uri, array $attributes = [])
    {
        return $this->link($uri, 'prerender', $attributes);
    }
}
