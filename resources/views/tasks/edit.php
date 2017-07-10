<h1 class="page-header">Редактирование задачи</h1>

<?php if ($this->success): ?>
    <div class="alert alert-success"><?= $this->success ?></div>
<?php endif ?>

<?= $this->partial('tasks/partials/form', [
    'action' => '/tasks/' . $this->values['id'],
    'method' => \Framework\Http\Router::METHOD_PUT,
    'values' => $this->values,
    'allowComplete' => true,
    'submitText' => 'Изменить',
    'pictureRequired' => false,
    'preview' => false,
    'errors' => $this->errors,
]) ?>
