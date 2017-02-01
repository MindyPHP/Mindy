<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MetaBundle\Provider;

use Mindy\Bundle\MetaBundle\Model\Meta;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MetaProvider.
 */
class MetaProvider
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function getMeta(Request $request)
    {
        return Meta::objects()->asArray()->get([
            'host' => $request->getHost(),
            'url' => $request->getPathInfo(),
        ]);
    }
}
