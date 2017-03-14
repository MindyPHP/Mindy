<?php

/*
 * This file is part of SentryBundle.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SentryBundle\Test;

use Mindy\Bundle\SentryBundle\SentryBundle;
use Mindy\Bundle\SentryBundle\SentrySymfonyClient;

class SentrySymfonyClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_that_it_sets_sdk_name_and_version()
    {
        $client = new SentrySymfonyClient();

        $data = $client->get_default_data();

        $this->assertEquals('sentry-symfony', $data['sdk']['name']);
        $this->assertEquals(SentryBundle::VERSION, $data['sdk']['version']);
    }

    public function test_that_it_forwards_options()
    {
        $client = new SentrySymfonyClient('https://a:b@app.getsentry.com/project', [
            'name' => 'test',
        ]);

        $data = $client->get_default_data();

        // Not a big fan of doing this kind of assertions, couples tests to external API...
        // Perhaps, refactor is needed for this class?
        $this->assertEquals('test', $data['server_name']);
        $this->assertEquals('https://app.getsentry.com/api/project/store/', $client->getServerEndpoint(null));
        $this->assertEquals('a', $client->public_key);
        $this->assertEquals('b', $client->secret_key);
    }
}
