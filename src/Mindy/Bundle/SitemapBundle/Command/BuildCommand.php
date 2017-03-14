<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\SitemapBundle\Command;

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
            ->addOption('scheme', '', InputOption::VALUE_OPTIONAL, 'Scheme', null)
            ->addOption('savePath', '', InputOption::VALUE_OPTIONAL, 'Path for save sitemap xml', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        if (empty($host)) {
            $host = $this->getContainer()->getParameter('sitemap_host');
        }

        $scheme = $input->getOption('scheme');
        if (empty($scheme)) {
            $scheme = $this->getContainer()->getParameter('sitemap_scheme');
        }

        $webPath = $input->getOption('savePath');
        if (empty($webPath)) {
            $webPath = $this->getContainer()->getParameter('sitemap_save_path');
        }

        if (false == is_dir($webPath)) {
            throw new \Exception(sprintf('%s isnt directory', $webPath));
        }

        $this->getContainer()->get('sitemap.builder')->build($scheme, $host, $webPath);
    }
}
