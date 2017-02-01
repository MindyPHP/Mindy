<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MindyBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponseListener
{
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ((($result = $event->getControllerResult()) instanceof Request) === false) {
            if (is_string($result)) {
                $event->setResponse(new Response($result));
            } elseif (is_array($result)) {
                $event->setResponse(new JsonResponse($result));
            } elseif (is_object($result)) {
                if (method_exists($result, 'toArray')) {
                    $event->setResponse(new JsonResponse($result->toArray()));
                } elseif (method_exists($result, 'toJson')) {
                    $event->setResponse(new Response($result->toJson()));
                }
            }
        }
    }
}
