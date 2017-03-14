<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\SeoBundle\Provider;

use Mindy\Bundle\SeoBundle\Model\Seo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MetaProvider.
 */
class SeoProvider
{
    /**
     * @param Request $request
     *
     * @return \Mindy\Orm\ModelInterface|null
     */
    public function getMeta(Request $request)
    {
        return $this->fetchMeta($request->getHost(), $request->getPathInfo());
    }

    /**
     * @param $host
     * @param $url
     * @return \Mindy\Orm\ModelInterface|null
     */
    public function fetchMeta($host, $url)
    {
        return Seo::objects()->get([
            'host' => $host,
            'url' => $url,
        ]);
    }
}
