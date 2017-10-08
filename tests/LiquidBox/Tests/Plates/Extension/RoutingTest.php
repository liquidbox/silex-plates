<?php

namespace LiquidBox\Tests\Plates\Extension;

use Silex\WebTestCase;

/**
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class RoutingTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new \Silex\Application();

        $app->register(new \LiquidBox\Silex\Provider\PlatesServiceProvider(), [
            'plates.directory' => __DIR__ . '/../../Resources/views',
        ]);
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $app->get('/', function () {})->bind('homepage');
        $app->get('/hello/{name}', function ($name) {})->bind('hello');

        $app['request'] = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        return $app;
    }

    public function testRegister()
    {
        $this->assertTrue($this->app['plates']->doesFunctionExist('path'));
        $this->assertTrue($this->app['plates']->doesFunctionExist('url'));
    }

    public function testPathCall()
    {
        $this->assertEquals('/', $this->app['plates']->getFunction('path')->call(null, ['homepage']));
    }

    public function testUrlCall()
    {
        $this->assertEquals(
            'http://localhost/hello/Jonathan-Paul',
            $this->app['plates']->getFunction('url')->call(null, ['hello', ['name' => 'Jonathan-Paul']])
        );
    }
}
