<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="book-detail">
    <h1><?= htmlspecialchars($book['title']) ?></h1>

    <div class="book-info">
        <p><strong>Автор:</strong> <?= htmlspecialchars($book['author_name']) ?></p>
        <p><strong>Категория:</strong> <?= htmlspecialchars($book['category_name']) ?></p>
        <p><strong>Год издания:</strong> <?= htmlspecialchars($book['publication_year']) ?></p>
        <p><strong>Цена:</strong> <?= htmlspecialchars($book['price']) ?> руб.</p>
        <p><strong>Описание:</strong> <?= htmlspecialchars($book['description']) ?></p>
        <p><strong>Доступность:</strong> <?= $book['is_available'] ? 'В наличии' : 'Нет в наличии' ?></p>
    </div>

    <?php if ($book['is_available'] && isset($_SESSION['user_id'])): ?>
    <div class="purchase-options">
        <h3>Варианты приобретения:</h3>
        <form method="POST" action="/purchase">
            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

            <div class="option">
                <input type="radio" id="purchase" name="type" value="purchase" checked>
                <label for="purchase">Купить за <?= htmlspecialchars($book['price']) ?> руб.</label>
            </div>

            <?php if ($book['rental_price_week']): ?>
            <div class="option">
                <input type="radio" id="rent_2weeks" name="type" value="rent_2weeks">
                <label for="rent_2weeks">Аренда на 2 недели - <?= htmlspecialchars($book['rental_price_week']) ?> руб.</label>
            </div>
            <?php endif; ?>

            <?php if ($book['rental_price_month']): ?>
            <div class="option">
                <input type="radio" id="rent_month" name="type" value="rent_month">
                <label for="rent_month">Аренда на месяц - <?= htmlspecialchars($book['rental_price_month']) ?> руб.</label>
            </div>
            <?php endif; ?>

            <?php if ($book['rental_price_3months']): ?>
            <div class="option">
                <input type="radio" id="rent_3months" name="type" value="rent_3months">
                <label for="rent_3months">Аренда на 3 месяца - <?= htmlspecialchars($book['rental_price_3months']) ?> руб.</label>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Оформить</button>
        </form>
    </div>
    <?php elseif (!isset($_SESSION['user_id'])): ?>
    <p>Для покупки книги необходимо <a href="/login">войти в систему</a></p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
