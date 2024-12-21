<?php 

namespace Framework\Middleware;
use Framework\Session;

/** @package Framework\Middleware */
class Authorize
{
    /**
     *  Check of user is authenticated.
     * 
     * @return bool
     */
    public static function isAuthenticated() {
        return Session::has('user');
    }
    
    /**
     * Handle the incoming request
     * 
     * @param string $role
     * @return bool
     */
    public function handle($role)
    {
        if ($role == 'guest' && $this->isAuthenticated()) {
            return redirect('/');
        } elseif ($role == 'auth' && !$this->isAuthenticated()) {
            return redirect('/auth/login');
        }
    }
}