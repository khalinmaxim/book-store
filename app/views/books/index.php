<?php include __DIR__ . '/../layout/header.php'; ?>

<h1>Наша библиотека</h1>

<div class="filters">
    <form method="GET" action="/books">
        <select name="category">
            <option value="">Все категории</option>
            <!-- Здесь будут категории -->
        </select>
        <select name="author">
            <option value="">Все авторы</option>
            <!-- Здесь будут авторы -->
        </select>
        <select name="year">
            <option value="">Все годы</option>
            <!-- Здесь будут годы -->
        </select>
        <button type="submit">Фильтровать</button>
    </form>
</div>

<div class="books-grid">
    <?php foreach ($books as $book): ?>
        <div class="book-card">
            <h3><?= htmlspecialchars($book['title']) ?></h3>
            <p>Автор: <?= htmlspecialchars($book['author_name']) ?></p>
            <p>Год: <?= htmlspecialchars($book['publication_year']) ?></p>
            <p>Цена: <?= htmlspecialchars($book['price']) ?> руб.</p>
            <a href="/books/<?= $book['id'] ?>">Подробнее</a>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
