<?php if ($this->success): ?>
    <div class="alert alert-success"><?= $this->success ?></div>
<?php endif ?>

<form method="GET" class="form-inline search-box">
    <div class="form-group">
        <select name="field" class="form-control">
            <?php foreach ($this->fields as $key => $name): ?>
                <option <?php if ($this->field === $key): ?>selected<?php endif ?> value="<?= $key ?>"><?= $name ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <select name="direction" class="form-control">
            <?php foreach ($this->directions as $key => $name): ?>
                <option <?php if ($this->direction === $key): ?>selected<?php endif ?> value="<?= $key ?>"><?= $name ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-info">Сортировать</button>
    </div>
</form>

<div class="help-block record-count">Всего записей: <?= $this->pagination['count'] ?></div>

<div class="media-list">
    <?php foreach ($this->list as $item): ?>
        <?= $this->partial('tasks/partials/item', ['item' => $item]) ?>
    <?php endforeach ?>
</div>

<?= $this->partial('tasks/partials/pagination', $this->pagination) ?>