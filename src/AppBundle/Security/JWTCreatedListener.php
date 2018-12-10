<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 26/11/18
 * Time: 11:24
 */

namespace AppBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        $payload       = $event->getData();
        $payload['id'] = $user->getId();

        $event->setData($payload);
    }
}