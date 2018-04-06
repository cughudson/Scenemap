<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/2
 * Time: 23:58
 */
session_start();
header("Content-Type:text/html;charset=utf-8");
require_once dirname(__FILE__, 2) . "/Lib/UserManagment.php";
if ($_REQUEST['type'] == 'POST') {
    die();
}
$admin = array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == TRUE;
if (!$admin) {
    $respond = array("status" => false);
    echo json_encode($respond);
    return;
}
try {
    $User = new UserManagment();
} catch (Exception $ex) {
    $respond = array("status" => false);
    echo json_encode($respond);
    return;
}
$respondData = $User->GetAllUser();
$index = 1;
foreach ($respondData as $row) {
    ?>
    <tr data-id=<?php echo $row[id] ?>>
        <td><?php echo $index ?></td>
        <td><?php echo $row['name'] ?></td>
        <td><?php echo $row['ctime'] ?></td>
        <td class="auth">
            <?php
            $sceneControl = $row['auth']['scene'];
            $hotelControl = $row['auth']['hotel'];
            $stateControl = $row['auth']['state'];
            ?>
            <div class="ui checkbox" <?php $sceneControl ? ' checked' : "" ?>>
                <input type="checkbox" data-type='scene'>
                <label>景点</label>
            </div>
            <div class="ui checkbox" <?php $stateControl ? ' checked' : "" ?>>
                <input type="checkbox" data-type='hotel'>
                <label>民宿</label>
            </div>
            <div class="ui checkbox" <?php $sceneControl ? ' checked' : "" ?>>
                <input type="checkbox" data-type='state'>
                <label>运行状态</label>
            </div>
        </td>
        <td class="action">
            <span class='hu-ui action-text delete' data-action='delete'>删除</span>
            <span class='hu-ui action-text modify' data-action='modifyname'>修改名称</span>
            <span class='hu-ui action-text modify' data-action='modifypsd'>修改密码</span>
            <span class='hu-ui action-text reset' data-action='resetpsd'>重置密码</span>
        </td>
    </tr>
<?php } ?>
