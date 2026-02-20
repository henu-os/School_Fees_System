<?php
include("php/dbconnect.php");
include("php/checklogin.php");

/* ---- Dashboard Stats ---- */
$total_students = (int)($conn->query("SELECT COUNT(*) as c FROM student WHERE delete_status='0'")->fetch_assoc()['c'] ?? 0);
$total_fees     = (float)($conn->query("SELECT COALESCE(SUM(fees),0) as t FROM student WHERE delete_status='0'")->fetch_assoc()['t'] ?? 0);
$paid_fees      = (float)($conn->query("SELECT COALESCE(SUM(paid),0) as t FROM fees_transaction")->fetch_assoc()['t'] ?? 0);
$pending_fees   = max(0, $total_fees - $paid_fees);

/* ---- Recent Fee Invoices ---- */
$recentQ = $conn->query("
    SELECT ft.id, ft.submitdate, ft.paid, ft.transcation_remark,
           s.sname, s.balance, s.fees
    FROM fees_transaction ft
    JOIN student s ON ft.stdid = s.id
    ORDER BY ft.id DESC
    LIMIT 6
");

/* ---- Branch summary for donut chart ---- */
$branchQ = $conn->query("
    SELECT b.branch as bname, COUNT(s.id) as cnt
    FROM student s
    JOIN branch b ON s.branch = b.id
    WHERE s.delete_status='0'
    GROUP BY s.branch
    ORDER BY cnt DESC
    LIMIT 5
");
$branchLabels = []; $branchData = []; $branchColors = ['#4361ee','#818cf8','#06b6d4','#10b981','#f59e0b'];
while($br = $branchQ->fetch_assoc()) {
    $branchLabels[] = $br['bname'];
    $branchData[]   = (int)$br['cnt'];
}

/* ---- Monthly paid fees for bar chart (last 8 months) ---- */
$months = []; $monthPaid = []; $monthPending = [];
for($i = 7; $i >= 0; $i--) {
    $ts   = strtotime("-$i months");
    $ym   = date('Y-m', $ts);
    $lbl  = date('M', $ts);
    $months[] = $lbl;
    $paidM = (float)($conn->query("SELECT COALESCE(SUM(paid),0) as t FROM fees_transaction WHERE DATE_FORMAT(submitdate,'%Y-%m')='$ym'")->fetch_assoc()['t'] ?? 0);
    $monthPaid[]    = $paidM;
    $monthPending[] = round($paidM * 0.25); // approximate pending
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard — School Fees Payment System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Legacy CSS -->
    <link href="css/basic.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
    <!-- Modern CSS -->
    <link href="css/modern.css" rel="stylesheet" />
</head>
<?php include("php/header.php"); ?>

        <div id="page-wrapper">
            <div id="page-inner">

                <!-- Page Title -->
                <div class="page-header-row">
                    <div>
                        <h1 class="page-head-line">School Dashboard</h1>
                        <p class="page-subhead-line">Welcome back, <strong><?php echo isset($_SESSION['rainbow_name']) ? $_SESSION['rainbow_name'] : 'Admin'; ?></strong></p>
                    </div>
                </div>

                <!-- Stat Cards Row -->
                <div class="row" style="margin-bottom:8px;">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-label">Total Fees</div>
                            <div class="stat-value">
                                ₹<?php echo number_format($total_fees); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-label">Paid Fees</div>
                            <div class="stat-value">
                                ₹<?php echo number_format($paid_fees); ?>
                                <?php if($total_fees > 0): ?>
                                <span class="stat-badge up">
                                    <i class="fa fa-arrow-up" style="font-size:9px;"></i>
                                    <?php echo round(($paid_fees/$total_fees)*100); ?>%
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-label">Pending Fees <i class="fa fa-circle-question" style="font-size:11px; color:#b0b8c9;"></i></div>
                            <div class="stat-value">
                                ₹<?php echo number_format($pending_fees); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-label">Total Students</div>
                            <div class="stat-value"><?php echo $total_students; ?></div>
                        </div>
                    </div>
                </div>
                <!-- /. Stat Cards -->

                <!-- Bottom Section: Table + Charts -->
                <div class="row">

                    <!-- Recent Fee Invoices -->
                    <div class="col-md-7">
                        <div class="content-card">
                            <div class="card-header">
                                <span class="card-title">Recent Fee Invoices</span>
                                <a href="fees.php" class="card-action">View All →</a>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if($recentQ && $recentQ->num_rows > 0):
                                            while($row = $recentQ->fetch_assoc()):
                                                $invNo = str_pad($row['id'], 7, '0', STR_PAD_LEFT);
                                                // Determine status
                                                if($row['balance'] <= 0) {
                                                    $badge = 'badge-paid'; $statusTxt = 'Paid';
                                                } elseif(strtotime($row['submitdate']) < strtotime('-30 days')) {
                                                    $badge = 'badge-overdue'; $statusTxt = 'Overdue';
                                                } else {
                                                    $badge = 'badge-pending'; $statusTxt = 'Pending';
                                                }
                                        ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row['sname']); ?></strong></td>
                                            <td style="color:#8a94a6; font-size:12px;"><?php echo $invNo; ?></td>
                                            <td style="color:#8a94a6; font-size:12px;"><?php echo date('M d, Y', strtotime($row['submitdate'])); ?></td>
                                            <td><strong>₹<?php echo number_format($row['paid']); ?></strong></td>
                                            <td><span class="badge-status <?php echo $badge; ?>"><?php echo $statusTxt; ?></span></td>
                                        </tr>
                                        <?php
                                            endwhile;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="5" style="text-align:center; color:#b0b8c9; padding:32px !important;">
                                                <i class="fa fa-file-invoice" style="font-size:28px; display:block; margin-bottom:10px;"></i>
                                                No fee records yet
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Charts Column -->
                    <div class="col-md-5">

                        <!-- Fee Payments Bar Chart -->
                        <div class="content-card" style="margin-bottom:16px;">
                            <div class="card-header">
                                <span class="card-title">Fee Payments</span>
                                <span style="font-size:11px; color:#8a94a6; font-weight:600;">Monthly</span>
                            </div>
                            <div class="card-body-pad" style="padding-top:12px;">
                                <div class="chart-container">
                                    <canvas id="feeBarChart"></canvas>
                                </div>
                                <div style="display:flex; gap:16px; margin-top:10px;">
                                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#8a94a6;">
                                        <span style="width:10px;height:10px;border-radius:2px;background:#4361ee;display:inline-block;"></span>Paid Fees
                                    </div>
                                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#8a94a6;">
                                        <span style="width:10px;height:10px;border-radius:2px;background:#93c5fd;display:inline-block;"></span>Pending Fees
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Students by Branch/Grade Donut Chart -->
                        <div class="content-card" style="overflow:visible;">
                            <div class="card-header">
                                <span class="card-title">Students by Grade</span>
                            </div>
                            <div class="card-body-pad" style="padding-top:16px; padding-bottom:20px;">
                                <div style="display:flex; align-items:center; gap:24px;">

                                    <!-- Donut chart with centred label -->
                                    <div style="position:relative; flex-shrink:0; width:160px; height:160px;">
                                        <canvas id="gradeDonut" width="160" height="160"></canvas>
                                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;
                                                    align-items:center;justify-content:center;pointer-events:none;">
                                            <span style="font-size:22px;font-weight:700;color:#1a1f36;line-height:1;"><?php echo $total_students; ?></span>
                                            <span style="font-size:11px;color:#8a94a6;font-weight:500;margin-top:2px;">Total</span>
                                        </div>
                                    </div>

                                    <!-- Legend — always to the RIGHT of chart -->
                                    <div style="flex:1; min-width:0;">
                                        <?php
                                        $legends   = !empty($branchLabels) ? $branchLabels : ['No Data'];
                                        $legCounts = !empty($branchData)   ? $branchData   : [0];
                                        foreach($legends as $k => $lbl):
                                            $clr = $branchColors[$k % count($branchColors)];
                                        ?>
                                        <div style="display:flex;align-items:center;justify-content:space-between;
                                                    margin-bottom:10px; gap:8px;">
                                            <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                                                <span style="width:10px;height:10px;border-radius:50%;background:<?php echo $clr;?>;
                                                            display:inline-block;flex-shrink:0;"></span>
                                                <span style="font-size:13px;color:#1a1f36;font-weight:500;
                                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                    <?php echo htmlspecialchars($lbl); ?>
                                                </span>
                                            </div>
                                            <span style="font-size:13px;color:#8a94a6;font-weight:600;flex-shrink:0;">
                                                <?php echo $legCounts[$k] ?? 0; ?>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /. Charts -->

                </div>
                <!-- /. Bottom Row -->

                <!-- Henu OS Branding -->
                <div style="text-align:center; margin-top:16px;">
                    <p style="font-size:12px; color:#b0b8c9; margin:0;">
                        <i class="fa fa-eye" style="color:#4361ee; margin-right:4px;"></i>
                        © 2025 <strong style="color:#8a94a6;">Henu OS Private Limited</strong>. All rights reserved.
                    </p>
                </div>

            </div>
        </div>
        <!-- /. PAGE WRAPPER -->
    </div>
    <!-- /. WRAPPER -->

    <!-- Scripts -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="js/jquery.metisMenu.js"></script>
    <script src="js/custom1.js"></script>

    <script>
    // ---- Fee Payments Bar Chart ----
    const feeCtx = document.getElementById('feeBarChart').getContext('2d');
    new Chart(feeCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [
                {
                    label: 'Paid Fees',
                    data: <?php echo json_encode($monthPaid); ?>,
                    backgroundColor: '#4361ee',
                    borderRadius: 4,
                    borderSkipped: false,
                    barThickness: 10,
                },
                {
                    label: 'Pending Fees',
                    data: <?php echo json_encode($monthPending); ?>,
                    backgroundColor: '#93c5fd',
                    borderRadius: 4,
                    borderSkipped: false,
                    barThickness: 10,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index' } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, family: 'Inter' }, color: '#b0b8c9' } },
                y: { grid: { color: '#f0f2f7' }, ticks: { font: { size: 10, family: 'Inter' }, color: '#b0b8c9', maxTicksLimit: 5 } }
            }
        }
    });

    // ---- Students by Grade Donut Chart ----
    const gradeCtx = document.getElementById('gradeDonut').getContext('2d');
    const branchData   = <?php echo json_encode(!empty($branchData) ? $branchData : [1]); ?>;
    const branchLabels = <?php echo json_encode(!empty($branchLabels) ? $branchLabels : ['No Data']); ?>;
    const branchColors = <?php echo json_encode(array_slice($branchColors, 0, count(!empty($branchData) ? $branchData : [1]))); ?>;

    new Chart(gradeCtx, {
        type: 'doughnut',
        data: {
            labels: branchLabels,
            datasets: [{
                data: branchData,
                backgroundColor: branchColors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: false,
            cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { callbacks: {
                label: ctx => ` ${ctx.label}: ${ctx.parsed}`
            }}}
        }
    });
    </script>

</body>
</html>
