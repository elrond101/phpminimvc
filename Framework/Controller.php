<?php
namespace Framework;

/**
 * Base controller 
 */
abstract class Controller
{
    /**
     * Redirects to url
     *
     * @param integer $url url
     */
    protected function redirect($url)
    {
        header('Location: '.$url);
        die();
    }
}

