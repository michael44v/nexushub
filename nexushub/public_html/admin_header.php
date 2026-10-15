<?php
if (!isset($conn)) {
    include 'config.php';
}
if (!isset($_SESSION)) {
    @session_start();
}

$currentUri = $_SERVER['REQUEST_URI'];

// Fetch counts for badges
$pendingWithdrawalsCount = 0;
$wRes = $conn->query("SELECT COUNT(*) as cnt FROM withdrawals WHERE stat='Pending'");
if ($wRes && $row = $wRes->fetch_assoc()) {
    $pendingWithdrawalsCount = $row['cnt'];
}

$pendingKycCount = 0;
$kRes = $conn->query("SELECT COUNT(*) as cnt FROM user_details WHERE kyc_status='Pending Approval'");
if ($kRes && $row = $kRes->fetch_assoc()) {
    $pendingKycCount = $row['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexusHub Admin Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #07080a;
            color: #e8eaf0;
            font-family: 'DM Sans', sans-serif;
        }
        .font-syne { font-family: 'Syne', sans-serif; }
        .gradient-gold-text {
            background: linear-gradient(135deg, #f0a500 0%, #ffc857 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-gold {
            background: linear-gradient(135deg, #f0a500 0%, #d48800 100%);
        }
        .admin-card {
            background: #11141c;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .admin-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.15rem;
            border-radius: 0.85rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            color: #808a9d;
            text-decoration: none;
        }
        .admin-nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
        }
        .admin-nav-item.active {
            background: linear-gradient(135deg, #f0a500 0%, #d48800 100%);
            color: #07080a;
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(240,165,0,0.25);
        }
        .badge-count {
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 800;
        }
    </style>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">
    <!-- Unified Admin Sidebar -->
    <aside class="w-72 bg-[#0c0e14] border-r border-white/5 fixed inset-y-0 left-0 z-40 flex flex-col hidden lg:flex">
        <!-- Brand Header -->
        <div class="p-6 border-b border-white/5 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-gold rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20 text-black font-800 text-lg">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <h1 class="font-syne font-800 text-lg text-white leading-tight">NexusHub</h1>
                <span class="text-[10px] uppercase tracking-widest text-amber-400 font-700">Admin Control</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <p class="px-3 text-[10px] font-800 uppercase tracking-widest text-gray-500 mb-2">Main Controls</p>

            <!-- 1. Overview / Dashboard -->
            <a href="/admin/secured/@root#hash" class="admin-nav-item <?php echo (strpos($currentUri, '/admin/secured') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span>Overview</span>
                </div>
            </a>

            <!-- 2. User Management -->
            <a href="/user_management" class="admin-nav-item <?php echo (strpos($currentUri, '/user_management') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-users-gear w-5 text-center"></i>
                    <span>User Management</span>
                </div>
            </a>

            <!-- 3. Deposits & Withdrawals -->
            <a href="/admin_deposits/new" class="admin-nav-item <?php echo (strpos($currentUri, '/admin_deposits') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-money-bill-transfer w-5 text-center"></i>
                    <span>Deposits & Withdrawals</span>
                </div>
                <?php if ($pendingWithdrawalsCount > 0): ?>
                    <span class="badge-count bg-red-500 text-white"><?php echo $pendingWithdrawalsCount; ?></span>
                <?php endif; ?>
            </a>

            <!-- 4. Special Payments -->
            <a href="/special_payments" class="admin-nav-item <?php echo (strpos($currentUri, '/special_payments') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-receipt w-5 text-center"></i>
                    <span>Special Payments</span>
                </div>
            </a>

            <!-- 5. Profits -->
            <a href="/profits" class="admin-nav-item <?php echo (strpos($currentUri, '/profits') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span>Profits & ROI</span>
                </div>
            </a>

            <!-- 6. Messenger -->
            <a href="/messenger" class="admin-nav-item <?php echo (strpos($currentUri, '/messenger') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-comments w-5 text-center"></i>
                    <span>Messenger Support</span>
                </div>
            </a>

            <!-- 7. KYC Requests -->
            <a href="/admin_kyc" class="admin-nav-item <?php echo (strpos($currentUri, '/admin_kyc') !== false) ? 'active' : ''; ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-id-card w-5 text-center"></i>
                    <span>KYC Requests</span>
                </div>
                <?php if ($pendingKycCount > 0): ?>
                    <span class="badge-count bg-amber-500 text-black"><?php echo $pendingKycCount; ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-white/5">
            <a href="/signin/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-700 text-red-400 hover:bg-red-500/10 transition-all">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Logout Admin</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 lg:ml-72 min-h-screen flex flex-col">
        <!-- Top Admin Header -->
        <header class="h-20 bg-[#0c0e14]/80 backdrop-blur-md border-b border-white/5 px-6 lg:px-10 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button id="mobileAdminMenuBtn" class="lg:hidden text-gray-400 hover:text-white text-xl">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="font-syne font-800 text-xl text-white">
                    <?php
                    if (strpos($currentUri, '/user_management') !== false) echo "User Management";
                    elseif (strpos($currentUri, '/admin_deposits') !== false) echo "Deposits & Withdrawals";
                    elseif (strpos($currentUri, '/special_payments') !== false) echo "Special Crypto Payments";
                    elseif (strpos($currentUri, '/profits') !== false) echo "Profits & ROI Settings";
                    elseif (strpos($currentUri, '/messenger') !== false) echo "Live Messenger";
                    elseif (strpos($currentUri, '/admin_kyc') !== false) echo "KYC Document Verification";
                    else echo "Admin Dashboard";
                    ?>
                </h2>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden sm:flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-full text-emerald-400 text-xs font-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Admin Active
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-gold text-black flex items-center justify-center font-800 font-syne text-sm">
                        AD
                    </div>
                    <div class="hidden md:block">
                        <p class="text-xs font-700 text-white">Administrator</p>
                        <p class="text-[10px] text-gray-400">Super Access</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body Wrapper -->
        <main class="flex-1 p-6 lg:p-10">
