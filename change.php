<?php
require 'common.php';
require 'db.php';

$boards_id = filter_input(INPUT_GET, 'boards_id');
$threads_id = filter_input(INPUT_GET, 'threads_id');
$board_id = filter_input(INPUT_GET, 'board_id');
$title = filter_input(INPUT_GET, 'title');
$comment = filter_input(INPUT_GET, 'comment');

if(filter_input(INPUT_SERVER,'REQUEST_METHOD') === 'POST'){
	$change_pass = filter_input(INPUT_POST, 'change_pass');
	$new_title = filter_input(INPUT_POST, 'new_title');
	$new_comment = filter_input(INPUT_POST, 'new_comment');
	$token = filter_input(INPUT_POST, 'token');
	date_default_timezone_set('Asia/Tokyo');
	$created_at = date("Y-m-d H:i:s");

	$vdt = new Validation;
  $vdt->check_type($change_pass,'パスワード');
  $vdt->check_max($new_title,'タイトル',10);
  $vdt->check_max($new_comment,'コメント',30);
	$vdt->check_type($token,'トークン');
  $error = $vdt->get_error();

	$db = new DB;
	$sql = 'SELECT pass FROM users_registrations';
	$rows = $db->select($sql);
	foreach ($rows as $row) {
		$user_pass = $row['pass'];
	}

	if(empty($error)){
		if(isset($boards_id) && password_verify($change_pass,$user_pass)){
			$db = new DB;
			$sql = 'UPDATE boards SET title=?, created_at=? WHERE boards_id=?';
			$params = [];
			$params[] = $new_title;
			$params[] = $created_at;
			$params[] = $boards_id;
			$success_change_boards = $db->update($sql,$params);
		}elseif(isset($threads_id) && password_verify($change_pass,$user_pass)){
			$db = new DB;
			$sql = 'UPDATE threads SET comment=?, commented_at=? WHERE threads_id=?';
			$params = [];
			$params[] = $new_comment;
			$params[] = $created_at;
			$params[] = $threads_id;
			$success_change_threads = $db->update($sql,$params);
		}else{
			$error[] = 'パスワードが一致しません。';
	  }
	}
}
?>

<!DOCTYPE html>
<html>
  <head>
		<link rel="stylesheet" href="/css/styles.css">
    <meta charset="utf-8">
    <title>変更ページ</title>
  </head>
  <body>
		<?php if(isset($success_change_boards) && $success_change_boards): ?>
			<p>パスワードが一致し、投稿内容を編集しました。</p>
			<p><a href="boards.php">掲示板に戻る。</a></p>
		<?php elseif(isset($success_change_threads) && $success_change_threads): ?>
			<p>パスワードが一致し、投稿内容を編集しました。</p>
			<p><a href="threads.php?boards_id=<?php echo h($board_id); ?>">コメント欄に戻る</a></p>
    <?php else: ?>
			<?php if(isset($board_id)): ?>
				<p>投稿内容を編集します。宜しければパスワードと変更内容を入力してください。</p>
				<p>編集しない場合は<a href="threads.php?boards_id=<?php echo h($board_id); ?>">こちら</a>から。</p>
				<p class="strong">『<?php echo h($comment); ?>』</p>
				<?php if(isset($error) > 0): ?>
	        <?php foreach($error as $e): ?>
	          <p class="error"><?php echo h($e); ?></p>
	        <?php endforeach; ?>
	      <?php endif; ?>
				<form action="" method="post" autocomplete="off">
					<div class="cp_iptxt">
						<label class="ef">
						<input type="password" name="change_pass" placeholder="パスワードを入力してください。">
						</label>
					</div>
					<div class="cp_iptxt">
						<label class="ef">
						<textarea name="new_comment" rows="8" cols="80 "placeholder="新しいコメントを入力してください。"></textarea>
						</label>
					</div>
					<button type="submit" name="button">送信</button>
					<input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
		    </form>
			<?php else: ?>
				<p>投稿内容を編集します。宜しければパスワードと変更内容を入力してください。</p>
				<p>編集しない場合は<a href="boards.php">こちら</a>から。</p>
				<p class="strong">『<?php echo h($title); ?>』</p>
				<?php if(isset($error) > 0): ?>
	        <?php foreach($error as $e): ?>
	          <p class="error"><?php echo h($e); ?></p>
	        <?php endforeach; ?>
	      <?php endif; ?>
				<form action="" method="post" autocomplete="off">
					<div class="cp_iptxt">
						<label class="ef">
						<input type="password" name="change_pass" placeholder="パスワードを入力してください。">
						</label>
					</div>
					<div class="cp_iptxt">
						<label class="ef">
						<input type="text" name="new_title" placeholder="新しいタイトルを入力してください。">
						</label>
					</div>
					<input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
					<button type="submit">送信</button>
				</form>
			<?php endif; ?>
		<?php endif; ?>
  </body>
</html>
