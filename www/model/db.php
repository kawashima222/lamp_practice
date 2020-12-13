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

// function fetch_query($db, $sql, $params = array()){
//   try{
//     $statement = $db->prepare($sql);
//     $statement->execute($params);
//     return $statement->fetch();
//   }catch(PDOException $e){
//     set_error('データ取得に失敗しました。');
//   }
//   return false;
// }

// function fetch_all_query($db, $sql, $params = array()){
//   try{
//     $statement = $db->prepare($sql);
//     $statement->execute($params);
//     return $statement->fetchAll();
//   }catch(PDOException $e){
//     set_error('データ取得に失敗しました。');
//   }
//   return false;
// }

function fetch_query($db ,$sql ,$params = array()){
  $i = 0;
  try{
    $statement = $db->prepare($sql);
    foreach ($params as $key=>$value) {
      $statement -> bindValue(++$i,$value['value'],$value['type']);
    }
    $statement->execute();
    return $statement->fetch();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

//関数の共通化
function fetch_query_bind($db ,$sql ,$params = array()){
  $i = 0;
  try{
    $statement = $db->prepare($sql);
    foreach ($params as $key=>$value) {
      $statement -> bindValue(++$i,$value['value'],$value['type']);
    }
    $statement->execute();
    return $statement->fetchAll();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}



//bindValue無し
function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}

//bindValue有り
//関数の共通化
function execute_query_bind($db ,$params ,$sql){
  $i = 0;
  try{
    $statement = $db->prepare($sql);
    foreach ((array)$params as $key=>$value) {
      //$i++だと判定がtrueならプラスになるため++$iにする
      $statement -> bindValue (++$i,$value['value'],$value['type']);
    }
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