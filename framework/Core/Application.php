<?php

namespace Framework\Core;

use Framework\Exceptions\HttpException;
use Framework\Http\Router;
use Framework\Model\Storage;

class Application
{
    /**
     * @var Router
     */
    private $router;

    private $controllerNamespace;

    private $viewPath;

    private $name;

    public function __construct($config)
    {
        session_start();

        $this->controllerNamespace = $config['controllerNamespace'];
        $this->viewPath = $config['viewPath'];
        $this->name = $config['appName'];

        Storage::initialize($config['storageConnect']);

        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function run()
    {
        try {
            $request = $this->router->execute();

            if ($request === false) {
                throw new HttpException('Route not found', 404);
            }

            return $request->execute($this->controllerNamespace, $this);

        } catch (HttpException $e) {
            $this->throwException($e);
        }
    }

    public function shutdown()
    {
        /* nothing */
    }

    /**
     * @return mixed
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * @param \Exception $e
     */
    protected function throwException(\Exception $e)
    {
        if ($e instanceof HttpException) {
            echo $e->getMessage();
            header('HTTP/1.0 404 Not Found');
        } else {
            echo 'Server send error, please contact administrator';
            header('HTTP/1.0 500 Internal Server Error');
        }

        exit;
    }
}