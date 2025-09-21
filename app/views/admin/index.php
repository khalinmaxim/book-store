<?php include __DIR__ . '/../layout/header.php'; ?>

<h1>Панель администратора</h1>

<div class="admin-content">
    <h2>Управление книгами</h2>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Автор</th>
                <th>Год</th>
                <th>Цена</th>
                <th>Доступность</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?= $book['id'] ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author_name']) ?></td>
                <td><?= $book['publication_year'] ?></td>
                <td><?= $book['price'] ?> руб.</td>
                <td><?= $book['is_available'] ? 'Да' : 'Нет' ?></td>
                <td>
                    <a href="/admin/edit/<?= $book['id'] ?>" class="btn btn-primary">Редактировать</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
