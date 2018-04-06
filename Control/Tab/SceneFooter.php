<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/10
 * Time: 19:03
 */
session_start(['cookie_lifetime' => 30 * 60]);
header("Content-Type:text/html; charset=utf8");
include_once dirname(__FILE__, 3) . "/Control/Lib/SceneManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
$req_type = $_SERVER["REQUEST_METHOD"];
if ($req_type == 'POST') {
    die();
}
$Scene = new SceneManagment();
$uuid = $_SESSION['uuid'];
$state = $_POST['state'];
switch ($state) {
    case 1:
        if ($uuid == 'admin') {
            $total = (int)$Scene->GetTotalCountByState(1);
        } else {
            $total = (int)$Scene->GetTotalCountByStateAndAuthor(1, $uuid);
        }
        break;
    case 0:
        if ($uuid == 'admin') {
            $total = (int)$Scene->GetTotalCountByState(0);
        } else {
            $total = (int)$Scene->GetTotalCountByStateAndAuthor(0, $uuid);
        }
        break;
    case 2:
        if ($uuid == 'admin') {
            $total = (int)$Scene->GetTotalCountByState(2);
        } else {
            $total = (int)$Scene->GetTotalCountByStateAndAuthor(2, $uuid);
        }
        break;
    default:
        $total = 0;
}

$rowPerPage = (int)$Scene->configData['rowPerPage'];
$maxPage = ceil($total / $rowPerPage);
if ($maxPage == 0) $maxPage = 1;
?>
<tr data-currentpage="1" data-maxpage=<?php echo $maxPage ?> data-state=<?php echo $state ?>>
    <th colspan="6">
        <?php if ($maxPage == 1) {
            echo "共1页";
            echo "</th>";
            echo "</tr>";
            die();
        }
        ?>
        <div class="ui pagination menu clearfix">
            <?php if ($maxPage > 5) { ?>
                <a class="icon item" id="prevPage">
                    <i class="left chevron icon"></i>
                </a>
            <?php } ?>
            <?php foreach (range(1, $maxPage < 5 ? $maxPage : 5, 1) as $item) { ?>
                <a class="item <?php if ($item == 1) {
                    echo " active";
                } ?>" data-page=<?php echo $item; ?>><?php echo $item; ?></a>
            <?php } ?>
            <?php if ($maxPage > 5) { ?>
                <a class="icon item" id='nextPage'>
                    <i class="right chevron icon"></i>
                </a>
            <?php } ?>
        </div>
        &nbsp;
        <?php echo "共" . $maxPage . "页"; ?>
    </th>
</tr>

