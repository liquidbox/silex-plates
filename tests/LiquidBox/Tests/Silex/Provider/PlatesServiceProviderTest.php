<?php

namespace LiquidBox\Tests\Silex\Provider;

use LiquidBox\Silex\Provider\PlatesServiceProvider;
use Silex\Application;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class PlatesServiceProviderTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app['request'] = Request::createFromGlobals();

        return $app;
    }

    /**
     * @return array
     */
    public function platesAssetProvider()
    {
        return [
            'string' => [
                __DIR__ . '/../../Resources/web',
            ],
            'singleton' => [
                [__DIR__ . '/../../Resources/web'],
            ],
            'array' => [
                [__DIR__ . '/../../Resources/web', true],
            ],
        ];
    }

    /**
     * @return array
     */
    public function platesDataProvider()
    {
        return [
            'global' => [
                ['name' => 'Jonathan-Paul'],
            ],
            'singleton' => [
                [['name' => 'Jonathan-Paul'], 'index'],
            ],
            'array' => [
                [
                    [['title' => 'Test', 'name' => 'Fabien'], ['templates/article', 'index']],
                    [['name' => 'Igor']],
                    [['name' => 'Jonathan-Paul'], 'index'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function platesFoldersProvider()
    {
        return [
            'singleton' => [
                ['sections', __DIR__ . '/../../Resources/views/templates', true],
            ],
            'array' => [[
                ['head', __DIR__ . '/../../Resources/views/headers'],
                ['sections', __DIR__ . '/../../Resources/views/templates', true],
            ]],
            'compatible' => [[
                'head' => __DIR__ . '/../../Resources/views/headers',
                'sections' => __DIR__ . '/../../Resources/views/templates',
            ]],
        ];
    }

    public function testRegister()
    {
        $this->app->register(new PlatesServiceProvider());

        $this->assertInstanceOf('\\League\\Plates\\Engine', $this->app['plates']);

        return $this->app;
    }

    /**
     * @depends testRegister
     */
    public function testEngineFactory(Application $app)
    {
        $this->assertInstanceOf('\\League\\Plates\\Engine', $app['plates.engine_factory']());
    }

    /**
     * @depends testRegister
     */
    public function testExtensionLoaderAsset(Application $app)
    {
        $app['plates.extension_loader.asset'](__DIR__ . '/../../Resources/web');

        $this->assertTrue($app['plates']->doesFunctionExist('asset'));
    }

    /**
     * @depends testRegister
     */
    public function testExtensionURI(Application $app)
    {
        $this->assertTrue($app['plates']->doesFunctionExist('uri'));
    }

    public function testRegisterWithDirectory()
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.directory' => __DIR__ . '/../../Resources/views',
        ]);

        $this->assertTrue($this->app['plates']->exists('index'));
    }

    /**
     * @dataProvider platesAssetProvider
     *
     * @param array|string $mixed
     */
    public function testRegisterWithExtensionAsset($platesAsset)
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.extension.asset' => $platesAsset,
        ]);

        $this->assertTrue($this->app['plates']->doesFunctionExist('asset'));
    }

    public function testRegisterWithFileExtension()
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.file_extension' => null,
        ]);

        $this->app['plates']->setDirectory(__DIR__ . '/../../Resources/views');

        $this->assertTrue($this->app['plates']->exists('index.php'));
    }

    /**
     * @dataProvider platesFoldersProvider
     */
    public function testRegisterWithFolders(array $platesFolders)
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.folders' => $platesFolders,
        ]);

        $this->assertTrue($this->app['plates']->exists('sections::article'));
    }

    public function testRegisterWithFunctions()
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.functions' => [
                'bar' => function () {
                    return true;
                },
                'foo' => function () {
                    return false;
                },
            ],
        ]);

        $this->assertTrue($this->app['plates']->doesFunctionExist('foo'));
    }

    public function testRegisterWithPath()
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.path' => __DIR__ . '/../../Resources/views',
        ]);

        $this->assertTrue($this->app['plates']->exists('index'));
    }

    public function testRegisterWithSharedVariables()
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.data' => ['name' => 'Jonathan-Paul'],
        ]);

        $this->assertEquals($this->app['plates']->getData()['name'], 'Jonathan-Paul');
    }

    /**
     * @dataProvider platesDataProvider
     */
    public function testRegisterWithTemplateVariables(array $platesData)
    {
        $this->app->register(new PlatesServiceProvider(), [
            'plates.data' => $platesData,
        ]);

        $this->assertEquals($this->app['plates']->getData('index')['name'], 'Jonathan-Paul');
    }
}
