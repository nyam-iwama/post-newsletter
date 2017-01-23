<?php

function post_newsletter_setting() {

  // ショートコード値
  $shortcode_n = constant('SHORTCODE_NUM');     // 会報号
  $shortcode_day1 = constant('SHORTCODE_DAY1'); // 日付(yyyy年M月d日(月～日))
  $shortcode_day2 = constant('SHORTCODE_DAY2'); // 日付(yyyy年M月d日)

  // オプションテーブルカラム名一覧
  $option_names = array(
    constant('OPTION_NAME_SETTING_NUM'), // 会報号入力フォーム
    constant('OPTION_NAME_TTL'),         // タイトル入力フォーム
    constant('OPTION_NAME_TXT'),         // 本文入力フォーム
    constant('OPTION_NAME_CAT')          // カテゴリーチェックBox
  );

  // name属性値
  $num_formname = $option_names[0];
  $ttl_formname = $option_names[1];
  $txt_formname = $option_names[2];
  $cat_formname = $option_names[3];

  // 既存カテゴリー一覧取得
  $args = array(
      'orderby' => 'id',
      'order'   => 'ASC',
      'hide_empty'    => '0'
  );
  $cat_all = get_categories($args);

  // メッセージ判定
  $is_err = false; // エラー有無
  $msg    = '';    // メッセージ内容

  $post = $_POST;
  // 「適用する」ボタンのクリック判定
  if(!empty($post)) {
    // 入力フォームが全て入力されているかチェック
    if(is_stored_post($option_names, $post)) {
      // 適用成功(オプションテーブルに入力値を保存)
      if(save_inputs($option_names, $post)) {
        $msg = '設定が適用されました。';
      }
    } else {
      // 適用失敗
      // ToDo:適用失敗時は、各入力フォームvalueをPOST値にする
      $is_err = true;
      $msg = '全ての項目を入力してください。';
    }
  }

?>

  <div class="wrap">
    <h2>会報投稿設定</h2>

    <!-- メッセージ出力 -->
    <?php
      if(!empty($msg)) {
        $ret = '<p';
        if($is_err) {
          $ret .= ' class="err"';
        }
        $ret .= '>' . $msg . '</p>';
        echo $ret;
      }
    ?>

  <!-- 各入力フォームvalueは、前回の適用値(適用失敗時はPOST値) -->
    <form action="" method="post">
      <?php wp_nonce_field('setting_num_action', 'newsletter_setting_num_nonce'); ?>
      <?php wp_nonce_field('setting_ttl_action', 'newsletter_setting_ttl_nonce'); ?>
      <?php wp_nonce_field('setting_cat_action', 'newsletter_setting_cat_nonce'); ?>

      <!-- 会報号設定入力フォーム -->
      <?php $val = get_option($num_formname); // 前回の適用値取得 ?>
      <h3>会報号設定</h3>
      <input type="number" name="<?= $num_formname ?>" value="<?php if(!empty($val)){echo $val;} ?>">

      <!-- タイトルテンプレート入力フォーム -->
      <?php $val = get_option($ttl_formname); // 前回の適用値取得 ?>
      <h3>タイトルテンプレート入力</h3>
      <input type="text" id="<?= $ttl_formname ?>" name="<?= $ttl_formname ?>" value="<?php if(!empty($val)){echo $val;} ?>" />
      <input type="button" onClick="insertShortcode('<?= $ttl_formname ?>', '<?= $shortcode_n; ?>')" value="[会報号]"/>
      <input type="button" onClick="insertShortcode('<?= $ttl_formname ?>', '<?= $shortcode_day1; ?>')" value="[日付(曜日)]"/>
      <input type="button" onClick="insertShortcode('<?= $ttl_formname ?>', '<?= $shortcode_day2; ?>')" value="[日付]"/>

      <!-- 本文テンプレート入力フォーム -->
      <?php $val = get_option($txt_formname); // 前回の適用値取得 ?>
      <h3>本文テンプレート入力</h3>
      <textarea id="<?= $txt_formname ?>" name="<?= $txt_formname ?>"><?php if(!empty($val)){echo $val;} ?></textarea>
      <input type="button" onClick="insertShortcode('<?= $txt_formname ?>', '<?= $shortcode_n; ?>')" value="[会報号]"/>
      <input type="button" onClick="insertShortcode('<?= $txt_formname ?>', '<?= $shortcode_day1; ?>')" value="[日付(曜日)]"/>
      <input type="button" onClick="insertShortcode('<?= $txt_formname ?>', '<?= $shortcode_day2; ?>')" value="[日付]"/>

      <!-- カテゴリー選択チェックBox -->
      <?php
        $vals = get_option($cat_formname); // 前回適用値取得
        if(empty($vals)){$vals = array();} // 保存値が配列型じゃない場合のエラー回避
      ?>

      <h3>投稿カテゴリー選択</h3>
      <?php
        if(count($cat_all) > 1) {
          foreach($cat_all as $term) {
            $cat_id   = $term->cat_ID;
            $cat_name = $term->cat_name;
            $is_checked = false;
            if(in_array($cat_id, $vals)) {
              $is_checked = true;
            }
      ?>
            <input style="margin: 0;" type="checkbox" name="<?= $cat_formname ?>[]" value="<?= $cat_id ?>" <?php if($is_checked){echo 'checked="checked"';} ?> />
      <?php
            echo $cat_name;
          } // /foreach
        } else {
      ?>
        <p>選択できるカテゴリーがありません。カテゴリーを作成してください。</p>
        <p>投稿 ＞ カテゴリー で編集することができます。</p>
      <?php
        } // /if
        submit_button('適用する');
      ?>
    </form>
  </div> <!-- /.wrap -->

<?php
} // /post_newsletter_setting()