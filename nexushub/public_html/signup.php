<<<<<<< HEAD
<?php include 'cdn.php'; include 'config.php';

if(isset($_POST['email'])) {
  $pwd = filter($_POST['pwd']); $rpwd = filter($_POST['rpwd']);
  $match = ''; $len = '';

  if($pwd !== $rpwd) { $matchError = true; }
  else { $match = 'fixed'; }

  if(strlen($pwd) < 8) { $lenError = true; }
  else { $len = 'fixed'; }

  if($match == 'fixed' && $len == 'fixed') {
    $m = $_POST['email'];
    $u = $_POST['username'];
    $chk = $conn->query("select * from user_details where uemail='$m'");
    if($chk->num_rows > 0) { $emailError = true; }
    else {
      $put = $conn->query("insert into user_details(uname,uemail,upwd) values('$u','$m','$pwd')");
      if($put) { $success = true; }
      $conn->query("insert into bal(uid,bal) values('$conn->insert_id','0.00')");
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Wallet — VertexxHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg: #0b0d11;
      --surface: #13161d;
      --surface2: #1a1e28;
      --border: rgba(255,255,255,0.07);
      --accent: #f0a500;
      --accent-dim: rgba(240,165,0,0.12);
      --accent-glow: rgba(240,165,0,0.25);
      --text: #e8eaf0;
      --muted: #6b7280;
      --error-bg: rgba(239,68,68,0.08);
      --error-border: rgba(239,68,68,0.3);
      --error-text: #f87171;
      --success-bg: rgba(34,197,94,0.08);
      --success-border: rgba(34,197,94,0.3);
      --success-text: #4ade80;
    }

    html, body {
      min-height: 100%;
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-weight: 400;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem 1rem;
      position: relative;
      overflow-x: hidden;
    }

    /* Background grid */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background-image:
        linear-gradient(rgba(240,165,0,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(240,165,0,0.03) 1px, transparent 1px);
      background-size: 48px 48px;
      pointer-events: none;
      z-index: 0;
    }

    /* Glow orb */
    body::after {
      content: '';
      position: fixed;
      top: -200px; right: -200px;
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(240,165,0,0.06) 0%, transparent 65%);
      pointer-events: none;
      z-index: 0;
    }

    .page-wrapper {
      width: 100%;
      max-width: 480px;
      position: relative;
      z-index: 1;
    }

    /* Brand header */
    .brand {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .brand-logo {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 52px; height: 52px;
      background: var(--accent-dim);
      border: 1px solid rgba(240,165,0,0.2);
      border-radius: 14px;
      margin-bottom: 1rem;
    }

    .brand-logo svg { width: 24px; height: 24px; }

    .brand h1 {
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      color: var(--text);
    }

    .brand p {
      font-size: 0.875rem;
      color: var(--muted);
      margin-top: 0.35rem;
      font-weight: 300;
    }

    /* Card */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 2.5rem 2rem;
      box-shadow:
        0 0 0 1px rgba(255,255,255,0.02),
        0 32px 64px rgba(0,0,0,0.5);
      animation: slideUp 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Alerts */
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 0.65rem;
      padding: 0.85rem 1rem;
      border-radius: 10px;
      font-size: 0.875rem;
      line-height: 1.5;
      margin-bottom: 1.5rem;
    }

    .alert-error {
      background: var(--error-bg);
      border: 1px solid var(--error-border);
      color: var(--error-text);
    }

    .alert-success {
      background: var(--success-bg);
      border: 1px solid var(--success-border);
      color: var(--success-text);
    }

    .alert-icon {
      flex-shrink: 0;
      margin-top: 1px;
    }

    .alert a { color: inherit; font-weight: 500; }

    /* Form */
    .form-group { margin-bottom: 1.35rem; }

    .form-label {
      display: block;
      font-size: 0.8rem;
      font-weight: 500;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 0.5rem;
    }

    .input-wrap {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%; transform: translateY(-50%);
      color: var(--muted);
      pointer-events: none;
      transition: color 0.2s;
    }

    .form-input {
      width: 100%;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 10px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      padding: 0.85rem 1rem 0.85rem 2.75rem;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      -webkit-appearance: none;
    }

    .form-input::placeholder { color: var(--muted); }

    .form-input:focus {
      border-color: rgba(240,165,0,0.4);
      background: rgba(240,165,0,0.03);
      box-shadow: 0 0 0 3px var(--accent-glow);
    }

    /* Submit */
    .btn-submit {
      width: 100%;
      padding: 0.9rem 1rem;
      background: var(--accent);
      color: #0b0d11;
      font-family: 'Syne', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      letter-spacing: 0.03em;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
      margin-top: 0.5rem;
      box-shadow: 0 4px 24px rgba(240,165,0,0.2);
    }

    .btn-submit:hover {
      opacity: 0.92;
      transform: translateY(-1px);
      box-shadow: 0 8px 32px rgba(240,165,0,0.3);
    }

    .btn-submit:active { transform: translateY(0); }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin: 1.75rem 0 1.5rem;
      color: var(--muted);
      font-size: 0.8rem;
    }

    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    /* Footer link */
    .auth-footer {
      text-align: center;
      font-size: 0.875rem;
      color: var(--muted);
    }

    .auth-footer a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 500;
      transition: opacity 0.15s;
    }

    .auth-footer a:hover { opacity: 0.8; }

    /* Responsive */
    @media (max-width: 520px) {
      .card { padding: 2rem 1.25rem; }
    }
  </style>
