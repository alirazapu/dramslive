<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kohana_Exception extends Kohana_Kohana_Exception
{
    public static function handler(Throwable $e)
    {
        // Throw errors when in development mode
        /*if (Kohana::$environment === Kohana::DEVELOPMENT)
        {*/
            parent::handler($e);
        /*}
        else
        { */
            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
             
            $attributes = array(
                'action'    => 500,
                'origuri'   => rawurlencode(Arr::get($_SERVER, 'REQUEST_URI')),
                'message'   => rawurlencode($e->getMessage())
            );
             
            if ($e instanceof Http_Exception)
            {
                $attributes['action'] = $e->getCode();
            }
             
            // Error sub request
            echo Request::factory(Route::get('error')->uri($attributes))
                ->execute()
                ->send_headers()
                ->body();
       // }
    }
}