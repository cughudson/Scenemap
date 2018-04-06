<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/3
 * Time: 1:11
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
//include_once dirname(__FILE__,3)."/Helper/ChromePhp.php";
header("cache-control:no-cache");
include_once dirname(__FILE__, 3) . "/Control/Lib/ConfigManagment.php";
$isAdmin = array_key_exists("admin", $_SESSION) && $_SESSION["admin"];
$filePath = dirname(__FILE__, 3) . "/Config.ini";
$configData = (new ConfigManagment($filePath))->Parse();
$isAdmin = array_key_exists("admin", $_SESSION) && $_SESSION["admin"];
if (!$isAdmin) {
//
    ?>
    <div class='input-wrapper' style="padding: 36px 0 0 0">
        <div class='ui input'>
            <input id="city-input" type='text' placeholder='设置常用城市' value="<?php echo $_COOKIE['city'] ?>">
        </div>
        <div class='ui button primary' id="set-city" style="margin-left: 24px">确定</div>
    </div>
<?php } else {
    ?>
    <table class="ui collapsing celled table">
        <thead>
        <tr>
            <th colspan=2>
                系统信息配置
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div class="ui selection dropdown" style="width: 100%">
                    <input class='config' type="hidden" name="database" data-title='database'
                           value="<?php echo @$configData['database'] ?>">
                    <i class="dropdown icon"></i>
                    <div class="text"><?php echo @$configData['database'] ?></div>
                    <div class="menu">
                        <div class="item" data-value="mysql">mysql</div>
                        <div class="item" data-value="mssql">mssql</div>
                        <div class="item" data-value="oracle">oracle</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        数据库地址
                    </div>
                    <input class='config' type="text" placeholder="localhost" data-title='host'
                           value="<?php echo @$configData['host'] ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        端口
                    </div>
                    <input class='config' type=number placeholder="3306" data-title='port'
                           value="<?php echo @$configData['port'] ?>">
                </div>
            </td>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        用户名
                    </div>
                    <input class='config' type=text placeholder="数据库用户名" data-title='user'
                           value="<?php echo @$configData['user'] ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        密码
                    </div>
                    <input class='config' type=text placeholder="数据库密码" data-title='psd'
                           value="<?php echo @$configData['psd'] ?>">
                </div>
            </td>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        每页行数（后台）
                    </div>
                    <input class='config' type=number placeholder="15" data-title='rowPerPage'
                           value="<?php echo @$configData['rowPerPage'] ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        每次加载数目（后台）
                    </div>
                    <input class='config' type=number placeholder="15" data-title='itemNumEachLoad'
                           value="<?php echo @$configData['itemNumEachLoad'] ?>">
                </div>
            </td>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        推荐数目
                    </div>
                    <input class='config' type=number placeholder="15" data-title='recommandNum'
                           value="<?php echo @$configData['recommandNum'] ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        网址
                    </div>
                    <input class='config' type=text placeholder="输入系统网址" data-title='url'
                           value="<?php echo @$configData['url'] ?>">
                </div>
            </td>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        城市
                    </div>
                    <input class='config' type=text placeholder="设置城市" data-title='city'
                           value="<?php echo @$configData['city'] ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="ui labeled input" style="width: 100%">
                    <div class="ui label">
                        数据库名称
                    </div>
                    <input class='config' type=text placeholder="数据库名称" data-title='dbname'
                           value="<?php echo @$configData['dbname'] ?>">
                </div>
            </td>
            <td></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th colspan=2>
                <button class='ui primary button' id='save-config'>保存</button>
            </th>
        </tr>
        </tfoot>
    </table>
    <script type="application/ecmascript">
        $(function () {
            $('.ui.dropdown')
                .dropdown({
                    maxSelections: 3
                });
        })
    </script>
<?php } ?>

