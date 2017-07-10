<?php

namespace Framework\Http;


class Router
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    protected $routes;

    private function route($method, $pattern, $callback)
    {
        $this->routes[$callback] = [
            'method' => $method,
            'pattern' => $this->patternPrepare($pattern),
            'callback' => $callback
        ];
    }

    private function patternPrepare($pattern)
    {
        return '|^/' . $pattern . '$|';
    }

    public function resource($prefix, $controllerName)
    {
        $idPrefix = $prefix . '/(?<id>[^/]+?)';

        $this->get($prefix, $controllerName . '@index');
        $this->get($idPrefix . '/show', $controllerName . '@show');
        $this->get($prefix . '/create', $controllerName . '@create');
        $this->get($idPrefix . '/edit', $controllerName . '@edit');
        $this->post($prefix, $controllerName . '@store');
        $this->put($idPrefix, $controllerName . '@update');
        $this->delete($idPrefix, $controllerName . '@destroy');
    }

    public function get($pattern, $callback)
    {
        $this->route(self::METHOD_GET, $pattern, $callback);
    }

    public function post($pattern, $callback)
    {
        $this->route(self::METHOD_POST, $pattern, $callback);
    }

    public function put($pattern, $callback)
    {
        $this->route(self::METHOD_PUT, $pattern, $callback);
    }

    public function delete($pattern, $callback)
    {
        $this->route(self::METHOD_DELETE, $pattern, $callback);
    }

    private function routeMatch($uri, $method, $route, &$paramsRoute)
    {
        if ($method === $route['method']) {
            $result = preg_match($route['pattern'], $uri, $params) > 0;

            if ($result)
            {
                foreach ($params as $k => $v)
                {
                    if (is_numeric($k) === false) {
                        $paramsRoute[$k] = $v;
                    }
                }

                return true;
            }
        }

        return false;
    }

    private function extractCallbackInfo($callback)
    {
        list($controller, $action) = explode('@', $callback);
        return [$controller, $action];
    }

    /**
     * @return Request|bool
     */
    public function execute()
    {
        $paramsRoute = [];

        $request = false;

        foreach ($this->routes as $route)
        {
            if ($this->routeMatch($this->getUri(), $this->getMethod(), $route, $paramsRoute))
            {
                list($controllerName, $actionName) = $this->extractCallbackInfo($route['callback']);

                $request = new Request($controllerName, $actionName);

                $request->setParamsGet($_GET);
                $request->setParamsPost($_POST);
                $request->setParamsUploads($_FILES);
                $request->setParamsRoute($paramsRoute);
            }
        }

        return $request;
    }

    public function getMethod()
    {
        return isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];
    }

    public function getUri()
    {
        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    }
}