<?php

/*
 * This file is part of SentryBundle.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SentryBundle\Tests\DependencyInjection;

use Mindy\Bundle\SentryBundle\DependencyInjection\SentryExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    const CONFIG_ROOT = 'sentry';

    public function test_that_it_uses_kernel_root_parent_as_app_path_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            'kernel/root/..',
            $container->getParameter('sentry.app_path')
        );
    }

    public function test_that_it_uses_app_path_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'app_path' => 'sentry/app/path',
            ],
        ]);

        $this->assertSame(
            'sentry/app/path',
            $container->getParameter('sentry.app_path')
        );
    }

    public function test_vendor_in_default_excluded_paths()
    {
        $container = $this->getContainer();

        $this->assertContains(
            'kernel/root/../vendor',
            $container->getParameter('sentry.excluded_app_paths')
        );
    }

    public function test_that_it_uses_defined_class_as_client_class_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            'Mindy\Bundle\SentryBundle\SentrySymfonyClient',
            $container->getParameter('sentry.client')
        );
    }

    public function test_that_it_uses_client_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'client' => 'clientClass',
            ],
        ]);

        $this->assertSame(
            'clientClass',
            $container->getParameter('sentry.client')
        );
    }

    public function test_that_it_uses_kernel_environment_as_environment_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            'test',
            $container->getParameter('sentry.environment')
        );
    }

    public function test_that_it_uses_environment_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'environment' => 'custom_env',
            ],
        ]);

        $this->assertSame(
            'custom_env',
            $container->getParameter('sentry.environment')
        );
    }

    public function test_that_it_uses_null_as_dsn_default_value()
    {
        $container = $this->getContainer();

        $this->assertSame(
            null,
            $container->getParameter('sentry.dsn')
        );
    }

    public function test_that_it_uses_dsn_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'dsn' => 'custom_dsn',
            ],
        ]);

        $this->assertSame(
            'custom_dsn',
            $container->getParameter('sentry.dsn')
        );
    }

    public function test_that_it_uses_options_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'options' => [
                    'http_proxy' => 'http://user:password@host:port',
                ],
            ],
        ]);

        $options = $container->getParameter('sentry.options');

        $this->assertSame(
            'http://user:password@host:port',
            $options['http_proxy']
        );
    }

    public function test_that_it_uses_defined_class_as_exception_listener_class_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            'Mindy\Bundle\SentryBundle\EventListener\ExceptionListener',
            $container->getParameter('sentry.exception_listener')
        );
    }

    public function test_that_it_uses_exception_listener_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'exception_listener' => 'exceptionListenerClass',
            ],
        ]);

        $this->assertSame(
            'exceptionListenerClass',
            $container->getParameter('sentry.exception_listener')
        );
    }

    public function test_that_it_uses_array_with_http_exception_as_skipped_capture_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            [
                'Symfony\Component\HttpKernel\Exception\HttpExceptionInterface',
            ],
            $container->getParameter('sentry.skip_capture')
        );
    }

    public function test_that_it_uses_skipped_capture_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'skip_capture' => [
                    'classA',
                    'classB',
                ],
            ],
        ]);

        $this->assertSame(
            ['classA', 'classB'],
            $container->getParameter('sentry.skip_capture')
        );
    }

    public function test_that_it_uses_null_as_release_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            null,
            $container->getParameter('sentry.release')
        );
    }

    public function test_that_it_uses_release_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'release' => '1.0',
            ],
        ]);

        $this->assertSame(
            '1.0',
            $container->getParameter('sentry.release')
        );
    }

    public function test_that_it_uses_array_with_kernel_parent_as_prefix_by_default()
    {
        $container = $this->getContainer();

        $this->assertSame(
            ['kernel/root/..'],
            $container->getParameter('sentry.prefixes')
        );
    }

    public function test_that_it_uses_prefixes_value()
    {
        $container = $this->getContainer([
            static::CONFIG_ROOT => [
                'prefixes' => [
                    'dirA',
                    'dirB',
                ],
            ],
        ]);

        $this->assertSame(
            ['dirA', 'dirB'],
            $container->getParameter('sentry.prefixes')
        );
    }

    public function test_that_it_has_sentry_client_service_and_it_defaults_to_symfony_client()
    {
        $client = $this->getContainer()->get('sentry.client');
        $this->assertInstanceOf('Mindy\Bundle\SentryBundle\SentrySymfonyClient', $client);
    }

    public function test_that_it_has_sentry_exception_listener_and_it_defaults_to_default_exception_listener()
    {
        $client = $this->getContainer()->get('sentry.exception_listener');
        $this->assertInstanceOf('Mindy\Bundle\SentryBundle\EventListener\ExceptionListener', $client);
    }

    public function test_that_it_has_proper_event_listener_tags_for_exception_listener()
    {
        $containerBuilder = new ContainerBuilder();
        $extension = new SentryExtension();
        $extension->load([], $containerBuilder);

        $definition = $containerBuilder->getDefinition('sentry.exception_listener');
        $tags = $definition->getTag('kernel.event_listener');

        $this->assertSame(
            [
                ['event' => 'kernel.request', 'method' => 'onKernelRequest'],
                ['event' => 'kernel.exception', 'method' => 'onKernelException'],
                ['event' => 'console.exception', 'method' => 'onConsoleException'],
            ],
            $tags
        );
    }

    private function getContainer(array $options = [])
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.root_dir', 'kernel/root');
        $containerBuilder->setParameter('kernel.environment', 'test');

        $extension = new SentryExtension();

        $extension->load($options, $containerBuilder);

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
