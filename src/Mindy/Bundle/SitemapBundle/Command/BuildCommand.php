<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 00:32
 */

namespace Mindy\Bundle\SitemapBundle\Command;

use Mindy\Bundle\SitemapBundle\Sitemap\Entity\LocationEntity;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sitemap:build')
            ->addOption('host', '', InputOption::VALUE_OPTIONAL, 'Http host', null)
            ->addOption('savePath', '', InputOption::VALUE_OPTIONAL, 'Path for save sitemap xml', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        if (empty($host)) {
            $host = $this->getContainer()->getParameter('sitemap_host');
        }

        $webPath = $input->getOption('savePath');
        if (empty($webPath)) {
            $webPath = $this->getContainer()->getParameter('sitemap_save_path');
        }

        if (false == is_dir($webPath)) {
            throw new \Exception(sprintf("%s isnt directory", $webPath));
        }

        $this->getContainer()->get('sitemap.builder')->build($host, $webPath);
    }
}