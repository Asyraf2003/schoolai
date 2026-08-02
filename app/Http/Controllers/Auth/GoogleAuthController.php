<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\AuthenticatesGoogleUsers;
use App\Http\Controllers\Auth\Concerns\ResolvesGoogleUsers;
use App\Http\Controllers\Controller;
use App\Services\ActiveSessionManager;
use App\Services\AuditLogger;

class GoogleAuthController extends Controller
{
    use AuthenticatesGoogleUsers;
    use ResolvesGoogleUsers;

    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly ActiveSessionManager $sessionManager,
    ) {}

}
