<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DemoCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if(config('app.APP_DEMO')) {

            $message = "On Demo Mode Some Features Are Restricted";

            if($request->ajax()){
                $success[0] = $message;
                $success[1] = 'error';
                $success[2] = ___('alert.Demo_Mode');
                $success[3] = ___('alert.Close');
                return response()->json($success);
            }

            return redirect()->back()->with('danger', $message);
        }

        return $next($request);



    }
}
