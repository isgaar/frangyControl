<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Models\UserActivity;
use Carbon\Carbon;

class LogSuccessfulLogin
{
    protected $request;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        // Update user model
        $user->last_login_at = Carbon::now();
        $user->last_login_ip = $this->request->ip();
        $user->save();

        // Log the activity
        UserActivity::create([
            'user_id' => $user->id,
            'action' => 'Inicio de Sesión',
            'description' => 'El usuario accedió al sistema exitosamente.',
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}
