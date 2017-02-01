<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MindyBundle\Library;

use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Expression\AttributeExpression;
use Mindy\Template\Library;
use Mindy\Template\Token;
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
        return [
            'url' => 'parseUrl',
        ];
    }

    public function parseUrl($token)
    {
        $name = null;
        $params = [];
        $route = $this->parser->parseExpression();
        while (
            (
                $this->stream->test(Token::NAME) ||
                $this->stream->test(Token::NUMBER) ||
                $this->stream->test(Token::STRING)
            ) && !$this->stream->test(Token::BLOCK_END)
        ) {
            if ($this->stream->consume(Token::NAME, 'with')) {
                if ($this->stream->look()->test(Token::OPERATOR, '=')) {
                    $this->stream->expect(Token::OPERATOR, '[');
                    $params = $this->parser->parseArrayExpression();
                    $this->stream->expect(Token::OPERATOR, ']');
                } else {
                    $params = $this->parser->parseExpression();
                }
            } elseif ($this->stream->test(Token::NAME) && $this->stream->look()->test(Token::OPERATOR, '=')) {
                $key = $this->parser->parseName()->getValue();
                $this->stream->next();
                $params[$key] = $this->parser->parseExpression();
            } elseif ($this->stream->test(Token::NAME, 'as')) {
                $this->stream->next();
                $name = $this->parser->parseName()->getValue();
            } elseif ($this->stream->test(Token::NAME)) {
                $expression = $this->parser->parseExpression();
                if (
                    $expression instanceof ArrayExpression ||
                    $expression instanceof AttributeExpression
                ) {
                    $params = $expression;
                    break;
                }
                $params[] = $expression;
            } else {
                $params[] = $this->parser->parseExpression();
            }
        }

        $this->stream->expect(Token::BLOCK_END);

        return new UrlNode($token->getLine(), $route, $params, $name);
    }
}
