<?php

namespace App\Services;

use App\Models\User;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class TwoFactorService
{
    public function __construct(
        private TwoFactorAuthenticationProvider $provider
    ) {}

    /**
     * Enable two-factor authentication for a user
     */
    public function enable(User $user): void
    {
        $secret = $this->provider->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(
                $this->generateRecoveryCodes()
            )),
            'two_factor_confirmed_at' => now(),
        ])->save();
    }

    /**
     * Disable two-factor authentication for a user
     */
    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Toggle two-factor authentication based on requirement
     */
    public function toggle(User $user, bool $enable): void
    {
        if ($enable && !$user->hasEnabledTwoFactorAuthentication()) {
            $this->enable($user);
        } elseif (!$enable && $user->hasEnabledTwoFactorAuthentication()) {
            $this->disable($user);
        }
    }

    /**
     * Generate recovery codes for two-factor authentication
     *
     * @return array<int, string>
     */
    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn() => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10))
            ->toArray();
    }
}
