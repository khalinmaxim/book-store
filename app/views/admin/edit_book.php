<?php include __DIR__ . '/../layout/header.php'; ?>

<h1>Редактирование книги: <?= htmlspecialchars($book['title']) ?></h1>

<div class="admin-content">
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/edit/<?= $book['id'] ?>">
        <div class="form-group">
            <label for="title">Название:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="author_id">ID автора:</label>
            <input type="number" id="author_id" name="author_id" value="<?= $book['author_id'] ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">ID категории:</label>
            <input type="number" id="category_id" name="category_id" value="<?= $book['category_id'] ?>" required>
        </div>

        <div class="form-group">
            <label for="publication_year">Год издания:</label>
            <input type="number" id="publication_year" name="publication_year" value="<?= $book['publication_year'] ?>">
        </div>

        <div class="form-group">
            <label for="price">Цена:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= $book['price'] ?>" required>
        </div>

        <div class="form-group checkbox-group">
            <input type="checkbox" id="is_available" name="is_available" <?= $book['is_available'] ? 'checked' : '' ?>>
            <label for="is_available">Доступна для покупки</label>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/admin" class="btn">Отмена</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
