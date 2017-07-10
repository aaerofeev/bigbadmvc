<?php

namespace Framework\View;

use Framework\Exceptions\CoreException;

class View
{
    private $filename;

    private $params = [];

    /**
     * @var ViewRenderer
     */
    private $viewRenderer;

    /**
     * View constructor.
     * @param $filename
     * @param array $params
     * @param $viewRenderer
     * @throws CoreException
     */
    public function __construct($filename, array $params, $viewRenderer)
    {
        $this->filename = $filename;
        $this->params = $params;
        $this->viewRenderer = $viewRenderer;

        if (file_exists($this->filename) === false) {
            throw new CoreException("View {$this->filename} not found");
        }
    }

    public function partial($viewName, $params = [])
    {
        return $this->viewRenderer->view($viewName, $params);
    }

    public function __toString()
    {
        try {
            ob_start();
            require $this->filename;
            return ob_get_clean();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->params))
        {
            return $this->params[$name];
        }

        throw new CoreException("View variable {$name} in {$this->filename} not found");
    }

    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }

    function __isset($name)
    {
        return array_key_exists($name, $this->params);
    }


}