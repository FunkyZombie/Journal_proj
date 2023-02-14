<?php

namespace Journal\Http\Actions;

use Journal\Http\Request;
use Journal\Http\Response;
interface ActionInterface
{
public function handle(Request $request): Response;
}