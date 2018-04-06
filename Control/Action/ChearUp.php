<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/3/10
 * Time: 17:02
 */
//访客的session有效期为3个小时
session_start(['cookie_lifetime' => 180 * 60]);
include_once dirname(__FILE__, 2) . "/Lib/SceneManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") die();
$id = $_POST["id"];
//如果没有点过赞
function FindArticleInSession($id, $SESSION)
{
    if (isset($SESSION['cheararticle'])) {
        if (!array_key_exists("cheararticle", $SESSION)) {
            return 1;
        }
        $cleararticle = $SESSION['cheararticle'];
        foreach ($cleararticle as $index => $item) {
            $temp = explode("&", $item);
            if ($temp[0] == $id) {
                return array($index => $item);
            }
        }
        return 0;
    } else {
        return 1;
    }
}

$Scene = new SceneManagment();
$result = FindArticleInSession($id, $_SESSION);
if ($result == 0) {
    $Scene->UpdateChearup($id);
    $respond = array(
        "status" => true,
    );
    // ChromePhp::log($_SESSION);
    $_SESSION['cheararticle'][] = (string)$id . '&' . (string)time();
    echo json_encode($respond);
    //如果点过赞
} else if ($result == 1) {
    $_SESSION['cheararticle'] = array();
    $Scene->UpdateChearup($id);
    $respond = array(
        "status" => true,
    );
    $_SESSION['cheararticle'][] = (string)$id . '&' . (string)time();
    echo json_encode($respond);
} else {
    $chearTime = (int)explode("&", array_pop($result))[1];
    //如果有过期
    if ($chearTime + 30 * 60 < time()) {
        array_replace($_SESSION['cheararticle'], $result, array(array_keys($result) . pop() => $id . "&" . time()));
        $Scene->UpdateChearup($id);
        $respond = array(
            "status" => true,
        );
        echo json_encode($respond);
    } else {
        $respond = array(
            "status" => false,
        );
        echo json_encode($respond);
    }
}
