<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Sitemap;

use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Sitemap\AbstractSitemapProvider;
use Mindy\Sitemap\Entity\LocationEntity;

class PageSitemapProvider extends AbstractSitemapProvider
{
    /**
     * @param string $scheme
     * @param string $host
     *
     * @return \Generator
     */
    public function build($scheme, $host)
    {
        foreach (Page::objects()->asArray()->batch(100) as $chunk) {
            foreach ($chunk as $object) {
                yield (new LocationEntity())
                    ->setLastmod(new \DateTime($object['updated_at']))
                    ->setLocation($this->generateLoc($scheme, $host, 'page_view', [
                        'url' => $object['url'],
                    ]));
            }
        }
    }
}
