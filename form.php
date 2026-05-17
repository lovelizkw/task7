<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Анкета</title>
  <style>
    body { font-family: sans-serif; background: #fff5f7; display: flex; justify-content: center; padding: 20px; }
    .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(216, 27, 96, 0.1); width: 440px; border: 1px solid #fce4ec; }
    h2 { color: #d81b60; text-align: center; }
    input, select, textarea { width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #f8bbd0; border-radius: 6px; box-sizing: border-box; }
    .btn-main { background: #d81b60; color: white; border: none; padding: 12px; width: 100%; cursor: pointer; border-radius: 6px; font-weight: bold; }
    .msg-ok { background: #fce4ec; color: #880e4f; padding: 10px; margin-bottom: 15px; border-radius: 6px; text-align: center; }
  </style>
</head>
<body>
<div class="card">
  <h2>Анкета</h2>

  <?php if (!empty($messages)) foreach($messages as $m) echo "<div class='msg-ok'>".htmlspecialchars($m)."</div>"; ?>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token'] ?? ''?>">
    <input type="text" name="fio" placeholder="ФИО" value="<?=htmlspecialchars($values['fio'] ?? '')?>" required>
    <input type="tel" name="phone" placeholder="Телефон" value="<?=htmlspecialchars($values['phone'] ?? '')?>">
    <input type="email" name="email" placeholder="E-mail" value="<?=htmlspecialchars($values['email'] ?? '')?>">
    <input type="date" name="birth_date" value="<?=htmlspecialchars($values['birth_date'] ?? '')?>" required>
    
    <select name="gender">
      <option value="male" <?= ($values['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Мужской</option>
      <option value="female" <?= ($values['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Женский</option>
    </select>

    <select name="languages[]" multiple size="4">
      <?php foreach ($allowed_languages as $l): ?>
        <option value="<?= $l ?>" <?= (isset($values['languages']) && in_array($l, $values['languages'])) ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>

    <textarea name="biography" placeholder="О себе..."><?=htmlspecialchars($values['biography'] ?? '')?></textarea>
    
    <label><input type="checkbox" name="contract" required checked> Я принимаю условия соглашения</label>

    <button type="submit" class="btn-main"><?= $is_logged ? 'Сохранить изменения' : 'Отправить' ?></button>
  </form>

  <div style="margin-top:20px; text-align:center;">
    <?php if ($is_logged): ?>
      Вы вошли как: <b><?=htmlspecialchars($_SESSION['login'])?></b><br>
      <a href="logout.php">Выйти</a>
    <?php else: ?>
      <a href="login.php">Вход</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>