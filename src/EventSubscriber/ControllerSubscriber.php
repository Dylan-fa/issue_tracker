<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * This class is an event subscriber and runs when the kernel controller event is triggered. The event is triggered right before a controller is ran.
 */
class ControllerSubscriber implements EventSubscriberInterface
{
    private $security;
    private $environment;

    public function __construct(Security $security, Environment $environment)
    {
        $this->security = $security;
        $this->environment = $environment;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $environment = $this->environment;
        $user = $this->security->getUser();
        // is user is logged in
        if ($user) {
            // get roles and check if they are disabled
            $roles = $user->getRoles();
            if (in_array('ROLE_DISABLED', $roles)) {
                // if they are disabled, override the controller and deny them access with a custom response.
                $event->setController(function() use ($environment) {
                    $html = $environment->render('disabled.html.twig', []);
                    $response = new Response();
                    $response->setContent($html);
                    $response->setStatusCode(403);
                    return $response;
               });
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}