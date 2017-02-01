<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Application;

/**
 * Class LegacyMethodsTrait.
 *
 * @method \Symfony\Component\DependencyInjection\ContainerInterface getContainer()
 */
trait LegacyMethodsTrait
{
    public function hasComponent($id)
    {
        return $this->getContainer()->has($id);
    }

    public function getComponent($id)
    {
        return $this->getContainer()->get($id);
    }

    public function getUser()
    {
        if (!$this->getContainer()->has('security.token_storage')) {
            return;
        }

        if (null === $token = $this->getContainer()->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
}
