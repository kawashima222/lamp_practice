<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// DB利用

//指定した商品のみ取り出す
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";
  $params = [
    ['value'=>$item_id,'type'=>PDO::PARAM_INT]
  ];

  return fetch_query_bind($db ,$sql, $params);
}

//全ての商品を取り出す
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_query_bind($db, $sql);
}

function get_all_items($db){
  return get_items($db);
}

function get_open_items($db){
  return get_items($db, true);
}

function regist_item($db, $name, $price, $stock, $status, $image){
  //ランダムなファイル名 or false
  $filename = get_upload_filename($image);
  //この中にfalseがあるか確認
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  $params = [
    ['value'=>$name,'type'=>PDO::PARAM_STR],
    ['value'=>$price,'type'=>PDO::PARAM_INT],
    ['value'=>$stock,'type'=>PDO::PARAM_INT],
    ['value'=>$filename,'type'=>PDO::PARAM_STR],
    ['value'=>$status,'type'=>PDO::PARAM_INT]
  ];
  return regist_item_transaction($db ,$params ,$image);
}

//DBに書き込み
//戻り値:true or false
function regist_item_transaction($db, $params ,$image){
  $db->beginTransaction();
  if(insert_item($db, $params) 
  // if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $params['3']['value'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

//変更済み
function insert_item($db, $params){
  // $status = open なら 1
  // $status = close なら 2
  $params['4']['value'] = PERMITTED_ITEM_STATUSES[$params['4']['value']];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(?, ?, ?, ?, ?);
  ";

  return execute_query_bind($db, $params,$sql);
}

//変更済み
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  $params = [
    ['value'=>$status,'type'=>PDO::PARAM_INT],
    ['value'=>$item_id,'type'=>PDO::PARAM_INT]
  ];
  return execute_query_bind($db ,$params ,$sql);
}

//変更済み
function update_item_stock($db, $params){
  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query_bind($db, $params, $sql);
}

//商品削除用
function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

//変更済み
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  $params = [
    ['value'=>$item_id,'type'=>PDO::PARAM_INT]
  ];
  return execute_query_bind($db ,$params, $sql);
}


// 非DB
function is_open($item){
  return $item['status'] == 1;
}

//商品を追加した時用
//ここからバリデーション
function validate_item($name, $price, $stock, $filename, $status){
  //戻り値はture or false
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}