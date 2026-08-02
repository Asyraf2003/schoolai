<?php

namespace Tests;

use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function actingAs(Authenticatable $user, $guard = null): static
    {
        parent::actingAs($user, $guard);

        if ($user instanceof User) {
            $this->withSession([
                ActiveSessionManager::SESSION_KEY => $user->session_version,
            ]);
        }

        return $this;
    }
}
