<?php

namespace App\Utils;

class Route
{
    private string $method;
    private string $path;
    private $controller;
    private string $action;
    private array $middleware;
    private array $matches = [];

    public function __construct(string $method, string $path, $controller, string $action, array $middleware = [])
    {
        $this->method = $method;
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
        $this->middleware = $middleware;
    }

    public function matches(string $method, string $path): bool
    {
        if ($this->method !== $method) {
            return false;
        }

        $pattern = $this->getPatternFromPath();
        return preg_match($pattern, $path, $this->matches);
    }

    public function execute(): mixed
    {
        $params = array_slice($this->matches, 1);
        $next = function () use ($params) {
            return call_user_func_array([$this->controller, $this->action], $params);
        };

        foreach (array_reverse($this->middleware) as $middleware) {
            $next = function ($request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }

        return $next([]);
    }

    private function getPatternFromPath(): string
    {
        return '#^' . preg_replace('/{([^\/]+)}/', '([^/]+)', $this->path) . '$#';
    }
}

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path, $controller, string $action, array $middleware = []): void
    {
        $this->routes[] = new Route($method, $path, $controller, $action, $middleware);
    }

    public function handleRequest(string $method, string $path): mixed
    {
        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                return $route->execute();
            }
        }

        return $this->handleNotFound();
    }

    private function handleNotFound(): array
    {
        http_response_code(404);
        return ['error' => 'Not Found'];
    }
}