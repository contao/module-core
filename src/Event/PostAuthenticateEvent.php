<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Event;

use Contao\User;
use Symfony\Component\EventDispatcher\Event;

class PostAuthenticateEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the user object.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
