<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\MindyBundle\Library;

use Mindy\Template\Library;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class CoreLibrary extends Library implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'switched_user' => function () {
                $authChecker = $this->container->get('security.authorization_checker');
                $tokenStorage = $this->container->get('security.token_storage');

                if ($authChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
                    foreach ($tokenStorage->getToken()->getRoles() as $role) {
                        if ($role instanceof SwitchUserRole) {
                            return $role->getSource()->getUser();
                        }
                    }
                }

                return null;
            },
            'is_granted' => function ($attributes, $object = null) {
                if (!$this->container->has('security.authorization_checker')) {
                    throw new \LogicException('The SecurityBundle is not registered in your application.');
                }

                return $this->container->get('security.authorization_checker')->isGranted($attributes, $object);
            },
            'path' => function ($route, array $parameters = []) {
                return $this->container->get('router')->generate($route, $parameters);
            },
            'url' => function ($route, array $parameters = []) {
                return $this->container->get('router')->generate($route, $parameters);
            },
            'rand' => function ($min, $max) {
                return rand($min, $max);
            },
            'd' => function () {
                dump(func_get_args());
                die();
            },
            't' => function ($id, array $parameters = [], $domain = null, $locale = null) {
                return $this->container->get('translator')->trans($id, $parameters, $domain, $locale);
            },
            'trans' => function ($id, array $parameters = [], $domain = null, $locale = null) {
                return $this->container->get('translator')->trans($id, $parameters, $domain, $locale);
            },
            'transChoice' => function ($id, $number, array $parameters = [], $domain = null, $locale = null) {
                return $this->container->get('translator')->transChoice($id, $number, $parameters, $domain, $locale);
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
