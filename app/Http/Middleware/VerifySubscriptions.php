<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class VerifySubscriptions
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest() || (!$this->auth->user()->isStaff && (is_null($this->auth->user()->subscription) || !$this->auth->user()->subscription->isValid()))) {
            /* TODO replace route with actual value */
            return redirect()->route('subscription.back');
        }
        return $next($request);
    }
}
