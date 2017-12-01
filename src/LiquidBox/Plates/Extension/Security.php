<?php

namespace LiquidBox\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Security exposes security context features.
 *
 * @author Jonathan-Paul Marois <jonathanpaul.marois@gmail.com>
 */
class Security implements ExtensionInterface
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker = null)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isGranted($attributes, $object = null, $field = null)
    {
        if (null === $this->authorizationChecker) {
            return false;
        }
        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('is_granted', array($this, 'isGranted'));
    }
}
