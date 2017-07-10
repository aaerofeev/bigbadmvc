<?php

namespace App\Controllers;

use App\Models\Task;
use Framework\Core\Auth;
use Framework\Exceptions\CoreException;
use Framework\Exceptions\HttpException;
use Framework\Http\Controller;
use Framework\Http\Router;

class TaskController extends Controller
{
    public function getInput($edit = false)
    {
        return [
            'username' => htmlspecialchars($this->getRequest()->getPost('username')),
            'email' => htmlspecialchars($this->getRequest()->getPost('email')),
            'description' => htmlspecialchars($this->getRequest()->getPost('description')),
            'picture' => $this->getRequest()->getPost('picture'),
            'completed' => $edit ? (bool)$this->getRequest()->getPost('completed') : false,
        ];
    }

    public function create()
    {
        $values = $this->getInput();
        $errors = $this->getRequest()->getPost('errors');
        $success = $this->getRequest()->getPost('success');

        $this->template->title = 'Создать задачу';

        return $this->render('tasks/create', [
            'values' => $values,
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function store()
    {
        $task = new Task();
        $values = $this->getInput();

        $errors = $task->validateInput($values, false);

        if ($errors === false)
        {
            $values['picture'] = $task->loadPicture($values['picture']);

            if ($task->createTask($values) !== false) {
                $this->getRequest()->redirectBack(['success' => 'Задача успешно добавлена']);
            } else {
                throw new CoreException('Database insert error');
            }
        }

        $this->getRequest()->redirectBack(array_merge($values, ['errors' => $errors]));
    }

    public function preview()
    {
        $item = $this->getInput();

        if (empty($item['username'])) $item['username'] = 'Укажите имя пользователя';
        if (empty($item['email'])) $item['email'] = 'укажите эл. почту';
        if (empty($item['description'])) $item['description'] = 'Здесь будет описание';

        $item['picture'] = 'http://via.placeholder.com/320x240';

        $this->template = $this->render('tasks/partials/item', [
            'item' => $item
        ]);
    }

    public function calculatePagination($page, $count, $limit)
    {
        $page = max(1, (int)$page);

        $totalPages = (int)ceil($count / $limit);
        $offset = max(($page-1) * $limit, 0);

        return [
            'totalPages' => $totalPages,
            'offset' => $offset,
            'perPage' => $limit,
            'page' => min($totalPages, $page),
            'count' => $count,
        ];
    }

    public function index()
    {
        $this->template->title = 'Список задач';

        $field = $this->getRequest()->getQuery('field');
        $direction = $this->getRequest()->getQuery('direction');
        $page = $this->getRequest()->getQuery('page');

        $success = $this->getRequest()->getPost('success');

        $task = new Task();

        $fields = $task->getSortingFields();
        $directions = $task->getSortingDirections();

        if (array_key_exists($field, $fields) === false) $field = 'id';
        if (array_key_exists($direction, $directions) === false) $direction = 'DESC';

        $pagination = $this->calculatePagination($page, $task->getCount(), 3);

        return $this->render('tasks/index', [
            'list' => $task->getList([], $pagination['offset'], $pagination['perPage'], $field, $direction),
            'fields' => $fields,
            'directions' => $directions,
            'field' => $field,
            'direction' => $direction,
            'pagination' => $pagination,
            'success' => $success,
        ]);
    }

    protected function checkAccess()
    {
        if (Auth::hasLogin() === false) {
            throw new HttpException('Forbidden', 403);
        }
    }

    public function edit($id)
    {
        $this->checkAccess();

        $taskRow = (new Task())->get($id);
        if (empty($taskRow)) throw new HttpException('Record not found');

        if ($this->getApplication()->getRouter()->getMethod() !== Router::METHOD_GET) {
            $values = $this->getInput();
        } else $values = $taskRow;

        $errors = $this->getRequest()->getPost('errors');
        $success = $this->getRequest()->getPost('success');

        $this->template->title = 'Редактировать задачу';

        return $this->render('tasks/edit', [
            'values' => $values,
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function update($id)
    {
        $this->checkAccess();

        $task = new Task();
        $taskRow = $task->get($id);
        if (empty($taskRow)) throw new HttpException('Record not found');

        $values = $this->getInput(true);
        $errors = $task->validateInput($values, true);

        if ($errors === false)
        {
            $picture = $task->loadPicture($values['picture']);
            if ($picture !== false) {
                $values['picture'] = $picture;
            } else unset($values['picture']);

            if ($task->editTask(['id' => $id], $values) !== false) {
                $this->getRequest()->redirectBack(['success' => 'Задача успешно изменена']);
            } else {
                throw new CoreException('Database insert error');
            }
        }

        $this->getRequest()->redirectBack(array_merge($values, ['errors' => $errors]));
    }
}