<?php

namespace App\Http\Controllers\Auth;

use App\Application\Auth\ResolveUserHomeRoute;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request, ResolveUserHomeRoute $resolveUserHomeRoute): RedirectResponse
    {
        $target = $resolveUserHomeRoute->handle($request->user());

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($target->toUrl(query: ['verified' => 1]));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($target->toUrl(query: ['verified' => 1]));
    }
}
