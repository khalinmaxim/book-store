<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="form-container">
    <h2>Регистрация</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>

    <p>Уже есть аккаунт? <a href="/login">Войдите</a></p>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
