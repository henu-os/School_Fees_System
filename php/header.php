
<body>
<div id="wrapper">
    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-default navbar-cls-top" role="navigation" style="margin-bottom:0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">School Dashboard</a>
        </div>
        <div class="header-right">
            <!-- Notifications -->
            <a href="fees.php" title="Fee Invoices" style="text-decoration:none;">
                <div class="notif-icon">
                    <i class="fa fa-bell"></i>
                    <span class="notif-badge">2</span>
                </div>
            </a>
            <a href="student.php" title="Students" style="text-decoration:none;">
                <div class="notif-icon">
                    <i class="fa fa-users"></i>
                    <span class="notif-badge" style="background:#f59e0b;">3</span>
                </div>
            </a>

            <!-- Admin Dropdown -->
            <div class="dropdown" style="position:relative;">
                <div class="admin-pill" id="adminDropdownToggle"
                     onclick="document.getElementById('adminDropdownMenu').classList.toggle('show')"
                     style="cursor:pointer; user-select:none;">
                    <div class="admin-avatar">
                        <?php echo isset($_SESSION['rainbow_name']) ? strtoupper(substr($_SESSION['rainbow_name'], 0, 1)) : 'A'; ?>
                    </div>
                    <span><?php echo isset($_SESSION['rainbow_name']) ? htmlspecialchars($_SESSION['rainbow_name']) : 'Admin'; ?></span>
                    <i class="fa fa-chevron-down" style="font-size:10px; color:#8a94a6;"></i>
                </div>
                <!-- Dropdown Menu -->
                <div id="adminDropdownMenu" style="
                    display:none; position:absolute; right:0; top:calc(100% + 8px);
                    background:#fff; border:1px solid #e8eaf0; border-radius:10px;
                    box-shadow:0 8px 30px rgba(0,0,0,.10); min-width:170px; z-index:9999; overflow:hidden;">
                    <div style="padding:12px 16px; border-bottom:1px solid #e8eaf0;">
                        <div style="font-size:13px; font-weight:700; color:#1a1f36;">
                            <?php echo isset($_SESSION['rainbow_name']) ? htmlspecialchars($_SESSION['rainbow_name']) : 'Admin'; ?>
                        </div>
                        <div style="font-size:11px; color:#8a94a6; margin-top:2px;">Administrator</div>
                    </div>
                    <a href="setting.php" style="display:flex; align-items:center; gap:10px; padding:10px 16px;
                        color:#1a1f36; font-size:13px; font-weight:500; text-decoration:none; transition:background .15s;"
                        onmouseover="this.style.background='#f0f2f7'" onmouseout="this.style.background='transparent'">
                        <i class="fa fa-gear" style="width:16px; color:#8a94a6;"></i> Settings
                    </a>
                    <a href="logout.php" style="display:flex; align-items:center; gap:10px; padding:10px 16px;
                        color:#ef4444; font-size:13px; font-weight:600; text-decoration:none; transition:background .15s;
                        border-top:1px solid #e8eaf0;"
                        onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='transparent'">
                        <i class="fa fa-right-from-bracket" style="width:16px;"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <!-- /. NAV TOP -->

    <!-- Close dropdown on outside click -->
    <script>
    document.addEventListener('click', function(e) {
        var toggle = document.getElementById('adminDropdownToggle');
        var menu   = document.getElementById('adminDropdownMenu');
        if (menu && toggle && !toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
            menu.classList.remove('show');
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('adminDropdownToggle');
        var menu   = document.getElementById('adminDropdownMenu');
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            });
        }
    });
    </script>

    <!-- SIDEBAR -->
    <nav class="navbar-default navbar-side" role="navigation">
        <!-- Brand: Henu Logo -->
        <div class="sidebar-brand-bar">
            <img src="img/HENU LOGO.svg" alt="Henu OS"
                 style="height:220px; width:auto; max-width:540px; object-fit:contain; display:block; margin:0 auto;" />
        </div>

        <!-- Nav — plain text list, no icons -->
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li>
                    <a class="active-menu" href="index.php">Dashboard</a>
                </li>
                <li>
                    <a href="fees.php">Fee Invoices</a>
                </li>
                <li>
                    <a href="student.php">Students</a>
                </li>
                <li>
                    <a href="fees.php">Payments</a>
                </li>
                <li>
                    <a href="report.php">Reports</a>
                </li>
                <li>
                    <a href="setting.php">Settings</a>
                </li>
                <li style="margin-top:12px; border-top:1px solid #e8eaf0; padding-top:12px;">
                    <a href="logout.php" style="color:#ef4444 !important; font-weight:600;">
                        <i class="fa fa-right-from-bracket" style="margin-right:6px;"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Footer: Henu Logo small -->
        <div class="sidebar-footer">
            <img src="img/HENU LOGO.svg" alt="Henu OS"
                 style="height:24px; width:auto; opacity:.55;" />
            <span style="margin-left:4px;">© 2025</span>
        </div>
    </nav>
    <!-- /. NAV SIDE -->