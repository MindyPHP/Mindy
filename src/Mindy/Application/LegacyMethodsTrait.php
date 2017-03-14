<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Application;

@trigger_error('The '.__NAMESPACE__.' class is deprecated since version 3.0 and will be removed in 4.0.', E_USER_DEPRECATED);

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
