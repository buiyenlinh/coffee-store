<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Role;
use App\Models\User;
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $check = 0;
        if (Auth::check()) {
            $active = Auth::user()->active;
            if ($active) {
                $role = Role::where('id', Auth::user()->role_id)
                    ->get()
                    ->toArray();

                $check = $role[0]['level'];
            }
        }

        if ($check == 0) {
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}
