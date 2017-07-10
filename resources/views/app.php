<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $this->title ?></title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <link href="/css/app.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/"><img src="/images/logo.png" alt="Site logo"/></a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item <?php if ($this->uri === '/tasks'): ?>active<?php endif ?>">
                            <a class="nav-link" href="/tasks">Список</a>
                        </li>
                        <li class="nav-item <?php if ($this->uri === '/tasks/create'): ?>active<?php endif ?>">
                            <a class="nav-link" href="/tasks/create">Создать задачу</a>
                        </li>
                        <?php if (!Framework\Core\Auth::hasLogin()): ?>
                            <li class="nav-item <?php if ($this->uri === '/login'): ?>active<?php endif ?>">
                                <a class="nav-link" href="/login">Войти</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/logout">Выйти</a>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="content-page"><?= $this->content ?></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>