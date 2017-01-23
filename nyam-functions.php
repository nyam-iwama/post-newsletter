<?php

/** プラグインをWPメニューへ追加 */
function add_post_newsletter_menu(){

  // 「会報投稿プラグイン」メニュー生成
  add_menu_page('会報投稿プラグイン', '会報投稿プラグイン', 'manage_options', 'post-newsletter.php', 'post_newsletter_top');

  // 自動生成されるサブメニュー「会報投稿プラグイン」→「会報投稿」に変更
  add_submenu_page('post-newsletter.php', '会報投稿プラグイン', '会報投稿', 'manage_options', 'post-newsletter.php', 'post_newsletter_top');

  // サブメニュー「設定」生成
  require_once('post-newsletter-setting.php');
  add_submenu_page('post-newsletter.php', '設定', '設定', 'manage_options', 'post-newsletter-setting.php', 'post_newsletter_setting');
}

/** 自作JavaScript呼び出し */
function call_script(){
  $dir_url = plugins_url() . '/post-newsletter/js/insert-shortcode.js';
  echo '<script type="text/javascript" src="' . $dir_url . '"></script>';
}

/**
 * 入力フォームの$_POST値全てが
 * emptyではないときにTRUEを返します。
 *
 * @param Array $post_names, Array, $_POST
 * @return Boolean
 */
function is_stored_post($post_names, $post) {
  foreach($post_names as $key=>$val) {
    if(empty($post[$val])){return false;}
  }
  return true;
}

/**
 * 各オプションテーブルカラム(引数)に対応する値を保存します。
 * 保存に成功した場合はTRUEを返します。
 *
 * @param Array $option_names(index=>option_name), Array $post($_POST)
 * @return Boolean(TRUE)
 */
function save_inputs($option_names, $post) {
  foreach($option_names as $idx=>$name) {
    update_option($name, $post[$name]);
  }
  return true;
}

/**
 * タイトル・本文最適化
 *
 * @param String $str(最適化したい文字列),
 *        String $num(会報号),
 *        String $day1(Y年m月d日(曜)),
 *        String $day2(Y年m月d日))
 * @return String
 * */
function to_optimaize($str, $num, $day1, $day2) {
  $code_num  = constant('SHORTCODE_NUM');
  $code_day1 = constant('SHORTCODE_DAY1');
  $code_day2 = constant('SHORTCODE_DAY2');

  $str = str_replace($code_num, $num, $str);
  $str = str_replace($code_day1, $day1, $str);
  $str = str_replace($code_day2, $day2, $str);

  return $str;
}