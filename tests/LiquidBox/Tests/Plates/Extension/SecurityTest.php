<?php

namespace LiquidBox\Tests\Plates\Extension;

use Silex\WebTestCase;

/**
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class SecurityTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new \Silex\Application();

        $app->register(new \LiquidBox\Silex\Provider\PlatesServiceProvider(), [
            'plates.directory' => __DIR__ . '/../../Resources/views',
        ]);
        $app->register(new \Silex\Provider\SecurityServiceProvider(), [
            'security.firewalls' => [
                'default' => [
                    'anonymous' => true,
                ],
            ],
        ]);

        $app['request'] = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        $app->boot();

        return $app;
    }

    public function testRegister()
    {
        $this->assertTrue($this->app['plates']->doesFunctionExist('is_granted'));
    }

    /*public function testIsGrantedCall()
    {
        $this->assertTrue($this->app['plates']->getFunction('is_granted')->call(null, ['ROLE_FOO']));
    }*/
}
