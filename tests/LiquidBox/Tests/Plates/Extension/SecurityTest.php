<?php

namespace LiquidBox\Tests\Plates\Extension;

use LiquidBox\Silex\Provider\PlatesServiceProvider;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class SecurityTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app->register(new PlatesServiceProvider(), [
            'plates.directory' => __DIR__ . '/../../Resources/views',
        ]);
        $app->register(new SecurityServiceProvider(), [
            'security.firewalls' => [
                'default' => [
                    'anonymous' => true,
                ],
            ],
        ]);

        $app['request_stack']->push(Request::createFromGlobals());

        $app->boot();

        return $app;
    }

    public function testRegister()
    {
        $this->assertTrue(
            $this->app['plates']->doesFunctionExist('is_granted'),
            'Function is_granted() does not exist.'
        );
    }

    /*public function testIsGrantedCall()
    {
        $this->assertTrue($this->app['plates']->getFunction('is_granted')->call(null, ['ROLE_FOO']));
    }*/
}
