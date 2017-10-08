<?php

namespace LiquidBox\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Security exposes security context features.
 *
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class Security implements ExtensionInterface
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext = null)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isGranted($attributes, $object = null, $field = null)
    {
        if (null === $this->securityContext) {
            return false;
        }
        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        return $this->securityContext->isGranted($attributes, $object);
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('is_granted', array($this, 'isGranted'));
    }
}
