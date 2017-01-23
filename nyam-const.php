<?php

/**
 * 定数
 */
// 日本語の曜日
define('WEEK_JP_SUN', '日');
define('WEEK_JP_MON', '月');
define('WEEK_JP_TUE', '火');
define('WEEK_JP_WED', '水');
define('WEEK_JP_THU', '木');
define('WEEK_JP_FRI', '金');
define('WEEK_JP_SUT', '土');

// 記事投稿用key
define('POST_TITLE', 'post_title');
define('POST_NAME', 'post_name');
define('POST_STATUS', 'post_status');
define('POST_CONTENT', 'post_content');
define('POST_CATEGORY', 'post_category');
define('POST_DATE', 'post_date');

// ショートコード値
define('SHORTCODE_NUM', '[serial_num]'); // 会報号
define('SHORTCODE_DAY1', '[day1]');      // 日付(Y年m月d日(曜))
define('SHORTCODE_DAY2', '[day2]');      // 日付(Y年m月d日)

// オプションテーブルカラム名_トップ画面
define('OPTION_NAME_ISSUED_DATE', 'issued_date'); // 会報発行日
define('OPTION_NAME_POST_TOP_NUM', 'top_num');    // 会報号_トップ
define('OPTION_NAME_POST_DATE', 'post_date');     // 投稿日時

// オプションテーブルカラム名_設定画面
define('OPTION_NAME_SETTING_NUM', 'setting_num'); // 会報号_設定
define('OPTION_NAME_TTL', 'setting_ttl');         // タイトル
define('OPTION_NAME_TXT', 'setting_txt');         // 本文
define('OPTION_NAME_CAT', 'setting_cat');         // カテゴリー