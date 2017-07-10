<div class="media">
    <div class="media-body">
        <h4 class="media-heading <?php if ($this->item['completed']): ?>text-success<?php endif ?>">
            <?= $this->item['username'] ?> &laquo;<?= $this->item['email'] ?>&raquo;
        </h4>

        <p><?= $this->item['description'] ?></p>

        <img class="img-responsive" src="<?= $this->item['picture'] ?>" alt="Task Picture">

        <ul class="edit-box list-inline">
            <?php if ($this->item['completed']): ?>
                <li class="text-success">Задача выполнена</li>
            <?php else: ?>
                <li class="text-info">В процессе</li>
            <?php endif ?>

            <?php if (Framework\Core\Auth::hasLogin() && isset($this->item['id'])): ?>
                <li><a href="<?= '/tasks/' . $this->item['id'] . '/edit' ?>">редактировать</a></li>
            <?php endif ?>
        </ul>
    </div>
</div>