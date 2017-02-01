<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MindyBundle\Controller;

use Mindy\Pagination\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Controller extends BaseController
{
    /**
     * @param $source
     * @param array $parameters
     *
     * @return Pagination
     */
    protected function createPagination($source, array $parameters = [])
    {
        if ($this->container->has('pagination.factory')) {
            return $this->container->get('pagination.factory')->createPagination(
                $source, $parameters,
                $this->container->get('pagination.handler')
            );
        }
        throw new \LogicException('You can not use the "createPagination" method if the Pagination Component are not available.');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($view, array $parameters = [])
    {
        if ($this->container->has('template')) {
            return $this->container->get('template')->render($view, $parameters);
        }
        throw new \LogicException('You can not use the "renderView" method if the Template Component are not available.');
    }

    /**
     * {@inheritdoc}
     */
    protected function render($view, array $parameters = [], Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->renderTemplate($view, $parameters));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function stream($view, array $parameters = [], StreamedResponse $response = null)
    {
        if ($this->container->has('template')) {
            $template = $this->container->get('template');

            $callback = function () use ($template, $view, $parameters) {
                echo $template->render($view, $parameters);
            };
        } else {
            throw new \LogicException('You can not use the "stream" method if the Templating Component are not available.');
        }

        if (null === $response) {
            return new StreamedResponse($callback);
        }

        $response->setCallback($callback);

        return $response;
    }
}
