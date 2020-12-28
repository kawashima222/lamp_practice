<?php
//共通の関数

function dd($var){
  var_dump($var);
  exit();
}

function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

function get_csrf_token() {
  //ランダムなバイナリ16文字を生成
  $token_byte = openssl_random_pseudo_bytes(16);
  //16進数に変換
  $token = bin2hex($token_byte);
  set_session('token',$token);
}

//引数のセッション名の値を取り出す
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

//セッションの値を初期化するための関数
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

//エラーをセッションにセット
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

//ユーザIDをセッションから取り出す
function is_logined(){
  return get_session('user_id') !== '';
}

function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

//ランダムなファイル名の作成
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

//
function save_image($image, $filename){
  //move_uploaded_file 定義済み関数
  //一時フォルダからアップロードするフォルダへ移動させる
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}



function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}


function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}


function entity_assoc_array($array) {
  foreach ($array as $key => $value ) {
      foreach ($value as $keys => $values ) {
          $array[$key][$keys] = entity_str($values);
      }
  }
  return $array;
}

function entity_str($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//トークンチェック
function token_check() {
  if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    return true;
  } else {
    return false;
  }
}