<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 13:16
 */
session_start(['cookie_lifetime' => 30 * 60 * 24]);
if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
    echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
    die();
}
if (isset($_SESSION['time'])) {
    if ($_SESSION['time'] < time()) {
        session_unset();
        session_destroy();
        echo "<script type='text/javascript'>alert('会话已经过期，如需继续操作，请重新登陆')</script>";
        echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
        die();
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
include_once dirname(__FILE__, 3) . "/Control/Lib/UserManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
$admin = array_key_exists('admin', $_SESSION) && ($_SESSION['admin']);
?>
    <table class='ui celled table'>
        <thead>
        <tr>
            <th>序号</th>
            <th>登录名</th>
            <?php if ($admin) { ?>

                <th>真实姓名</th>
                <th>创建时间</th>
                <th>权限</th>
            <?php } ?>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!$admin) {
            echo '<tr data-id=' . $_SESSION['id'] . '>' .
                '<td>1</td>' .
                '<td>' . $_SESSION['name'] . '</td>' .
                '<td class="action">' .
                '<span class="hu-ui action-text modifypsd" data-action="modifypsd">修改密码</span>' .
                '</td>' .
                '</tr>';
            echo '<div class="ui mini modal" id="modifypsd">
                <div class="ui header">修改密码</div>
                <div class="content">
                    <div class="ui input">
                        <input id="psdInput" type="text" autocomplete="false" placeholder="输入密码">
                    </div>
                </div>
                <div class="actions">
                    <button class="ui button primary positive" id="ok">确定</button>
                    <button class="ui button primary deny" id="cancel">取消</button>
                </div>
            </div>';
            die();
        }

        $User = new UserManagment();
        $respondData = $User->GetAllUser();
        $index = 1;
        if (count($respondData) == 0) {
            echo "<tr><td colspan='6'><div class='info ui message'>数据库中无任何数据</div></td></tr>";
        } else {
            foreach ($respondData as $row) {
                ?>
                <tr data-id=<?php echo $row["id"] ?>>
                    <td><?php echo $index++ ?></td>
                    <td class="user"><?php echo $row['user'] ?></td>
                    <td class="tname"><?php echo $row['truename'] ?></td>
                    <td class="ctime"><?php echo $row['ctime'] ?></td>
                    <td class="auth">
                        <?php
                        $auth = json_decode($row['auth'], true);
                        $sceneControl = $auth['scene'] == "true" ? true : false;
                        $hotelControl = $auth['hotel'] == "true" ? true : false;
                        $stateControl = $auth['state'] == "true" ? true : false;
                        ?>
                        <div class="ui checkbox">
                            <input type="checkbox" data-type='scene' <?php echo $sceneControl ? 'checked' : '' ?>
                                   disabled>
                            <label>景点</label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" data-type='hotel' <?php echo $hotelControl ? 'checked' : '' ?>
                                   disabled>
                            <label>民宿</label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" data-type='state' <?php echo $stateControl ? ' checked' : '' ?>
                                   disabled>
                            <label>运行状态</label>
                        </div>
                    </td>
                    <td class="action user">
                        <span class='hu-ui action-text delete' data-action='delete'>删除</span>
                        <span class='hu-ui action-text modify' data-action='modify'>修改用户</span>
                        <span class='hu-ui action-text resetpsd' data-action='resetpsd'>重置密码</span>
                    </td>
                </tr>
            <?php }
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan=6>
                <div class='icon-btn-wrapper' id="adduser">
                    <i class='icon add user button'></i>
                </div>
            </th>
        </tr>
        </tfoot>
    </table>
<?php if (array_key_exists('admin', $_SESSION)) { ?>
    <div class="ui mini modal" id="successdlg">
        <div class="ui header">提示</div>
        <div class="content">操作成功</div>
        <div class="actions">
            <button class="ui button primary positive" id="ok">确定</button>
        </div>
    </div>
    <div class="ui mini modal" id="modify">
    </div>
    <div class="ui mini modal" id="failuredlg">
        <div class="ui header">提示</div>
        <div class="content">操作失败</div>
        <div class="actions">
            <button class="ui button primary positive" id="ok">确定</button>
        </div>
    </div>
    <div class="ui mini modal" id="adduserdlg">
        <div class="ui header">新建用户</div>
        <div class="content">
            <div class="ui input">
                <input id="name" type="text" autocomplete=false placeholder="输入登录名">
            </div>
            <div class="divider ui"></div>
            <div class="ui input">
                <input id="truename" type="text" autocomplete="false" placeholder="输入用户名">
            </div>
            <div class="ui divider"></div>
            <div class='auth'>
                <div class="ui checkbox">
                    <input type="checkbox" data-type='scene'>
                    <label>景点</label>
                </div>
                <div class="ui checkbox">
                    <input type="checkbox" data-type='hotel'>
                    <label>民宿</label>
                </div>
                <div class="ui checkbox">
                    <input type="checkbox" data-type='state'>
                    <label>运行状态</label>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui button primary positive" id="ok">确定</button>
            <button class="ui button primary deny" id="cancel">取消</button>
        </div>
    </div>
<?php } ?>