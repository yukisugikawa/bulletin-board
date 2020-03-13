<?php
require 'common.php';
require 'db.php';
require_logined_session();

if(filter_input(INPUT_SERVER,'REQUEST_METHOD') === 'POST'){
  $name = filter_input(INPUT_POST, 'name');
  $mail = filter_input(INPUT_POST, 'mail');
  $pass = filter_input(INPUT_POST, 'pass');
  $pass_conf = filter_input(INPUT_POST, 'pass_conf');
  $token = filter_input(INPUT_POST, 'token');

  $vdt = new Validation;
  $vdt->check_max($name,'名前',10);
  $vdt->check_type($mail,'メールアドレス');
  $vdt->check_type($pass,'パスワード');
  $vdt->check_match($pass,$pass_conf,'確認用パスワード');
  $vdt->check_type($token,'トークン');
  $error = $vdt->get_error();

  if(empty($error)){

    $db = new DB;
    $sql = 'INSERT INTO users_registrations (users_registrations_id,name,mail,pass) VALUES (null,?,?,?)';
    $params[] = $name;
    $params[] = $mail;
    $params[] = password_hash($pass,PASSWORD_DEFAULT);
    $success = $db->insert($sql,$params);
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="utf-8">
    <title>新規登録</title>
  </head>
  <body>
    <?php if(isset($success) && $success) : ?>
      <h1>掲示板にようこそ！</h1>
      <p>登録に成功しました。</p>
      <p><a href="login.php">こちらからログインしてください。</a></p>
    <?php else: ?>
      <h1>掲示板へようこそ！</h1>
      <p>掲示板のアカウントを持っている場合は<a href="login.php">ログイン</a>から。</p>
      <?php if(isset($error)): ?>
        <?php foreach($error as $e): ?>
          <p class="error"><?php echo h($e); ?></p>
        <?php endforeach; ?>
      <?php endif; ?>
      <form action="" method="post" autocomplete="off">
        <div class="cp_iptxt">
        	<label class="ef">
        	<input type="text" name="name" placeholder="名前">
        	</label>
        </div>
        <div class="cp_iptxt">
        	<label class="ef">
        	<input type="email" name="mail" placeholder="メールアドレス">
        	</label>
        </div>
        <div class="cp_iptxt">
        	<label class="ef">
        	<input type="password" name="pass" placeholder="パスワード">
        	</label>
        </div>
        <div class="cp_iptxt">
        	<label class="ef">
        	<input type="password" name="pass_conf" placeholder="確認用パスワード">
        	</label>
        </div>
        <input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
        <button type="submit">新規登録</button>
      </form>
    <?php endif; ?>
  </body>
</html>
