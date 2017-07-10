<?php

namespace App\Models;

use Framework\Helpers\Image;
use Framework\Helpers\Validation;
use Framework\Model\Model;

class Task extends Model
{
    protected $tableName = 'tasks';

    public function validateInput($input, $edit = false)
    {
        $config = [
            'username' => ['имя пользователя', 'required', 'max_length' => 128],
            'email' => ['эл. почта', 'required', 'max_length' => 256],
            'description' => ['описание', 'required', 'max_length' => 1024],
            'picture' => ['изображение', 'upload_types' => ['jpg', 'jpeg', 'gif', 'png'] ],
        ];

        if ($edit === false) $config['picture'][] = 'upload_required';

        $validator = new Validation($config);

        if ($validator->isValid($input) === false) {
            return $validator->errors();
        }

        return false;
    }

    private function sanitizeName($filename)
    {
        return preg_replace('/[^a-z0-9\._-]+/i', '-', mb_strtolower($filename));
    }

    public function loadPicture($file)
    {
        $rawName = $this->sanitizeName(basename($file['name']));
        $name = time() . '_' . $rawName;
        $destination = APP_ROOT . '/public/images/tasks/' . $name;
        $public = '/images/tasks/' . $name;

        if (move_uploaded_file($file['tmp_name'], $destination))
        {
            $image = new Image($destination);
            $image->resize(320, 240);
            $image->save($destination);
            $image->cleanup();

            return $public;
        }

        return false;
    }

    public function createTask($values)
    {
        return $this->getStorage()->create($this->tableName, $values);
    }

    public function getList(array $condition = [], $from = 0, $length = false, $field = false, $sort = false)
    {
        return $this->getStorage()->fetchAll($this->tableName, $condition, $from, $length, $field, $sort);
    }

    public function getCount(array $condition = [])
    {
        return $this->getStorage()->count($this->tableName, $condition);
    }

    public function get($id)
    {
        return $this->getStorage()->fetch($this->tableName, ['id' => $id]);
    }

    public function getSortingFields() {
        return ['id' => 'Порядок добавления', 'username' => 'Имя пользователя', 'email' => 'Эл. почта', 'completed' => 'Статус'];
    }

    public function getSortingDirections()
    {
        return ['ASC' => 'По возрастанию', 'DESC' => 'По убыванию'];
    }

    public function editTask($condition, $values) {

        return $this->getStorage()->update($this->tableName, $condition, $values);
    }
}