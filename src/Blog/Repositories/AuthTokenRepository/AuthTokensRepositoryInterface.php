<?php

namespace Journal\Blog\Repositories\AuthTokenRepository;

use Journal\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function update(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}