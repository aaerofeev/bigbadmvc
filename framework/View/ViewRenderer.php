<?php

namespace Framework\View;


class ViewRenderer
{
    private $viewPath;

    /**
     * ViewRenderer constructor.
     * @param $viewPath
     */
    public function __construct($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    public function view($viewName, $params = [])
    {
        return new View($this->viewPath . DIRECTORY_SEPARATOR . $viewName . '.php', $params, $this);
    }
}