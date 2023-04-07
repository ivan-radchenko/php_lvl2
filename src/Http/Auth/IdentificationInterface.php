<?php

namespace Ivan\Php\Http\Auth;

use Ivan\Php\Blog\User;
use Ivan\Php\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}
