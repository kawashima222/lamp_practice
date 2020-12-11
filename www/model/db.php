<?php

function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  try {
    // データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}

function fetch_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    $statement->execute($params);
    return $statement->fetch();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

function fetch_all_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}

//共通化用
function execute_query_stock($db, $sql, $params = array()){
  $i = 0;
  try{
    $statement = $db->prepare($sql);
    foreach ($params as $key => $value) {
      var_dump($value['value']);
      var_dump($value['type']);
        $statement -> bindValue($i++,$value['value'],$value['type']);
      }
    return $statement->execute();
  }catch(PDOException $e){
    print $e;
    set_error('更新に失敗しました。');
  }
  return false;
}

//itemの追加用
function execute_query_item($db, $name ,$price ,$stock ,$filename, $status_value ,$sql){
  // print $status;
  try{
    $statement = $db->prepare($sql);
    $statement -> bindValue(1,$name,PDO::PARAM_STR);
    $statement -> bindValue(2,$price,PDO::PARAM_INT);
    $statement -> bindValue(3,$stock,PDO::PARAM_INT);
    $statement -> bindValue(4,$filename,PDO::PARAM_STR);
    $statement -> bindValue(5,$status_value,PDO::PARAM_INT);
    return $statement->execute();
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}

//stockの更新用
// function execute_query_stock($db, $item_id, $stock, $sql){
//   try{
//     $statement = $db->prepare($sql);
//     $statement->bindValue(1,$stock  ,PDO::PARAM_INT);
//     $statement->bindValue(2,$item_id,PDO::PARAM_INT);
//     //trueかfalse
//     //元々は上のexecute_queryと同じ
//     //executeにパラメータを渡すとPDOでbaindされているパラメータが消える
//     return $statement->execute();
//   }catch(PDOException $e){
//     set_error('更新に失敗しました。');
//   }
//   return false;
// }