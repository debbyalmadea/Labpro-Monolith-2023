<?php

namespace App\Libraries;

use App\Enums\HttpStatusCodes;
use App\Exceptions\HttpCustomException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Api implements ApiInterface
{
    private string $baseUrl;

    private PendingRequest $service;

    public function __construct()
    {
        $this->baseUrl = Config::get('services.single_service.url');
        $this->service = Http::baseUrl($this->baseUrl);
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        $this->service = Http::baseUrl($this->baseUrl);

        return $this;
    }

    public static function connection(string $api): ApiInterface
    {
        return (new Api())->setBaseUrl(Config::get('services.' . $api . '.url'));
    }

    private function handleResponse(Response $response)
    {
        if ($response->successful()) {
            $status = $response->json()['status'];
            $data = $response->json()['data'];
            $message = $response->json()['message'];

            if ($status === 'success') {
                return $data;
            }

            throw new HttpCustomException($response->status(), $message);
        } else {
            if ($response->json()['status'] === 'error') {
                throw new HttpCustomException($response->status(), $response->json()['message']);
            }
        }
    }

    public function withHeaders(array $headers): self
    {
        $this->service = $this->service->withHeaders($headers);
        return $this;
    }

    public function withQuery(array $query): self
    {
        $this->service = $this->service->withQuery($query);
        return $this;
    }

    public function get(string $route): array
    {
        $response = $this->service->get('/' . $route);
        return $this->handleResponse($response);
    }

    public function post(string $route, array $data = []): array
    {
        $response = $this->service->post('/' . $route, $data);
        return $this->handleResponse($response);
    }

    public function put(string $route, array $data = []): array
    {
        $response = $this->service->put('/' . $route, $data);
        return $this->handleResponse($response);
    }

    public function patch(string $route, array $data = []): array
    {
        $response = $this->service->patch('/' . $route, $data);
        return $this->handleResponse($response);
    }

    public function delete(string $route): array
    {
        $response = $this->service->delete('/' . $route);
        return $this->handleResponse($response);
    }
}