</head>
<body>
<div class="page-wrapper">

  <div class="brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="#f0a500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
    </div>
    <h1>VertexxHub</h1>
    <p>Web3 Wallet Platform</p>
  </div>

  <div class="card">

    <?php if (!empty($matchError)): ?>
    <div class="alert alert-error">
      <svg class="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Passwords do not match. Please try again.
    </div>
    <?php endif; ?>

    <?php if (!empty($lenError)): ?>
    <div class="alert alert-error">
      <svg class="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Password must be at least 8 characters.
    </div>
    <?php endif; ?>

    <?php if (!empty($emailError)): ?>
    <div class="alert alert-error">
      <svg class="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      This email is already registered. <a href="/signin/auth">Sign in instead.</a>
    </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
    <div class="alert alert-success">
      <svg class="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      Account created successfully. Redirecting&hellip;
    </div>
    <script>window.location.href = '/signin/success';</script>
    <?php endif; ?>

    <form method="post" action="/signup" novalidate>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="2,4 12,13 22,4"/></svg>
          <input class="form-input" type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <input class="form-input" type="text" id="username" name="username" placeholder="Choose a username" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="pwd">Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input class="form-input" type="password" id="pwd" name="pwd" placeholder="Min. 8 characters" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="rpwd">Confirm Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input class="form-input" type="password" id="rpwd" name="rpwd" placeholder="Re-enter your password" required>
        </div>
      </div>

      <button type="submit" class="btn-submit">Create Wallet</button>

    </form>

    <div class="divider">or</div>

    <p class="auth-footer">Already have an account? <a href="/signin/auth">Sign in</a></p>

  </div>
</div>
</body>
</html>
=======
<?php
include 'cdn.php';
include 'config.php';

$message = '';
$message_type = '';

if(isset($_POST['email'])) {
    $pwd = filter($_POST['pwd']);
    $rpwd = filter($_POST['rpwd']);
    $match = false;
    $len = false;

    if($pwd !== $rpwd) {
        $message = 'Passwords do not match. Try again!';
        $message_type = 'danger';
    } else {
        $match = true;
    }

    if(strlen($pwd) < 8) {
        $message = 'Passwords must be at least 8 characters. Try again!';
        $message_type = 'danger';
    } else {
        $len = true;
    }

    if($match && $len) {
        $m = filter($_POST['email']);
        $u = filter($_POST['username']);
        $chk = $conn->query("select * from user_details where uemail ='$m'");
        if($chk->num_rows > 0) {
            $message = 'Error! Email already in use. If you have an account, <a href="/signin/auth" class="underline">Login.</a>';
            $message_type = 'danger';
        } else {
            $put = $conn->query("insert into user_details(uname,uemail,upwd) values('$u','$m','$pwd')");
            if($put) {
                $uid = $conn->insert_id;
                $conn->query("insert into bal(uid,bal) values('$uid','0.00')");
                ?>
                <script>window.location.href = '/signin/success';</script>
                <?php
                exit;
            }
        }
    }
}
?>

<div class="min-h-screen flex items-center justify-center bg-[#050505] px-4 py-12">
    <!-- Background Glow -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-orange-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -left-[10%] w-[40%] h-[40%] bg-amber-500/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-12 h-12 bg-gradient-gold rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-orange-500/20">
                <i class="fas fa-bank text-black text-xl"></i>
            </div>
            <h1 class="font-syne text-2xl font-800 gradient-text">vertexxHub</h1>
            <p class="text-gray-400 text-sm mt-1">Professional Trading Platform</p>
        </div>

        <!-- Card -->
        <div class="card-dark rounded-3xl p-8 border border-white/10 shadow-2xl">
            <div class="text-center mb-8">
                <h2 class="font-syne text-2xl font-700 text-white">Create Wallet</h2>
                <p class="text-gray-400 text-sm mt-2">Join 1.2M+ traders worldwide</p>
            </div>

            <?php if($message): ?>
                <div class="mb-6 p-4 rounded-xl text-sm font-medium <?php echo $message_type == 'success' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'; ?>">
                    <div class="flex items-start gap-3">
                        <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mt-0.5"></i>
                        <span><?php echo $message; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" action="/signup" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="text" name="username" placeholder="johndoe" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-11 pr-4 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" placeholder="name@example.com" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-11 pr-4 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" name="pwd" placeholder="Min. 8 characters" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-11 pr-4 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <input type="password" name="rpwd" placeholder="Repeat password" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-11 pr-4 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-gradient-gold text-black font-800 py-4 rounded-xl shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 font-syne uppercase tracking-wider">
                        Create Account <i class="fas fa-arrow-right text-sm"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-400 text-sm">
                    Already have an account?
                    <a href="/signin/auth" class="text-primary font-700 hover:text-amber-400 transition ml-1">Sign In</a>
                </p>
            </div>
        </div>

        <p class="mt-8 text-center text-[11px] text-gray-600 leading-relaxed max-w-xs mx-auto">
            By creating an account, you agree to vertexxHub's <a href="#" class="underline">Terms of Service</a> and <a href="#" class="underline">Privacy Policy</a>.
        </p>
    </div>
</div>
>>>>>>> 12268fcf1fa663a3fe6a84e234f859567e0df2f3
