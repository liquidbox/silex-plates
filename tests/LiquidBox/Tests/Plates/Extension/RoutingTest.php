<?php

namespace LiquidBox\Tests\Plates\Extension;

use LiquidBox\Silex\Provider\PlatesServiceProvider;
use Silex\Application;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class RoutingTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app->register(new PlatesServiceProvider(), [
            'plates.directory' => __DIR__ . '/../../Resources/views',
        ]);

        $app['url_generator'] = function ($app) {
            $app->flush();
            return new UrlGenerator($app['routes'], $app['request_context']);
        };

        $app->get('/', function () {
        })->bind('homepage');
        $app->get('/hello/{name}', function ($name) {
        })->bind('hello');

        $app['request_stack']->push(Request::createFromGlobals());

        return $app;
    }

    public function testRegister()
    {
        $this->assertTrue($this->app['plates']->doesFunctionExist('path'), 'Function path() does not exist.');
        $this->assertTrue($this->app['plates']->doesFunctionExist('url'), 'Function url() does not exist.');
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
