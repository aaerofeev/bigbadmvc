<h1 class="page-header">Создание задачи</h1>

<?php if ($this->success): ?>
    <div class="alert alert-success"><?= $this->success ?></div>
<?php endif ?>

<?= $this->partial('tasks/partials/form', [
    'action' => '/tasks',
    'values' => $this->values,
    'allowComplete' => false,
    'pictureRequired' => true,
    'submitText' => 'Сохранить',
    'preview' => true,
    'errors' => $this->errors,
]) ?>
