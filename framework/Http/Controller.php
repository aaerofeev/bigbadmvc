<?php

namespace Framework\Http;

use Framework\Core\Application;
use Framework\Exceptions\HttpException;
use Framework\View\View;
use Framework\View\ViewRenderer;

class Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ViewRenderer
     */
    private $viewRenderer;

    protected $templateName = 'app';

    /**
     * @var View
     */
    protected $template;

    /**
     * Controller constructor.
     * @param Request $request
     * @param Application $application
     */
    public function __construct(Request $request, Application $application)
    {
        $this->request = $request;
        $this->application = $application;

        $this->viewRenderer = new ViewRenderer($application->getViewPath());
    }

    public function render($viewName, $params = [])
    {
        return $this->getViewRenderer()->view($viewName, $params);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return ViewRenderer
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    public function before()
    {
        $this->template = $this->getViewRenderer()->view($this->templateName, [
            'title' => $this->getApplication()->getName(),
            'uri' => $this->getApplication()->getRouter()->getUri()
        ]);
    }

    private function template($content)
    {
        $this->template->content = $content;
        return $this->template;
    }

    public function execute($actionName)
    {
        if (method_exists($this, $actionName)) {
            $this->before();
            $actionResult = call_user_func_array([$this, $actionName], $this->getRequest()->getParamsRoute());
            return $this->template($actionResult);
        }

        $controllerName = get_class($this);
        throw new HttpException("Action {$actionName} doesn't exists in controller {$controllerName}", 404);
    }
}