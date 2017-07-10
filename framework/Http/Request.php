<?php

namespace Framework\Http;

use Framework\Core\Application;
use Framework\Exceptions\CoreException;
use Framework\Exceptions\HttpException;

class Request
{
    private $paramsGet = [];

    private $paramsPost = [];

    private $paramsRoute = [];

    private $controllerName;

    private $actionsName;

    private $executed = false;

    /**
     * Request constructor.
     *
     * @param $controllerName
     * @param $actionName
     */
    public function __construct($controllerName, $actionName)
    {
        $this->controllerName = $controllerName;
        $this->actionsName = $actionName;

        $this->redirectBackSave();
    }

    /**
     * @return array
     */
    public function getParamsGet()
    {
        return $this->paramsGet;
    }

    /**
     * @param array $paramsGet
     */
    public function setParamsGet($paramsGet)
    {
        $this->paramsGet = $paramsGet;
    }

    /**
     * @return array
     */
    public function getParamsPost()
    {
        return $this->paramsPost;
    }

    /**
     * @param array $paramsPost
     */
    public function setParamsPost($paramsPost)
    {
        $this->paramsPost = array_merge($this->paramsPost, $paramsPost);
    }

    public function setParamsUploads($paramsUploads)
    {
        $this->setParamsPost($paramsUploads);
    }

    /**
     * @return array
     */
    public function getParamsRoute()
    {
        return $this->paramsRoute;
    }

    /**
     * @param array $paramsRoute
     */
    public function setParamsRoute($paramsRoute)
    {
        $this->paramsRoute = $paramsRoute;
    }

    /**
     * @return mixed
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @return mixed
     */
    public function getActionsName()
    {
        return $this->actionsName;
    }

    private function param($name, $from, $default = null)
    {
        return isset($from[$name]) ? $from[$name] : $default;
    }

    public function getParam($name, $default = null)
    {
        return $this->param($name, $this->paramsRoute, $default);
    }

    public function getQuery($name, $default = null)
    {
        return $this->param($name, $this->paramsGet, $default);
    }

    public function getPost($name, $default = null)
    {
        return $this->param($name, $this->paramsPost, $default);
    }

    public function redirect($location, $withData = false)
    {
        if (empty($withData) == false)
        {
            $_SESSION['_redirectBackData'] = $withData;
        }

        header('Location: ' . $location);
        exit;
    }

    public function redirectBackSave()
    {
        if (isset($_SESSION['_redirectBackData']))
        {
            $this->setParamsPost($_SESSION['_redirectBackData']);
            unset($_SESSION['_redirectBackData']);
            return true;
        }

        return false;
    }

    public function redirectBack($withData = false)
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $this->redirect($_SERVER['HTTP_REFERER'], $withData);
        } else {
            $this->redirect('/', $withData);
        }
    }

    /**
     * @param $controllerNamespace
     * @param $application Application
     * @return mixed
     * @throws CoreException
     * @throws HttpException
     */
    public function execute($controllerNamespace, $application)
    {
        if ($this->executed) {
            throw new CoreException('Request has already executed');
        }

        $this->executed = true;

        $controllerPath = $controllerNamespace . '\\' . $this->controllerName;

        if (class_exists($controllerPath) === false)
            throw new HttpException("Controller {$this->controllerName} doesn't not exists", 404);

        /**
         * @var Controller
         */
        $controller = new $controllerPath($this, $application);
        return $controller->execute($this->actionsName);
    }
}