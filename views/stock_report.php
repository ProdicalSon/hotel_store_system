<?php
// views/stock_report.php
require_once __DIR__ . '/../controllers/stock_controller.php';

$products = get_products();

$start = $_GET['start'] ?? date('Y-m-d'); // start date for the report (for day or week)
$end = $_GET['end'] ?? date('Y-m-d');     // end date
$dept = $_GET['department'] ?? '';       // optional department filter
$report_type = $_GET['type'] ?? 'daily'; // 'daily' or 'weekly' or 'range'

if($report_type === 'daily') {
    $start = norm_date($start);
    $end = $start;
} elseif($report_type === 'weekly') {
    // if only start provided, calculate 7-day period from start
    $start = norm_date($start);
    $end = date('Y-m-d', strtotime($start . ' +6 days'));
} else {
    $start = norm_date($start);
    $end = norm_date($end);
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Stock Reports</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<div class="container">
  <h2>Stock Reports</h2>

  <form method="get">
    <label>Type:
      <select name="type">
        <option value="daily" <?= $report_type==='daily'?'selected':'' ?>>Daily</option>
        <option value="weekly" <?= $report_type==='weekly'?'selected':'' ?>>Weekly</option>
        <option value="range" <?= $report_type==='range'?'selected':'' ?>>Custom Range</option>
      </select>
    </label>
    <label>Start: <input type="date" name="start" value="<?=htmlspecialchars($start)?>"></label>
    <label>End: <input type="date" name="end" value="<?=htmlspecialchars($end)?>"></label>
    <label>Department (optional): <input name="department" value="<?=htmlspecialchars($dept)?>"></label>
    <button type="submit">View</button>
  </form>

  <h3>Report: <?=htmlspecialchars($start)?> to <?=htmlspecialchars($end)?> <?= $dept ? " - Dept: ".htmlspecialchars($dept) : '' ?></h3>

  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Opening Stock</th>
        <th>Purchased</th>
        <th>Issued</th>
        <th>Closing Stock</th>
        <th>Unit Price</th>
        <th>Closing Stock Value</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($products as $p): 
        $pid = $p['id'];
        $open = opening_stock($pid, $start);
        $purchased = purchased_between($pid, $start, $end);
        $issued = issued_between($pid, $start, $end, $dept ?: null);
        $close = $open + $purchased - $issued;
        $value = $close * floatval($p['unit_price']);
      ?>
      <tr>
        <td><?=htmlspecialchars($p['name'])?></td>
        <td><?=$open?></td>
        <td><?=$purchased?></td>
        <td><?=$issued?></td>
        <td><?=$close?></td>
        <td><?=number_format($p['unit_price'],2)?></td>
        <td><?=number_format($value,2)?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p><a href="../index.php">Back</a></p>
</div>
</body></html>
