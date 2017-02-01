<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\OrmBundle\Command;

use Mindy\Bundle\OrmBundle\Command\Helper\ConfigurationHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputOption;

trait MigrationConfigurationTrait
{
    protected function configure()
    {
        parent::configure();

        $this->addOption('bundle', 'b', InputOption::VALUE_REQUIRED);
        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'Connection name', 'default');
    }

    public function setHelperSet(HelperSet $helperSet)
    {
        $helperSet->set(new ConfigurationHelper($this->container), 'configuration');
        parent::setHelperSet($helperSet);
    }
}
