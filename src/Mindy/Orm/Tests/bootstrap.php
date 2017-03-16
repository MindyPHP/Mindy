<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/../vendor/autoload.php';

use Mindy\Orm\Tests\Connections;

define('MINDY_ORM_TEST', true);

$mockConnections = new Connections(include(__DIR__.'/connections_settings.php'));
