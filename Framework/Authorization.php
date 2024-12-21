<?php 

namespace Framework;

use Framework\Session;

Class Authorization {
    /**
     * Check if  current logged in user owns a resource
     * 
     * @param int $userId$resourceId
     * @return Bool
     * 
     */
    public static function isOWner($resourcesId) {

        $sessionUser = Session::get('user');

        if ($sessionUser !== null && isset($sessionUser['id'])) {
            $sessionUserId = (int)$sessionUser['id'];
            return $sessionUserId === $resourcesId;
        }

        return false;

    }

}
