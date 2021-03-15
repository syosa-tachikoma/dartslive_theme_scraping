<?php
include_once("../conf/configure.inc");

echo "------- START -------\n";

// Cookieを同じ場所に保存しています。
$cookie_path = './cookie.txt';

$dsn = 'mysql:dbname=' . $config['db']['schema'] . ';host=' . $config['db']['host'];
$db = new PDO($dsn, $config['db']['login_id'], $config['db']['login_pwd']);

// 有効なユーザー情報を取得
$user_sql = "SELECT id, user_name, login_id, password FROM m_user WHERE delete_flg = 0";
$user_list = [];
foreach($db->query($user_sql) as $row) {
    $user_list[] = ["id" => $row['id'], "user_name" => $row['user_name'], "login_id" => $row['login_id'], "password" => $row['password']];
}

// カテゴリ情報を取得
$cate_sql = "SELECT category_id, category_name FROM m_category WHERE delete_flg = 0";
$cate_list = [];
foreach($db->query($cate_sql) as $row) {
    $cate_list[] = ["category_id" => $row['category_id'], "category_name" => $row['category_name']];
}

$duplicate_check = [];
$theme_list = [];
$user_theme_list = [];

//パラメータをセット
foreach ($user_list as $user => $info) {
    // ログインクッキー保存ファイルを作成
    touch($cookie_path);

    //ログインページへ移動
    $login_url = sprintf($config['login_url'], $info['login_id'], $info['password']);
    $res = scraping($login_url, false);

    // カテゴリ分ループ処理
    foreach ($cate_list as $cate_key => $cate_val) {
        $page = 0;

        while (true) {
            $page++;
            // スクレイピングするURLを作成してHTMLを取得
            $theme_url = sprintf($config['theme_url'], $cate_val['category_id'], $page);
            $res = scraping($theme_url, true);

            // テーマブロックを切り取る
            preg_match_all("/<li class=\"item(.*?)<\/li>/us", $res, $themes);

            foreach ($themes[1] as $key => $val) {
                // テーマ名を切り取る
                preg_match_all("/<div class=\"name2\">(.*?)<\/div>/u", $val, $theme);
                preg_match_all("/<img src=\"(.*?)\"/u", $val, $image);

                if (!file_exists($config["image_path"]) || !is_dir($config["image_path"])) {
                    mkdir($config["image_path"]);
                }

                $theme_id = hash("md5", $theme[1][0]);
                $user_theme_list[$info['id']][] = $theme_id;
                $image_name = $theme_id . ".jpg";
                $image_path = $config["image_path"] . $image_name;

                if (file_exists($image_path) || in_array($theme_id, $duplicate_check, true)) {
                    // すでに保持済みのテーマ名ならスキップ
                    continue;
                }

                $duplicate_check[] = $theme_id;

                $image_url = sprintf($config["image_url"], $image[1][0]);
                $contents = file_get_contents($image_url);
                file_put_contents($image_path, $contents);

                $theme_list[] = ["theme_id" => $theme_id, "category_id" => $cate_val['category_id'], "theme_name" => $theme[1][0], "image_name" => $image_name];
            }

            // 次ページが存在するかチェックする
            preg_match_all("/<li class=\"pagerNext\">(.*?)<\/li>/su", $res, $match);

            if (empty($match[0])) {
                // 次ページがなければ対象カテゴリのスクレイピングを終了
                break;
            }
        }
    }

    //Cookieの削除
    unlink($cookie_path);
}

try {
    $db->beginTransaction();

    $ins_theme_sql = "INSERT INTO m_theme(theme_id, category_id, theme_name, image_name, copy_flg, delete_flg, created_at, updated_at) "
                . "VALUES(:theme_id, :category_id, :theme_name, :image_name, 0, 0, now(), now())";

    // 取得したテーマをDBに登録していく
    foreach ($theme_list as $key => $theme) {
        $theme_stmt = $db->prepare($ins_theme_sql);
        $theme_stmt->bindParam(":theme_id", $theme['theme_id'], PDO::PARAM_STR);
        $theme_stmt->bindParam(":category_id", $theme['category_id'], PDO::PARAM_STR);
        $theme_stmt->bindParam("theme_name", $theme['theme_name'], PDO::PARAM_STR);
        $theme_stmt->bindParam("image_name", $theme['image_name'], PDO::PARAM_STR);

        $res = $theme_stmt->execute();

        if (!$res) {
            var_dump($theme_stmt->errorInfo());
        }
    }

    // 各人の持ってるテーマ情報を更新
    $ins_user_sql = "INSERT IGNORE INTO t_user_theme_rel(user_id, theme_id, delete_flg, created_at, updated_at) "
                . "VALUES (:user_id, :theme_id, 0, now(), now())";

    foreach ($user_theme_list as $user_id => $val) {
        foreach ($val as $k => $theme_id) {
            $user_stmt = $db->prepare($ins_user_sql);
            $user_stmt->bindParam(":user_id", $user_id);
            $user_stmt->bindParam(":theme_id", $theme_id);

            $res = $user_stmt->execute();

            if (!$res) {
                var_dump($user_stmt->errorInfo());
            }
        }
    }

    $db->commit();
} catch(Exception $e) {
    $db->rollBack();
    var_dump($e);
    exit;
}

echo "------- END -------\n";

function scraping($url, $opt_head) {
    global $cookie_path;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($opt_head) {
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_path);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_path);
    // curl_setopt($ch, CURLOPT_POST, TRUE);
    $output = curl_exec($ch) or die('error ' . curl_error($ch));
    curl_close($ch);

    mb_language("Japanese");
    $html_source = mb_convert_encoding($output, "UTF-8", "auto");

    return $html_source;
}
?>