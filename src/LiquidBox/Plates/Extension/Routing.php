<?php

namespace LiquidBox\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides integration of the Routing component with Plates.
 *
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class Routing implements ExtensionInterface
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $relative
     *
     * @return string
     */
    public function getPath($name, array $parameters = array(), $relative = false)
    {
        return $this->generator->generate(
            $name,
            $parameters,
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $schemeRelative
     *
     * @return string
     */
    public function getUrl($name, array $parameters = array(), $schemeRelative = false)
    {
        return $this->generator->generate(
            $name,
            $parameters,
            $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('path', array($this, 'getPath'));
        $engine->registerFunction('url', array($this, 'getUrl'));
    }
}
