<?php
/*
Plugin Name: Post Newsletter Plugin
Plugin URI: https://github.com/nyam-iwama/post-newsletter
Description:
「会報投稿プラグイン」
このプラグインでは、連番をもつ投稿の設定を
テンプレート化することでWordPress記事作成の
ルーチン作業をよりシンプルにすることができます。
適用した設定値は保存され、一度設定すると
挿入日付・連番・投稿日時のみ変更を行えば
テンプレート設定をそのままに投稿を
生成することができます。

このプラグインでは以下の①～④を設定し
投稿を生成します。

① タイトルと本文のテンプレート
② ①中に含まれる連番(①へのショートコード挿入で連番と日付を表示することができます。)
③ 投稿に付随するカテゴリーの選択
④ 投稿日時の設定
Version: 0.9.0
Author: Nyam
Author URI: http://www.nyam.co.jp/
License: MIT
*/


require_once 'nyam-const.php';     // 定数読み込み
require_once 'nyam-functions.php'; // 関数読み込み
add_action('admin_menu', 'add_post_newsletter_menu');
add_action('admin_menu', 'call_script');

function post_newsletter_top(){

  // Load WordPress 関数
  require_once(ABSPATH . 'wp-load.php');

  // 日本語の曜日配列
  $weekjp = array(
    constant('WEEK_JP_SUN'), // 0
    constant('WEEK_JP_MON'), // 1
    constant('WEEK_JP_TUE'), // 2
    constant('WEEK_JP_WED'), // 3
    constant('WEEK_JP_THU'), // 4
    constant('WEEK_JP_FRI'), // 5
    constant('WEEK_JP_SUT')  // 6
  );

  // オプションテーブルカラム名_設定値
  $option_name_setting_num = constant('OPTION_NAME_SETTING_NUM'); // 会報号_設定
  $option_name_ttl = constant('OPTION_NAME_TTL');                 // タイトル
  $option_name_txt = constant('OPTION_NAME_TXT');                 // 本文
  $option_name_cat = constant('OPTION_NAME_CAT');                 // カテゴリー

  // POST値取得用key
  $top_issued_date_key = constant('OPTION_NAME_ISSUED_DATE'); // 会報発行日
  $top_num_key = constant('OPTION_NAME_POST_TOP_NUM');        // 会報号_トップ
  $top_post_date_key = constant('OPTION_NAME_POST_DATE');      // 投稿日時

  // POST値取得用key配列
  $post_names = array($top_issued_date_key, $top_num_key, $top_post_date_key);

  // 設定値取得
  $setting_num = get_option($option_name_setting_num); // 会報号
  if(empty($setting_num)){$setting_num = '';}

  $setting_ttl = get_option($option_name_ttl);         // タイトル
  if(empty($setting_ttl)){$setting_ttl = '';}

  $setting_txt = get_option($option_name_txt);         // 本文
  if(empty($setting_txt)){$setting_txt = '';}

  $cats = get_option($option_name_cat);                // カテゴリー
  if(empty($cats)){$cats = array();}

  // 投稿するデータ項目key
  $post_title_key    = constant('POST_TITLE')   ; // タイトル
  $post_name_key     = constant('POST_NAME')    ; // スラッグ
  $post_status_key   = constant('POST_STATUS')  ; // 公開ステータス(予約済み)
  $post_content_key  = constant('POST_CONTENT') ; // 本文
  $post_category_key = constant('POST_CATEGORY'); // カテゴリー
  $post_date_key     = constant('POST_DATE')    ; // 公開日時

  // 投稿するデータ項目初期化
  $post_title    = '';
  $post_name     = '';
  $post_status   = 'future';
  $post_content  = '';
  $post_category = $cats;
  $post_date     = '';

  // 投稿するデータ項目配列初期化
  $post_vals = array(
    $post_title_key    => $post_title,
    $post_name_key     => $post_name,
    $post_status_key   => $post_status,
    $post_content_key  => $post_content,
    $post_category_key => $post_category,
    $post_date_key     => $post_date
  );

  $msg = '';
  $post = $_POST;

  if(!empty($post)) {

    // 入力フォーム入力済みチェック
    if(is_stored_post($post_names, $post)) {

      // 発行日フォーマット
      $d = strtotime($post[$top_issued_date_key]);
      $week = $weekjp[date('w', $d)];
      $day2 = date('Y年m月d日', $d);
      $day1 = $day2 . "({$week})";

      // タイトルフォーマット
      $post_num = $post[$top_num_key];
      if(empty($setting_ttl)) {
        $msg = '<p>設定が正しく適用されていません。</p>';
      } else {
        $post_title = to_optimaize($setting_ttl, strval($post_num), $day1, $day2);
      }
      $post_vals[$post_title_key] = $post_title;

      // 本文フォーマット
      if(empty($setting_txt)) {
        $msg = '<p>設定が正しく適用されていません。</p>';
      } else {
        $post_content = to_optimaize($setting_txt, strval($post_num), $day1, $day2);
      }
      $post_vals[$post_content_key] = $post_content;

      // 公開日時
      $post_date = date('Y-m-d H:i:s', strtotime($post[$top_post_date_key]));
      $post_vals[$post_date_key] = $post_date;

      // 公開ステータス
      // 公開日時が過去の場合は公開ステータスを「公開済み」にする
      $now = new DateTime();
      if($now < $post_date){
        $post_vals[$post_status_key] = 'publish';
      }


      var_dump($post_vals);

      // 投稿実行(投稿id取得)
      $id = wp_insert_post($post_vals);

    } else {
      // 投稿失敗
      // ToDo:適用失敗時は、各入力フォームvalueをPOST値にする
      $msg = '<p>全ての項目を入力してください。</p>';
    }
  }

  if(!empty($id)) {
    // 投稿成功
    $edit_url = admin_url() . "post.php?post={$id}&action=edit";
    $posted_obj = get_post($id);
    $posted_title = $posted_obj->post_title;
    $msg = "<p><a href=\"{$edit_url}\">{$posted_title}</a>を投稿しました。</p>";
  } elseif(isset($post[$top_issued_date_key]) || isset($post[$top_num_key]) || isset($post[$top_post_date_key])) {
    // 投稿失敗
    $msg = "<p>投稿に失敗しました。</p>";
    if(strval(intval($_POST['post_num'])) <= 0) { // intval($_POST['post_num'])=空白 は'0'(str)を返却
      $_POST['post_num'] = '';
    }
  }
  ?>

  <div class="wrap">
    <h1>記事投稿プラグイン</h1>
    <?php
      if(!empty($msg)){
        echo $msg;
      }
    ?>
    <form action="" method="POST">
    <h2>タイトルの日付</h2>
    <input type="date" name="<?= $top_issued_date_key; ?>" value="">
    <hr>
    <h2>会報号</h2>
    <input type="number" name="<?= $top_num_key; ?>" value="">
    <hr>
    <h2>投稿日時</h2>
    <input type="datetime-local" name="<?= $top_post_date_key; ?>" value="">
    <hr>
    <input type="submit" value="投稿(予約)する">
    </form>
  </div><!-- /.wrap -->
<?php
}
