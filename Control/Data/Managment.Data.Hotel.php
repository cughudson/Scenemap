<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/2
 * Time: 23:58
 */
session_start();
header("Content-Type:text/html;charset=gb2312");
require_once dirname(__FILE__, 2) . "/Lib/HotelManagment.php";

$admin = array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == TRUE;
$hotelContrl = $_SESSION['auth']['hotel'];

if ($_REQUEST['type'] == 'POST') {
    $respond = array("status" => false);
    echo json_encode($respond);
    return;
}

$page = $_POST['page'];
$state = $_POST['state'];
if (!is_integer($state) && !is_integer($page)) {
    $respond = array("status" => false);
    echo json_encode($respond);
    return;
}
try {
    $Hotel = new HotelManagment();
} catch (Exception $ex) {
    $respond = array("status" => false);
    echo json_encode($respond);
    return;
}

$start = $Hotel->configData['rowPerPage'] * $page;
if (!$admin) {
    $author = $_SESSION['userid'];
    $RespondData = $Hotel->GetDatasByStateAndAuthor($start, $state, $author);
} else {
    $RespondData = $Hotel->getDatasByState($start, $state);
}
$index = 1;
foreach ($RespondData as $row) {
    ?>
    <tr data-id=<?php echo $row[id] ?>>
        <td><?php echo $index ?></td>
        <td><?php echo $row['title'] ?></td>
        <td><?php echo $row['ctime'] ?></td>
        <td><?php echo $row['viewer'] ?></td>
        <?php if ($admin) {
            echo "<td>" . $row['author'] . "</td>";
        }
        ?>
        <td class="action">
            <?php if ($state == 0) { ?>
                <a class='hu-ui action-text' href='https://www.baidu.com' target='_blank'>编辑</a>
                <span class='hu-ui action-text' data-action='pull'>撤回</span>
                <span class='hu-ui action-text' data-action='delete'>删除</span>
            <?php } ?>
            <?php if ($state == 1) { ?>
                <a class='hu-ui action-text' href='https://www.baidu.com' target='_blank'>编辑</a>
                <span class='hu-ui action-text' data-action='delete'>删除</span>
            <?php } ?>
            <?php if ($state == 2) { ?>
                <span class='hu-ui action-text' data-action='restore'>恢复</span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>

