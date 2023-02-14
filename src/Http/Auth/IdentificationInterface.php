<?php

namespace Journal\Http\Auth;

use Journal\Blog\User;
use Journal\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}