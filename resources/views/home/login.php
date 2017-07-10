<h1 class="page-header">Вход в панель</h1>

<?php if ($this->success): ?>
    <div class="alert alert-success"><?= $this->success ?></div>
<?php endif ?>

<?php if ($this->error): ?>
    <div class="alert alert-danger"><?= $this->error ?></div>
<?php endif ?>

<form method="POST" class="form form-login" enctype="multipart/form-data">

    <div class="form-group">
        <label for="inputUsername">Имя пользователя:</label>
        <input name="username" value="<?= $this->values['username'] ?>" required class="form-control" id="inputUsername" maxlength="128" placeholder="Введите имя">
    </div>

    <div class="form-group">
        <label for="inputPassword">Пароль:</label>
        <input name="password" type="password" value="<?= $this->values['password'] ?>" required class="form-control" id="inputPassword" maxlength="256" placeholder="Введите эл. адрес">
    </div>

    <button type="submit" class="btn btn-primary">Войти</button>
</form>
