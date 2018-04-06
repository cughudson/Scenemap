<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/2
 * Time: 23:58
 */
session_start(['cookie_lifetime' => 30 * 60]);
header("Content-Type:text/html; charset=utf8");
if ($_SERVER["REQUEST_METHOD"] == 'POST') die();

if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
    die();
}
if (isset($_SESSION['time'])) {
    if ($_SESSION['time'] < time()) {
        session_unset();
        session_destroy();
        echo "<script type='text/javascript'>alert('会话已经过期，如需继续操作，请重新登陆')</script>";
        echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
require_once dirname(__FILE__, 2) . "/Lib/SceneManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";

$admin = (array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == TRUE);
$page = (int)$_GET['page'];
$state = (int)$_GET['state'];
$index = 1;
$Scene = new SceneManagment();
$BaseUrl = $Scene->configData["url"];
if (!$admin) {
    $author = $_SESSION['uuid'];
    $RespondData = $Scene->GetDatasByStateAndAuthor($page, $state, $author);
} else {
    $RespondData = $Scene->getDatasByState($page, $state);
}
if (count($RespondData) == 0) {
    echo "<td colspan='6'><div class='ui info message'>无任何相关数据</div></td>";
    die();
}
foreach ($RespondData as $row) {
    ?>
    <tr data-id=<?php echo $row[id] ?>>
        <td><?php echo $index++ ?></td>
        <td><?php echo $row['title'] ?></td>
        <td><?php echo $row['author'] ?></td>
        <td><?php echo $row['ctime'] ?></td>
        <td><?php echo $row['viewers'] ?></td>
        <td class="action scene hotel">
            <?php if ($state == 0) { ?>
                <a class='hu-ui action-text' href='<?php echo $BaseUrl . "/edit.php?type=scene&id=" . $row[id] ?>'
                   target='_blank'>编辑</a>
                <span class='hu-ui action-text' data-action='pull'>撤回</span>
                <span class='hu-ui action-text' data-action='waste'>删除</span>
            <?php } ?>
            <?php if ($state == 1) { ?>
                <a class='hu-ui action-text'
                   href='<?php echo $BaseUrl . "/edit.php?type=scene&id=" . $row[id] ?>'>编辑</a>
                <span class='hu-ui action-text' data-action='waste'>删除</span>
            <?php } ?>
            <?php if ($state == 2) { ?>
                <span class='hu-ui action-text' data-action='restore'>恢复</span>
                <span class="'hu-ui action-text" data-action='delete' title="彻底删除数据">删除</span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
