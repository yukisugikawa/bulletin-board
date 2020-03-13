<?php
require 'config.php';

function h($s)
{
    return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}

// ログインしてたら掲示板へ
function require_logined_session()
{
  @session_start();
  if(isset($_SESSION['name'])){
    header('Location: boards.php');
    exit;
  }
}

// ログインしていなければログイン画面へ
function require_unlogined_session()
{
  //自動ログアウト
  ini_set('session.gc_maxlifetime', 3000);
  ini_set('session.gc_probability', 1);
  ini_set('session.gc_divisor', 1);
  @session_start();
  if(!isset($_SESSION['name'])){
    header('Location: login.php');
    exit;
  }
}

// トークン生成
function generate_token()
{
  return hash('sha256', session_id());
}

//バリデーション
class Validation
{

  private $error = [];

  //プロパティに格納されたエラーをgetする
  public function get_error()
  {
    return $this->error;
  }

  //未入力チェック
  private function blank_check($value='', $colName='')
  {
    if($value === ''){
      $this->error[] = $colName . 'は入力必須です。';
      return true;
    }
    return false;
  }

  // 最大文字数チェック
  public function check_max($value,$colName,$max)
  {
    $this->blank_check($value,$colName);
    if(mb_strlen($value) > $max){
    $this->error[] = $colName . 'は'. $max . '文字以下で入力してください。';
    }
    return true;
  }

  // 最小文字数チェック
  public function check_min($value,$colName,$min)
  {
    $this->blank_check($value,$colName);
    if(mb_strlen($value) < $min){
    $this->error[] = $colName . 'は'. $min . '文字以上で入力してください。';
    }
    return true;
  }

  //一致チェック
  public function check_match($value,$conf_value,$colName)
  {
    $this->blank_check($value,$colName);
    if($value !== $conf_value){
      $this->error[] = $colName . 'が一致しません。';
    }
    return true;
  }

  // 形式チェック
  public function check_type($value,$type)
  {
    switch($type){

      case "メールアドレス":
        if($this->blank_check($value,'メールアドレス')) return false;
        if(!filter_var($value,FILTER_VALIDATE_EMAIL)){
          $this->error[] = 'メールアドレスは正しい形式で入力してください。';
        }
        break;

      case "パスワード":
        if($this->blank_check($value,'パスワード')) return false;
        if(!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,10}+\z/i',$value)){
            $this->error[] = 'パスワードは半角英数字をそれぞれ1種類以上含む5文字以上10文字以下で入力してください。';
        }
        break;

        case "トークン":
          if(!$value === generate_token()){
            $this->error[] = 'トークンが一致しません。';
          }
          break;

      default:
        break;
    }
  }
}
