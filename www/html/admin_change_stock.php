<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

//在庫数の変更ボタンが押された時
session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

$item_id = get_post('item_id');
$stock = get_post('stock');

//execute()の戻り値で判定
//update_item/stock → db.php
$params = [
  ['value'=>$stock,'type'=>PDO::PARAM_INT],
  ['value'=>$item_id,'type'=>PDO::PARAM_INT]
];
if(update_item_stock($db ,$params)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

redirect_to(ADMIN_URL);