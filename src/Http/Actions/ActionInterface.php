<?php

namespace Ivan\Php\Http\Actions;

use Ivan\Php\http\Request;
use Ivan\Php\http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}