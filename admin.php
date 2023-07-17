<?php session_start(); ?>

<html>
<head>
  <title>Администратор - Букатовский затон</title>
  <meta charset="utf8">
  <link type="image/x-icon" rel="shortcut icon" href="favicon.ico" />
  <link href="/res/style.css" rel="stylesheet" />
  <link rel="stylesheet" href="/res/style-admin.css" />
  <script src="/res/script-admin.js"></script>
</head>
<body>
<?php if($_SESSION["logged_in"]) { ?>

  <div class="wrap">
    <div class="content">
      <div class="header">
        <span class="header-title">Управление отзывами</span>
        <a id="button-logout" class="header-logout" href="#">Выход</a>
      </div>
      <span class="reviews-title">Опубликованные отзывы</span>
      <div id="reviews-public" class="reviews"></div>
      <span class="reviews-empty">Пусто</span>
      <span class="reviews-title">Ожидают модерации</span>
      <div id="reviews-private" class="reviews"></div>
      <span class="reviews-empty">Пусто</span>
    </div>
  </div>

  <div id="edit-background" class="edit-background">
    <form id="edit-form" class="edit" onsubmit="return false;">
      <table class="reviews-add-form-table">
        <tr>
            <td class="reviews-add-form-inputname">Автор:</td>
            <td><input class="reviews-add-form-smallinput" type="text" name="username"></td>
        </tr>
        <tr>
            <td class="reviews-add-form-inputname">E-Mail (необязательно):</td>
            <td><input class="reviews-add-form-smallinput" type="email" name="email"></td>
        </tr>
        <tr>
            <td class="reviews-add-form-inputname">Телефон (необязательно):</td>
            <td><input class="reviews-add-form-smallinput" type="phone" name="phone"></td>
        </tr>
        <tr>
            <td class="reviews-add-form-inputname">Оценка:</td>
            <td>
              <div class="reviews-add-form-stars">
                <input type="radio" id="reviews-add-form-star5" name="rating" value="5" checked /><label for="reviews-add-form-star5">★</label>
                <input type="radio" id="reviews-add-form-star4" name="rating" value="4" /><label for="reviews-add-form-star4">★</label>
                <input type="radio" id="reviews-add-form-star3" name="rating" value="3" /><label for="reviews-add-form-star3">★</label>
                <input type="radio" id="reviews-add-form-star2" name="rating" value="2" /><label for="reviews-add-form-star2">★</label>
                <input type="radio" id="reviews-add-form-star1" name="rating" value="1" /><label for="reviews-add-form-star1">★</label>
              </div>
            </td>
        </tr>
      </table>

      <textarea class="reviews-add-form-biginput" placeholder="Текст отзыва" rows="5" name="review"></textarea>

      <div class="reviews-add-form-bottombar">
        <button id="edit-cancel" class="reviews-add-form-button">Отмена</input>
        <button id="edit-save" class="reviews-add-form-button">Сохранить</input>
      </div>
    </form>
  </div>

  <template id="template-review">
    <div class="review">
      <div class="review-header">
        <span class="review-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></span>
        <span class="review-name">Имя</span>
      </div>
      <div class="review-contacts">
        <span class="review-phone">телефон не указан</span> / <span class="review-email">почта не указана</span>
      </div>
      <span class="review-text">Текст отзыва</span>
      <div class="review-actions">
        <a class="review-action review-change-publicity" href="#"></a>
        <a class="review-action review-edit" href="#">Изменить</a>
        <a class="review-action review-delete" href="#">Удалить</a>
      </div>
    </div>
  </template>

<?php } else { ?>

  <div id="login" class="login-wrap">
    <div class="login-window">
      <p class="login-window-title">Вход</p>
      <input id="field-login" class="login-window-input" type="text" placeholder="Логин">
      <input id="field-password" class="login-window-input" type="password" placeholder="Пароль">
      <div class="login-window-buttonbar">
        <button id="button-login" class="login-window-button">Войти</button>
        <span id="status" class="login-window-status"></span>
      </div>
    </div>
  </div>

<?php } ?>
</body>
</html>