<form method="POST" class="form form-task" action="<?= $this->action ?>" enctype="multipart/form-data">
    <?php if (isset($this->method)): ?>
        <input type="hidden" name="_method" value="<?= $this->method ?>"/>
    <?php endif ?>

    <div class="form-group<?php if (!empty($this->errors['username'])): ?> has-error<?php endif ?>">
        <label for="inputUsername">Ваше имя:</label>
        <input name="username" value="<?= $this->values['username'] ?>" required class="form-control" id="inputUsername" maxlength="128" placeholder="Введите имя">
        <?php if (!empty($this->errors['username'])): ?><div class="help-block"><?=$this->errors['username'] ?></div><?php endif ?>
    </div>

    <div class="form-group<?php if (!empty($this->errors['email'])): ?> has-error<?php endif ?>">
        <label for="inputEmail">Эл. почта:</label>
        <input name="email" type="email" value="<?= $this->values['email'] ?>" required class="form-control" id="inputUsername" maxlength="256" placeholder="Введите эл. адрес">
        <?php if (!empty($this->errors['email'])): ?><div class="help-block"><?=$this->errors['email'] ?></div><?php endif ?>
    </div>

    <div class="form-group<?php if (!empty($this->errors['description'])): ?> has-error<?php endif ?>">
        <label for="inputDescription">Текст задачи:</label>
        <textarea required name="description" class="form-control" rows="5" id="inputDescription" placeholder="Опишите задачу"><?= $this->values['description'] ?></textarea>
        <?php if (!empty($this->errors['description'])): ?><div class="help-block"><?=$this->errors['description'] ?></div><?php endif ?>
    </div>

    <div class="form-group<?php if (!empty($this->errors['picture'])): ?> has-error<?php endif ?>">
        <label for="inputPicture">Прикрепите изображение:</label>
        <input <?php if ($this->pictureRequired): ?>required<?php endif ?> type="file" name="picture" id="inputPicture"/>
        <?php if (!empty($this->errors['picture'])): ?><div class="help-block"><?=$this->errors['picture'] ?></div><?php endif ?>
    </div>

    <?php if ($this->allowComplete): ?>
    <div class="form-check">
        <label class="form-check-label">
            <input type="hidden" name="completed" value="0"/>
            <input type="checkbox" name="completed" class="form-check-input" <?php if ($this->values['completed']): ?>checked<?php endif ?>>
            Выполнена
        </label>
    </div>
    <?php endif ?>

    <?php if (empty($this->values['id'])): ?>
        <div class="help-block">Все поля обязательны для заполнения</div>
    <?php endif ?>

    <div class="action-box">
        <button type="submit" class="btn btn-primary"><?= $this->submitText ?></button>

        <?php if ($this->preview): ?>
            <button type="button" class="btn btn-info btn-preview">Предпросмотр</button>

            <div class="modal fade preview-modal" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="previewModal">Предпросмотр</h4>
                        </div>
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</form>