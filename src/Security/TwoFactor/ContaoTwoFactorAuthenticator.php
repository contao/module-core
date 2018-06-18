<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Security\TwoFactor;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Contao\User;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use Symfony\Component\HttpFoundation\Request;

class ContaoTwoFactorAuthenticator implements ContaoTwoFactorAuthenticatorInterface
{
    /**
     * @param User   $user
     * @param string $code
     *
     * @return bool
     */
    public function validateCode(User $user, string $code): bool
    {
        // The 2FA app from Google (Google authenticator) does not strictly confirm to RFC 4648 [1] (they confirm to the old RFC 3548 [2]).
        // [1] https://github.com/paragonie/constant_time_encoding/issues/9#issuecomment-331469087
        // [2] https://github.com/google/google-authenticator/wiki/Key-Uri-Format#secret
        $totp = TOTP::create(Base32::encodeUpperUnpadded($user->getSecret()));

        return $totp->verify($code, time());
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return string
     */
    public function getProvisionUri(User $user, Request $request): string
    {
        $issuer = rawurlencode($request->getSchemeAndHttpHost());
        $username = rawurlencode($user->getUsername());

        // The 2FA app from Google (Google authenticator) does not strictly confirm to RFC 4648 [1] (they confirm to the old RFC 3548 [2]).
        // [1] https://github.com/paragonie/constant_time_encoding/issues/9#issuecomment-331469087
        // [2] https://github.com/google/google-authenticator/wiki/Key-Uri-Format#secret
        $qrContent = sprintf(
                'otpauth://totp/%s:%s?secret=%s&issuer=%s',
                $issuer,
                $username.'@'.$issuer,
                Base32::encodeUpperUnpadded($user->getSecret()),
                $issuer
        );

        return $qrContent;
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return string
     */
    public function getQrCode(User $user, Request $request): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($this->getProvisionUri($user, $request));
    }
}
