<?php

// 更新時に真っ先に変えなきゃいけないゾーン
$version = "151128";
$rating_all = 2367; // Rating基準値 (アーニャソロまでのレベル合計になってます)
$music_max = 40; // 全曲数
$music_max_masplus = 0; // マスプラの曲数

// 画像読み込み
$img = imagecreatefrompng ( 'img/body.png' );
$img_stamp = imagecreatefrompng ( 'img/stamp.png' );
$img_ss = imagecreatefrompng ( 'img/ss.png' );
$img_s = imagecreatefrompng ( 'img/s.png' );
$img_a = imagecreatefrompng ( 'img/a.png' );
$img_b = imagecreatefrompng ( 'img/b.png' );
$img_c = imagecreatefrompng ( 'img/c.png' );
$img_d = imagecreatefrompng ( 'img/d.png' );
$img_e = imagecreatefrompng ( 'img/e.png' );
$img_f = imagecreatefrompng ( 'img/f.png' );

$debut = 0;
$regular = 0;
$pro = 0;
$master = 0;
$maspuls = 0;

// フォントの指定
$font = 'font/mplus-2c-regular.ttf';

// 色の指定
$white = ImageColorAllocate ( $img, 0xFF, 0xFF, 0xFF );
$black = ImageColorAllocate ( $img, 0x00, 0x00, 0x00 );
$pink = ImageColorAllocate ( $img, 0xDA, 0x70, 0x6D );
$orange = ImageColorAllocate ( $img, 0xff, 0x9b, 0x38 );
$blue = ImageColorAllocate ( $img, 0x41, 0x69, 0xE1 );
$green = ImageColorAllocate ( $img, 0x00, 0x80, 0x00 );
$yellow = ImageColorAllocate ( $img, 0xBD, 0xB7, 0x6B );
$red = ImageColorAllocate ( $img, 0xDC, 0x14, 0x3C );
$dark_purple = ImageColorAllocate ( $img, 0x48, 0x3D, 0x8B );

$img_songs = glob ( 'songs/*.png' );
rsort ( $img_songs );

$arr = $_POST ['arr']; // postだと長ったらしくて毎回入力するのが面倒なのでぶち込む
rsort ( $arr );

// プロデューサーランクを入れる処理
$p_rank = array (
		20, // x
		185
); // y

