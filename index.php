<?php
// Категории для варианта 22
$categories = ['Подарки', 'Сувениры', 'Handmade'];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $category = $_POST['category'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    // Валидация
    if (!empty($email) && !empty($category) && !empty($title) && !empty($content)) {
        // Очищаем название от небезопасных символов для имени файла
        $safe_title = preg_replace('/[^a-zA-Zа-яА-Я0-9_\-]/u', '_', $title);
        $filename = "categories/$category/$safe_title.txt";
        
        // Сохраняем объявление в файл
        file_put_contents($filename, "Email: $email\n\n$content");
    }
}

// Получаем список всех объявлений
$ads = [];
foreach ($categories as $category) {
    $files = glob("categories/$category/*.txt");
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        $email = str_replace('Email: ', '', $lines[0]);
        $ad_content = implode("\n", array_slice($lines, 2));
        $title = basename($file, '.txt');
        
        $ads[] = [
            'category' => $category,
            'title' => $title,
            'email' => $email,
            'content' => $ad_content
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Доска объявлений - Подарки, Сувениры, Handmade</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Доска объявлений</h1>
    
    <h2>Добавить объявление</h2>
    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="category">Категория:</label>
            <select id="category" name="category" required>
                <option value="">-- Выберите категорию --</option>
                <option value="Подарки">Подарки</option>
                <option value="Сувениры">Сувениры</option>
                <option value="Handmade">Handmade</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="title">Заголовок объявления:</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="content">Текст объявления:</label>
            <textarea id="content" name="content" rows="5" required></textarea>
        </div>
        
        <button type="submit">Добавить</button>
    </form>
    
    <h2>Список объявлений</h2>
    <?php if (empty($ads)): ?>
        <p>Объявлений пока нет.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Категория</th>
                    <th>Заголовок</th>
                    <th>Email</th>
                    <th>Текст объявления</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ads as $ad): ?>
                    <tr>
                        <td><?= htmlspecialchars($ad['category']) ?></td>
                        <td><?= htmlspecialchars($ad['title']) ?></td>
                        <td><?= htmlspecialchars($ad['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($ad['content'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>