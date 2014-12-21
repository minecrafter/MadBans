<?php

namespace MadBans\Data;

class AjaxResponse
{
    private $message;
    private $redirect;

    function __construct($message, $redirect)
    {
        $this->message = $message;
        $this->redirect = $redirect;
    }

    public function toArray()
    {
        return array('message' => $this->message, 'redirect' => $this->redirect);
    }
}