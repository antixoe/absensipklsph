<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait ChecksFeatures
{
    /**
     * Check if user has a specific feature.
     * @throws AuthorizationException
     */
    protected function requireFeature(string $featureSlug): void
    {
        if (!auth()->user() || !auth()->user()->hasFeature($featureSlug)) {
            throw new AuthorizationException('This feature is not available for your role.');
        }
    }

    /**
     * Check if user has any of the given features.
     * @throws AuthorizationException
     */
    protected function requireAnyFeature(array $featureSlugs): void
    {
        if (!auth()->user()) {
            throw new AuthorizationException('Unauthenticated.');
        }

        foreach ($featureSlugs as $slug) {
            if (auth()->user()->hasFeature($slug)) {
                return;
            }
        }

        throw new AuthorizationException('This feature is not available for your role.');
    }

    /**
     * Check if user has a specific role.
     * @throws AuthorizationException
     */
    protected function requireRole(string $roleSlug): void
    {
        if (!auth()->user() || !auth()->user()->hasRole($roleSlug)) {
            throw new AuthorizationException('This action is not authorized for your role.');
        }
    }

    /**
     * Check if user has any of the given roles.
     * @throws AuthorizationException
     */
    protected function requireAnyRole(array $roleSlugs): void
    {
        if (!auth()->user() || !auth()->user()->hasAnyRole($roleSlugs)) {
            throw new AuthorizationException('This action is not authorized for your role.');
        }
    }
}