switch (mb_convert_encoding ( $_POST ["p_rank"], 'UTF-8', 'auto' )) {
	case "SS" :
		imagecopy ( $img, $img_ss, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "S" :
		imagecopy ( $img, $img_s, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "A" :
		imagecopy ( $img, $img_a, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "B" :
		imagecopy ( $img, $img_b, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "C" :
		imagecopy ( $img, $img_c, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "D" :
		imagecopy ( $img, $img_d, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "E" :
		imagecopy ( $img, $img_e, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
	case "F" :
		imagecopy ( $img, $img_f, $p_rank [0], $p_rank [1], 0, 0, 61, 61 );
		break;
}

// 曲情報を取得する処理
/*
 * 後で困らないためのメモ
 * 0 = debut
 * 1 = regular
 * 2 = pro
 * 3 = master
 * 4 = master+ (まだつくってない 通常曲として解禁される日は来るのだろうか・・・)
 *
 * 曲idはgoogleSpreadsheetに保存してあるアレ (随分前に作ったので0は存在しない ここで使うと思ってなかったんやで・・・)
 *
 * Ex Lv8の8曲目 → 08_8
 * Lv28の3曲目 → 28_3
 */
// レベル何処まで作るかの設定
if ($_POST ["limit_1"] < $_POST ["limit_2"]) {
	$upper_limit = $_POST ["limit_2"];
	$lower_limit = $_POST ["limit_1"];
} else {
	$upper_limit = $_POST ["limit_1"];
	$lower_limit = $_POST ["limit_2"];
}

$level_counts = 23 - ($upper_limit - $lower_limit);

$size_down = $upper_limit - 3;
$current_level = $upper_limit;
$set_x = 270;
$set_y = 5;
$img_music_size = 60 + ($level_counts - 1); // 画像サイズ
$img_music_size_default = $img_music_size;
$down_size = 15; // Lv3先どれぐらい小さくするか

$music_position = array (); // 曲の位置を記録するためのやつ

foreach ( $img_songs as $pref ) { // ここから配列がカラになるまでループ

	// 上限レベル以上だった場合スキップする処理
	if (substr ( $pref, 6, 2 ) > $upper_limit) {
		continue;
	}

	// 端っこまで行ったら折り返す処理
	if ($set_x + $img_music_size > 1024) {
		$set_x = 270;
		$set_y = $set_y + $img_music_size;
	}

	// 指定下限まで来たら抜ける処理
	if (substr ( $pref, 6, 2 ) < $lower_limit) {
		break 1;
	}

	// 上限の4レベル以降は小さくする処理
	if (substr ( $pref, 6, 2 ) == $size_down) {
		$set_x = 270;
		$set_y = $set_y + $img_music_size;
		$img_music_size = $img_music_size - $down_size;
		$size_down = false;
	}

	// 区切り画像追加する処理
	if (substr ( $pref, 6, 2 ) == $current_level) {
		$current_level --;

		if ($set_x + $img_music_size + $img_music_size > 1024) {
			$set_x = 270;
			$set_y = $set_y + $img_music_size;
		}

		$img_lv = imagecreatefrompng ( "img/lv/" . substr ( $pref, 6, 2 ) . ".png" );
		$width = ImageSx ( $img_lv );
		$height = ImageSy ( $img_lv );
		$resize = ImageCreateTrueColor ( $img_music_size, $img_music_size );
		imagealphablending ( $resize, false );
		imagesavealpha ( $resize, true );
		ImageCopyResampled ( $resize, $img_lv, 0, 0, 0, 0, $img_music_size, $img_music_size, $width, $height );
		imagecopy ( $img, $resize, $set_x, $set_y, 0, 0, $img_music_size, $img_music_size );

		$set_x = $set_x + $img_music_size;
	}

	$img_music = imagecreatefrompng ( $pref );

	// 縮小処理
	$width = ImageSx ( $img_music );
	$height = ImageSy ( $img_music );
	$resize = ImageCreateTrueColor ( $img_music_size, $img_music_size );
	imagealphablending ( $resize, false );
	imagesavealpha ( $resize, true );
	ImageCopyResampled ( $resize, $img_music, 0, 0, 0, 0, $img_music_size, $img_music_size, $width, $height );

	imagecopy ( $img, $resize, $set_x, $set_y, 0, 0, $img_music_size, $img_music_size );

	$str0 = str_replace ( "songs/", "", $pref );
	$str = str_replace ( ".png", "", $str0 );

	$music_position += array (
			$set_x . "_" . $set_y => $str
	);

	$set_x = $set_x + $img_music_size;
} // foreachおわり

// フルコンのスタンプ付ける処理
$size_down = $upper_limit - 3;
$img_music_size = $img_music_size_default;
$img_stamp = imagecreatefrompng ( 'img/stamp.png' );

foreach ( $arr as $pref ) {

	// 合計曲数を出す処理
	if (substr ( $pref, 0, 2 ) <= 9) {
		$debut ++;
	} elseif (substr ( $pref, 0, 2 ) <= 14) {
		$regular ++;
	} elseif (substr ( $pref, 0, 2 ) <= 19) {
		$pro ++;
	} elseif (substr ( $pref, 0, 2 ) <= 28) {
		$master ++;
	} elseif (substr ( $pref, 0, 2 )) {
		$masplus ++;
	}

	// 上限の4レベル以降は小さくする処理
	if (substr ( $pref, 0, 2 ) == $size_down) {
		$set_x = 270;
		$set_y = $set_y + $img_music_size;
		$img_music_size = $img_music_size - $down_size;
		$size_down = false;
	}

	$key = array_search ( $pref, $music_position );

	if ($key == FALSE) {
		continue;
	} else {

		$stamp_size = $img_music_size;

		$key1 = strstr ( $key, '_', true );
		$key2 = str_replace ( $key1 . "_", "", $key );

		if ($pref == "23_0") {
			$img_stamp = imagecreatefrompng ( 'img/stamp_c.png' );
			;
		} else {
			$img_stamp = imagecreatefrompng ( 'img/stamp.png' );
		}

		// 縮小処理
		$width = ImageSx ( $img_stamp );
		$height = ImageSy ( $img_stamp );
		$resize = ImageCreateTrueColor ( $stamp_size, $stamp_size );
		imagealphablending ( $resize, false );
		imagesavealpha ( $resize, true );
		ImageCopyResampled ( $resize, $img_stamp, 0, 0, 0, 0, $stamp_size, $stamp_size, $width, $height );

		imagecopymerge ( $img, $resize, $key1, $key2, 0, 0, $stamp_size, $stamp_size, 55 );


	}
} // foreachおわり

//合計曲数を入れる
ImageTTFText ( $img, 20, 0, 160, 280, $black, $font, $debut . " / " . $music_max );
ImageTTFText ( $img, 20, 0, 160, 311, $black, $font, $regular . " / " . $music_max );
ImageTTFText ( $img, 20, 0, 160, 342, $black, $font, $pro . " / " . $music_max );
ImageTTFText ( $img, 20, 0, 160, 373, $black, $font, $master . " / " . $music_max );

// プロデューサー情報を入れる処理

// -- P名
  // P名の文字数を判断してフォントサイズ変える処理
$name = mb_convert_encoding ( $_POST ["name"], 'UTF-8', 'auto' );
if (mb_strlen ( $name ) <= 5) {
	$name_size = 28;
} elseif (mb_strlen ( $name ) <= 7) {
	$name_size = 26;
} else {
	$name_size = 19;
}
ImageTTFText ( $img, $name_size, 0, 10, 95, $black, $font, $name );

// -- Twitter
ImageTTFText ( $img, 20, 0, 11, 127, $black, $font, "@" . mb_convert_encoding ( $_POST ["twitter"], 'UTF-8', 'auto' ) );

// -- PRP
$prp = mb_convert_encoding ( $_POST ["prp"], 'UTF-8', 'auto' );
if (mb_strlen ( $prp ) <= 3) {
	$prp = "  " . $prp;
} else {
	$prp;
}
ImageTTFText ( $img, 24, 0, 185, 203, $black, $font, $prp );

// -- PLv
ImageTTFText ( $img, 24, 0, 185, 243, $black, $font, "  " . $_POST ["plv"] );

// -- ゲームID
ImageTTFText ( $img, 22, 0, 90, 165, $black, $font, $_POST ["game_id"] );

// 生成方法表示
if ($_POST ["limited_1"] == "Limited") {
	ImageTTFText ( $img, 12, 0, 0, 570, $black, $font, "※限定楽曲を除く" );
} elseif ($_POST ["limited_1"] == "CD") {
	ImageTTFText ( $img, 12, 0, 0, 570, $black, $font, "※先行解禁曲を除く" );
}

// バージョン表記
ImageTTFText ( $img, 10, 0, 270, 572, $green, $font, "Powered by fcManagementTool 4 sl-stage (svr.aki-memo.net)" );
imagefttext ( $img, 10, 0, 678, 518, $green, $font, "
©BANDAI NAMCO Entertainment Inc.
©BNEI / PROJECT CINDERELLA
Created by Slime_hatena
画像データをはじめとした著作物は著作者様に帰属します。", $extrainfo = Array (
		"linespacing" => 0.7
) );

// 画像をbase64でimgタグに突っ込むための処理
ob_start ();
imagePNG ( $img );
$content = base64_encode ( ob_get_contents () );
ob_end_clean ();

// ファイル消しておわり
imagedestroy ( $img );
imagedestroy ( $img_stamp );

?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="utf-8">
<title>result - fcManagementTool 4 sl-stage</title>
<meta name="viewport"
	content="width=device-width; initial-scale=1.0; maximum-scale=1.0; maximum-scale=10; user-scalable=1">
<!-- favicon -->
<link rel="shortcut icon" href="img/icon/favicon.ico">
</head>



<body bgcolor="#EFEFEF" text="#000000">


	<a href="twitter://post?message=<?php echo $tweet; ?> %23デレステ"><img
		src="img/tweetbutton.png" height="30" alt="ツイートする"></a>
	<p style="font-size: 12px;">
		画像を保存してから押してください。<br /> 公式クライアントがインストールされている必要があります。<br />
		ツイート欄が開いたら画像を添付してツイートしてください。<br /> <br />
		現在一部の端末から画像が保存できない現象を確認しています。お手数ですがスクリーンショットでの代用をお願いします。<br />
		保存できない場合は<a href="https://twitter.com/Slime_hatena" target=_blank>@Slime_hatena</a>に端末、ブラウザ名をお知らせいただけると助かります。
	</p>

	<hr>
	<p style="font-size: 12px;">
		以下コピペ用<br /> 公式クライアントをインストールしていない時などにお使いください。
	</p>


	<!--  <p style="font-size: 9px"><?php echo $tweet; ?> #デレステ</p> -->

	<hr>

	<img style="width: 100%;"
		src="data:image/png;base64,<?php echo $content; ?>" alt="img" />




</body>
</html>


