<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Security;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\PostLogoutEvent;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutHandler implements LogoutHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ContaoFrameworkInterface $framework
     * @param LoggerInterface|null     $logger
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ContaoFrameworkInterface $framework, LoggerInterface $logger = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->framework = $framework;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->framework->initialize();

        if (null !== $this->logger) {
            $this->logger->info(
                sprintf('User "%s" has logged out', $user->getUsername()),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::ACCESS)]
            );
        }

        $this->eventDispatcher->dispatch(ContaoCoreEvents::POST_LOGOUT, new PostLogoutEvent($user));
        $this->triggerPostLogoutHook($user);
    }

    /**
     * Triggers the postLogout hook.
     *
     * @param User $user
     */
    private function triggerPostLogoutHook(User $user): void
    {
        if (empty($GLOBALS['TL_HOOKS']['postLogout']) || !\is_array($GLOBALS['TL_HOOKS']['postLogout'])) {
            return;
        }

        @trigger_error('Using the postLogout hook has been deprecated and will no longer work in Contao 5.0. Use the contao.post_logout event instead.', E_USER_DEPRECATED);

        foreach ($GLOBALS['TL_HOOKS']['postLogout'] as $callback) {
            $this->framework->createInstance($callback[0])->{$callback[1]}($user);
        }
    }
}
