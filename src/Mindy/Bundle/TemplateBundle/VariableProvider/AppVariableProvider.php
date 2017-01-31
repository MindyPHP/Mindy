<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\TemplateBundle\VariableProvider;

use Mindy\Template\VariableProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class AppVariableProvider.
 */
class AppVariableProvider implements VariableProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'request' => $this->getRequest(),
            'user' => $this->getUser(),
        ];
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @return mixed
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            return;
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
