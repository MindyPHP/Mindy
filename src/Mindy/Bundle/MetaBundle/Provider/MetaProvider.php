<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/10/16
 * Time: 15:51.
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
