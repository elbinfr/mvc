<?php

namespace App\controllers;

use Core\BaseController;

class WelcomeController extends BaseController
{
    public function index()
    {
        $message = 'Welcome to mvc.';

        $this->view('welcome', [
            'message' => $message
        ]);
    }
}