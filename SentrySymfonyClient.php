<?php

/*
 * This file is part of SentryBundle.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SentryBundle;

class SentrySymfonyClient extends \Raven_Client
{
    public function __construct($dsn = null, $options = [], $error_types = '')
    {
        if (is_string($error_types) && !empty($error_types)) {
            $exParser = new ErrorTypesParser($error_types);
            $options['error_types'] = $exParser->parse();
        }

        $options['sdk'] = [
            'name' => 'sentry-symfony',
            'version' => SentryBundle::VERSION,
        ];
        parent::__construct($dsn, $options);
    }
}
