<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print($item['name']); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $item['image']); ?>">
              <figcaption>
                <?php print(number_format($item['price'])); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="token" value="<?php print $_SESSION['token']; ?>">
                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <div class="page_link">
      <!-- ページが1より大きかったら表示 -->
      <?php if ($page > 1) { ?>
        <a href="?page=<?php print $page-1 ?>">前へ</a>
      <?php } ?>
      <?php for ($i= 1; $i <= $total_page; $i++) { ?>
        <?php if ($page == $i) { ?>
          <span class="now_page"><?php print $page ?></span>
        <?php }else { ?>
          <a href="?page=<?php print $i ?>"><?php print $i ?></a>
        <?php } ?>
      <?php } ?>
      <!-- ページがトータルページ未満なら表示 -->
      <!-- ページが同じなら非表示 -->
      <?php if ($page < $total_page) { ?>
        <a href="?page=<?php print $page+1 ?>">次へ</a>
      <?php } ?>
    </div>
  </div>
  
</body>
</html>