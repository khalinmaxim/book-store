<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="form-container">
    <h2>Вход в систему</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Войти</button>
    </form>

    <p>Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
