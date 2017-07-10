<?php if ($this->totalPages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $this->totalPages; $i ++): ?>
            <li <?php if ($i == $this->page): ?>class="active"<?php endif ?>>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor ?>
    </ul>
</nav>
<?php endif ?>