<?php

namespace App\Controllers;

class ErrorController
{
    /*
     * 404 Not Found Error
     * 
     * @return void
    */

    public static function notFound($message = 'Resource is not found')
    {
       http_response_code(404);

        loadView('error', [
            'status' => '404',
            'message' => $message,
        ]);
    }

    /*
     * 403 Unauthorized Error
     * 
     * @return void
    */

    public static function unauthorized($message = 'You are not authorized to view this resources')
    {
       http_response_code(403);

        loadView('error', [
            'status' => '403',
            'message' => $message,
        ]);
    }
}
