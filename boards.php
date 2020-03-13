<?php
require 'common.php';
require 'db.php';
require_unlogined_session();

$name = $_SESSION['name'];

if(filter_input(INPUT_SERVER,'REQUEST_METHOD') === 'POST'){
  $title = filter_input(INPUT_POST, 'title');
  $token = filter_input(INPUT_POST, 'token');
  date_default_timezone_set('Asia/Tokyo');
  $created_at = date("Y-m-d H:i:s");

  $vdt = new Validation;
  $vdt->check_max($title,'タイトル',10);
  $vdt->check_type($token,'トークン');
  $error = $vdt->get_error();

  if(empty($error)){
    $db = new DB;
    $sql = 'INSERT INTO boards (boards_id,title,created_at,name) VALUES (null,?,?,?)';
    $params[] = $title;
    $params[] = $created_at;
    $params[] = $name;
    $db->insert($sql,$params);

    header('Location:boards.php');
    exit;
  }
}

$db = new DB;
$sql = 'SELECT * FROM boards ORDER BY boards_id DESC';
$rows = $db->select($sql);
$count = count($rows);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="utf-8">
    <title>掲示板</title>
  </head>
  <body>
    <h1>掲示板へようこそ</h1>
    <h2>新規投稿</h2>
    <?php if(isset($error)): ?>
      <?php foreach($error as $e): ?>
        <p class="error"><?php echo h($e); ?></p>
      <?php endforeach; ?>
    <?php endif; ?>
    <form action="" method="post" autocomplete="off">
      <div class="cp_iptxt">
        <label class="ef">
        <input type="text" name="title" placeholder="タイトルを入力してください">
        </label>
      </div>
      <input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
      <button type="submit">部屋を立てる</button>
    </form>
    <h2>投稿一覧</h2>
    <?php if(isset($count)): ?>
    <p>現在の投稿は<?php echo h($count); ?>件です</p>
    <?php foreach($rows as $row): ?>
      <div class="flexbox">
        <div class="posts">
          <a href='threads.php?boards_id=<?php echo h($row['boards_id']); ?>'>
            <?php echo h($row['name']); ?>
            <?php echo h($row['created_at']); ?><br>
            <?php echo h($row['title']); ?>
          </a>
        </div>
        <div class="button">
          <form action="change.php" method="get">
            <button type="submit" name="boards_id" value="<?php echo h($row['boards_id']); ?>">編集</button>
            <input type="hidden" name="title" value="<?php echo h($row['title']); ?>">
          </form>
          <form action="delete.php" method="get">
            <button type="submit" name="boards_id" value="<?php echo h($row['boards_id']); ?>">削除</button>
            <input type="hidden" name="title" value="<?php echo h($row['title']); ?>">
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    <?php else: ?>
      <p>投稿はまだありません。</p>
    <?php endif; ?>
  </body>
</html>
