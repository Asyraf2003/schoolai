<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    use \App\Http\Controllers\Auth\Concerns\AuthenticatesGoogleUsers;
    use \App\Http\Controllers\Auth\Concerns\ResolvesGoogleUsers;

    private const ADMIN_CLAIM_KEY = 'first_google_admin';

    public function __construct(
        private readonly AuditLogger $auditLogger
    ) {
    }







}

