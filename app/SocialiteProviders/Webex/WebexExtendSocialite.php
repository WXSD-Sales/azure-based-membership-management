<?php

namespace App\SocialiteProviders\Webex;

use SocialiteProviders\Manager\SocialiteWasCalled;

class WebexExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('webex', Provider::class);
    }
}
