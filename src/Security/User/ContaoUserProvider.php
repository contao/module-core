<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Security\User;

use Contao\BackendUser;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\FrontendUser;
use Contao\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

@trigger_error('Using the ContaoUserProvider has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

/**
 * Class ContaoUserProvider.
 *
 * @deprecated Deprecated since Contao 4.x, to be removed in Contao 5.0.
 *             Use the BackendUserProvider or FrontendUserProvider service instead.
 */
class ContaoUserProvider implements ContainerAwareInterface, UserProviderInterface
{
    use ContainerAwareTrait;

    /**
     * @var ScopeMatcher
     */
    protected $scopeMatcher;

    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @param ContaoFrameworkInterface $framework
     * @param ScopeMatcher             $scopeMatcher
     *
     * @deprecated Using the ContaoUserProvider has been deprecated and will no longer work in Contao 5.0.
     */
    public function __construct(ContaoFrameworkInterface $framework, ScopeMatcher $scopeMatcher)
    {
        @trigger_error('Using the ContaoUserProvider has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

        $this->framework = $framework;
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * {@inheritdoc}
     *
     * @return BackendUser|FrontendUser
     *
     * @deprecated Using ContaoUserProvider::loadByUsername has been deprecated and will no longer work in Contao 5.0.
     */
    public function loadUserByUsername($username): User
    {
        @trigger_error('Using ContaoUserProvider::loadByUsername has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

        if ($this->isBackendUsername($username)) {
            $this->framework->initialize();

            return BackendUser::getInstance();
        }

        if ($this->isFrontendUsername($username)) {
            $this->framework->initialize();

            return FrontendUser::getInstance();
        }

        throw new UsernameNotFoundException('Can only load user "frontend" or "backend".');
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Using ContaoUserProvider::refreshUser has been deprecated and will no longer work in Contao 5.0.
     */
    public function refreshUser(UserInterface $user): void
    {
        @trigger_error('Using ContaoUserProvider::refreshUser has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

        throw new UnsupportedUserException('Cannot refresh a Contao user.');
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Using ContaoUserProvider::supportsClass has been deprecated and will no longer work in Contao 5.0.
     */
    public function supportsClass($class): bool
    {
        @trigger_error('Using ContaoUserProvider::supportsClass has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

        return is_subclass_of($class, User::class);
    }

    /**
     * Checks if the given username can be mapped to a front end user.
     *
     * @param string $username
     *
     * @return bool
     *
     * @deprecated Using ContaoUserProvider::isFrontendUsername has been deprecated and will no longer work in Contao 5.0.
     */
    private function isFrontendUsername(string $username): bool
    {
        @trigger_error('Using ContaoUserProvider::isFrontendUsername has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

        if (null === $this->container
            || null === ($request = $this->container->get('request_stack')->getCurrentRequest())
        ) {
            return false;
        }

        return 'frontend' === $username && $this->scopeMatcher->isFrontendRequest($request);
    }

    /**
     * Checks if the given username can be mapped to a back end user.
     *
     * @param string $username
     *
     * @return bool
     *
     * @deprecated Using ContaoUserProvider::isBackendUsername has been deprecated and will no longer work in Contao 5.0.
     */
    private function isBackendUsername(string $username): bool
    {
        @trigger_error('Using ContaoUserProvider::isBackendUsername has been deprecated and will no longer work in Contao 5.0.', E_USER_DEPRECATED);

        if (null === $this->container
            || null === ($request = $this->container->get('request_stack')->getCurrentRequest())
        ) {
            return false;
        }

        return 'backend' === $username && $this->scopeMatcher->isBackendRequest($request);
    }
}
