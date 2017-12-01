<?php
/**
 * Plates template engine service provider for the Silex micro-framework.
 *
 * @see http://platesphp.com
 */

namespace LiquidBox\Silex\Provider;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use League\Plates\Extension\URI;
use LiquidBox\Plates\Extension\Routing;
use LiquidBox\Plates\Extension\Security;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Plates Provider.
 *
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class PlatesServiceProvider implements ServiceProviderInterface
{
    private function addData(Container $app, Engine $engine)
    {
        if (!empty($app['plates.data'])) {
            if (isset($app['plates.data'][0])) {
                if (is_string($app['plates.data'][1][0])) {
                    $engine->addData($app['plates.data'][0], $app['plates.data'][1]);
                } else {
                    foreach ($app['plates.data'] as $args) {
                        call_user_func_array(array($engine, 'addData'), $args);
                    }
                }
            } else {
                $engine->addData($app['plates.data']);
            }
        }
    }

    private function addFolders(Container $app, Engine $engine)
    {
        if (!empty($app['plates.folders'])) {
            if (isset($app['plates.folders'][0])) {
                if (is_array($app['plates.folders'][0])) {
                    foreach ($app['plates.folders'] as $args) {
                        call_user_func_array(array($engine, 'addFolder'), $args);
                    }
                } else {
                    call_user_func_array(array($engine, 'addFolder'), $app['plates.folders']);
                }
            } else {
                foreach ($app['plates.folders'] as $name => $path) {
                    $engine->addFolder($name, $path);
                }
            }
        }
    }

    private function loadExtensionAsset(Container $app, Engine $engine)
    {
        if (!empty($app['plates.extension.asset'])) {
            if (count($app['plates.extension.asset']) > 1) {
                $engine->loadExtension(new Asset($app['plates.extension.asset'][0], $app['plates.extension.asset'][1]));
            } else {
                $engine->loadExtension(new Asset(
                    is_array($app['plates.extension.asset']) ?
                        $app['plates.extension.asset'][0] :
                        $app['plates.extension.asset']
                ));
            }
        }
    }

    private function loadExtensionURI(Container $app, Engine $engine)
    {
        if (null !== ($currentRequest = $app['request_stack']->getCurrentRequest())) {
            $engine->loadExtension(new URI($currentRequest->getPathInfo()));
        }
    }

    private function loadExtensions(Container $app, Engine $engine)
    {
        $this->loadExtensionAsset($app, $engine);
        $this->loadExtensionURI($app, $engine);

        if (isset($app['url_generator'])) {
            $engine->loadExtension(new Routing($app['url_generator']));
        }
        if (isset($app['security.authorization_checker'])) {
            $engine->loadExtension(new Security($app['security.authorization_checker']));
        }
    }

    private function registerFunctions(Container $app, Engine $engine)
    {
        if (!empty($app['plates.functions'])) {
            foreach ($app['plates.functions'] as $name => $callback) {
                $engine->registerFunction($name, $callback);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['plates.directory'] = null;
        $app['plates.file_extension'] = '';

        $app['plates'] = function (Container $app) {
            return $app['plates.loader']($app['plates.engine_factory'](
                $app['plates.directory'],
                $app['plates.file_extension']
            ));
        };
        $app['plates.engine_factory'] = $app->protect(function ($directory = null, $fileExtension = '') {
            return (strlen($fileExtension) || null === $fileExtension) ?
                new Engine($directory, $fileExtension) :
                new Engine($directory);
        });
        $app['plates.extension_loader.asset'] = $app->protect(function ($path, $filenameMethod = false) use ($app) {
            $app['plates']->loadExtension(new Asset($path, $filenameMethod));
        });
        $app['plates.loader'] = $app->protect(function (Engine $engine) use ($app) {
            $this->addFolders($app, $engine);
            $this->addData($app, $engine);
            $this->loadExtensions($app, $engine);
            $this->registerFunctions($app, $engine);

            return $engine;
        });
    }
}
