<?php

namespace Framework\Helpers;

use Framework\Exceptions\CoreException;

class Validation
{
    private $errors;
    private $config;

    private $messages = [
        'email' => 'Поле :name не является email адресом',
        'required' => 'Поле :name обязательно для заполнения',
        'max_length' => 'Поле :name не может быть длинее :count символов',
        'upload_types' => 'В поле :name возможна загрузка только :types форматов',
        'upload_required' => TRUE,// other rule
    ];

    /**
     * Validation constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function isValid($input)
    {
        $this->errors = [];

        foreach ($this->config as $key => $rules)
        {
            if (!array_key_exists($key, $this->errors) && count($rules) > 1) {

                $value = isset($input[$key]) ? $input[$key] : null;

                $name = null;

                foreach ($rules as $rule => $arguments)
                {
                    if ($rule === 0) {
                        $name = $arguments;
                        continue;
                    }

                    if (is_numeric($rule)) {
                        $rule = $arguments;
                        $arguments = null;
                    }

                    $error = $this->check($value, $rule, $arguments, $name);
                    if ($error !== false) {
                        $this->errors[$key] = $error;
                        break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    protected function check($value, $rule, $arguments, $name)
    {
        if (isset($this->messages[$rule])) {

            switch ($rule) {
                case 'required':
                    if (empty($value)) {
                        return $this->formatError($this->messages[$rule], [':name' => $name]);
                    }

                    break;

                case 'email':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                        return $this->formatError($this->messages[$rule], [':name' => $name]);
                    }

                    break;

                case 'max_length':
                    if ($arguments > 0 && mb_strlen($value) > $arguments) {
                        return $this->formatError($this->messages[$rule], [':name' => $name, ':count' => (int)$arguments]);
                    }

                    break;

                case 'upload_required':
                    if (is_array($value) === false || $value['error'] !== UPLOAD_ERR_OK) {
                        return $this->formatError($this->messages['required'], [':name' => $name]);
                    }

                    break;

                case 'upload_types':
                    if (is_array($value) && $value['error'] === UPLOAD_ERR_OK) {
                        $ext = mb_strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

                        if (in_array($ext, $arguments) === false) {
                            return $this->formatError($this->messages[$rule], [':name' => $name, ':types' => implode(', ', $arguments)]);
                        }
                    }

                    break;
            }

            return false;
        }

        throw new CoreException("Process for rule {$rule} not set");
    }

    protected function formatError($error, $params = [])
    {
        return str_replace(array_keys($params), array_values($params), $error);
    }

    public function errors()
    {
        return $this->errors;
    }
}