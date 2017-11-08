<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Sitemap;

use Exception;
use Mindy\Sitemap\Entity\SiteMapEntity;
use Mindy\Sitemap\Entity\SiteMapIndexEntity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Builder.
 */
class Builder
{
    const LIMIT = 50000;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * Builder constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param SitemapProviderInterface $provider
     */
    public function addProvider(SitemapProviderInterface $provider)
    {
        $provider->setUrlGenerator($this->urlGenerator);

        $this->providers[] = $provider;
    }

    /**
     * @param string $filePath
     * @param string $fileContent
     *
     * @throws Exception
     *
     * @return $this
     */
    public function saveFile($filePath, $fileContent)
    {
        $filesystem = new Filesystem();
        if (false === $filesystem->exists(dirname($filePath))) {
            throw new Exception('Directory "'.dirname($filePath).'" does not exist!');
        }
        try {
            $filesystem->mkdir(dirname($filePath));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        file_put_contents($filePath, $fileContent);

        return $this;
    }

    /**
     * @param $path
     * @param array $entities
     *
     * @return SiteMapEntity
     */
    public function saveSitemap($path, array $entities)
    {
        $sitemap = new SiteMapEntity();
        foreach ($entities as $location) {
            $sitemap->setLastmod(new \DateTime());
            $sitemap->addLocation($location);
        }

        $this->saveFile($path, $sitemap->getXml());

        return $sitemap;
    }

    /**
     * @param $scheme
     * @param $host
     * @param $path
     *
     * @return array
     */
    public function build($scheme, $host, $path, $name = 'sitemap.xml')
    {
        $sitemaps = [];

        $entities = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->build($scheme, $host) as $location) {
                $entities[] = $location;
            }
        }

        if (count($entities) > self::LIMIT) {
            $sitemapIndex = new SiteMapIndexEntity();
            foreach (array_chunk($entities, self::LIMIT) as $i => $chunk) {
                $sitemap = $this->saveSitemap(sprintf('%s/sitemap-%s.xml', $path, $i), $chunk);
                $sitemaps[] = $loc = sprintf('%s/sitemap-%s.xml', rtrim($host, '/'), $i);
                $sitemap->setLoc($loc);

                $sitemapIndex->addSiteMap($sitemap);
            }

            $this->saveFile(
                sprintf('%s/%s', $path, $name),
                $sitemapIndex->getXml()
            );
        } else {
            $this->saveSitemap(sprintf('%s/%s', $path, $name), $entities);
        }

        $sitemaps[] = sprintf('%s/%s', rtrim($host, '/'), $name);

        return $sitemaps;
    }
}
