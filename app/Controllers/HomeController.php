<?php

namespace App\Controllers;

use Framework\Core\Auth;
use Framework\Http\Controller;

class HomeController extends Controller
{
    public function getInput()
    {
        return [
            'username' => htmlspecialchars($this->getRequest()->getPost('username')),
            'password' => htmlspecialchars($this->getRequest()->getPost('password')),
        ];
    }

    public function index()
    {
        $this->getRequest()->redirect('/tasks');
    }

    public function login()
    {
        $values = $this->getInput();
        $error = $this->getRequest()->getPost('error');
        $success = $this->getRequest()->getPost('success');

        return $this->render('home/login', ['values' => $values, 'success' => $success, 'error' => $error]);
    }

    public function logout()
    {
        if (Auth::getInstance()->logout()) {
            $this->getRequest()->redirect('/tasks', ['success' => 'Произошел выход из панели']);
        }

        $this->getRequest()->redirect('/');
    }

    public function storeLogin()
    {
        $values = $this->getInput();

        if (Auth::getInstance()->login($values['username'], $values['password'])) {
            $this->getRequest()->redirect('/tasks', ['success' => 'Вход произведен успешно. Добро пожаловать!']);
        }

        $this->getRequest()->redirectBack(array_merge($values, ['error' => 'Ошибка входа, данные не верные']));
    }
}