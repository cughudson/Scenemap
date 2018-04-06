<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/23
 * Time: 11:07
 */

session_start(['cookie_lifetime' => 30 * 60 * 24]);
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
        die();
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    die();
}
include_once dirname(__FILE__, 3) . "/Control/Lib/UserManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
$id = $_GET["id"];
$User = new UserManagment();
$isExist = $User->IsIdExist($id);
if (!$isExist) {
    echo "<div class='ui info message'>用户不存在</div>";
    die();
} else {
    $row = $User->GetUserById($id);
    $data = $row[0];
}
?>
<div class="ui header">修改用户</div>
<div class="content">
    <div class="ui input">
        <input id="name" type="text" autocomplete=false placeholder="输入登录名" value="<?php echo $data['user'] ?>">
    </div>
    <div class="divider ui"></div>
    <div class="ui input">
        <input id="truename" type="text" autocomplete="false" placeholder="输入用户名"
               value="<?php echo $data['truename'] ?>">
    </div>
    <div class="ui divider"></div>
    <div class='auth'>
        <?php
        $auth = json_decode($data['auth'], true);
        $sceneControl = $auth['scene'] == "true" ? true : false;
        $hotelControl = $auth['hotel'] == "true" ? true : false;
        $stateControl = $auth['state'] == "true" ? true : false;
        ?>
        <div class="ui checkbox">
            <input type="checkbox" data-type='scene' <?php echo $sceneControl ? 'checked' : '' ?>>
            <label>景点</label>
        </div>
        <div class="ui checkbox">
            <input type="checkbox" data-type='hotel' <?php echo $hotelControl ? 'checked' : '' ?>>
            <label>民宿</label>
        </div>
        <div class="ui checkbox">
            <input type="checkbox" data-type='state' <?php echo $stateControl ? ' checked' : '' ?>>
            <label>运行状态</label>
        </div>
    </div>
</div>
<div class="actions">
    <button class="ui button primary positive" id="ok">确定</button>
    <button class="ui button primary deny" id="cancel">取消</button>
</div>