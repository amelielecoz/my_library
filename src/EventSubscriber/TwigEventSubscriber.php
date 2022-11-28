<?php

namespace App\EventSubscriber;

use App\Repository\BookRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private Environment $twig, private BookRepository $bookRepository)
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->twig->addGlobal('books', $this->bookRepository->findAll());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
