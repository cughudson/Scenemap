<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/10
 * Time: 19:04
 */
header("Content-Type:text/html; charset=utf8");
include_once dirname(__FILE__, 3) . "/Control/Lib/HotelManagment.php";
$req_type = $_SERVER["REQUEST_METHOD"];
if ($req_type == 'GET') {
    $respond = array(
        'status' => false,
    );
    echo json_encode($respond);
    die();
}

try {
    $Scene = new SceneManagment();
} catch (Exception $ex) {
    echo "<script type='application/javascript'>alert('加载失败')</script>";
    die();
}
$state = $_POST['state'];
switch ($state) {
    case 'draft':
        $total = (int)$Hotel->GetTotalCount(1);
        break;
    case 'publish':
        $total = (int)$Hotel->GetTotalCount(0);
        break;
    case 'waste':
        $total = (int)$Hotel->GetTotalCount(2);
        break;
    default:
        $total = 0;
}
$rowPerPage = (int)$Hotel->configData['rowPerPage'];
$total = $Scene->GetTotalCount(0);
$maxPage = ceil($total / $rowPerPage);
?>
<tr>
    <th colspan="5">
        <div class="ui pagination menu clearfix">
            <?php if ($maxPage > 5) { ?>
                <a class="icon item" id="prevPage">
                    <i class="left chevron icon"></i>
                </a>
            <?php } ?>
            <?php foreach (range(1, $maxPage < 5 ? $maxPage : 5, 1) as $item) { ?>
                <a class="item" data-page=<?php echo $item; ?>><?php echo $item; ?></a>
            <?php } ?>
            <?php if ($maxPage > 5) { ?>
                <a class="icon item" id='nextPage'>
                    <i class="right chevron icon"></i>
                </a>
            <?php } ?>
        </div>
    </th>
</tr>
