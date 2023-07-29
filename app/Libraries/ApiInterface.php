<?php

namespace App\Libraries;

use Illuminate\Http\Client\Response;

interface ApiInterface
{
    public function setBaseUrl(string $baseUrl): self;

    public static function connection(string $api): ApiInterface;

    public function withHeaders(array $headers): self;

    public function withQuery(array $query): self;

    public function get(string $route): array;

    public function post(string $route, array $data = []): array;

    public function put(string $route, array $data = []): array;

    public function patch(string $route, array $data = []): array;

    public function delete(string $route): array;
}