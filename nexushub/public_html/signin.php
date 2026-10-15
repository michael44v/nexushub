<?php
session_start();
include 'cdn.php'; include 'config.php';

$alertType = '';
$alertMsg  = '';

switch($req) {
  case 'success':
    $alertType = 'success';
    $alertMsg  = 'Registration successful. Sign in to access your wallet.';
    break;
  case 'logout':
    $_SESSION = array();
    session_destroy();
    $alertType = 'error';
    $alertMsg  = 'You have been signed out.';
    break;
}

if(isset($_POST['email'])) {
  $m   = filter($_POST['email']);
  $pwd = filter($_POST['pwd']);
  $log = $conn->query("select * from user_details where uemail='$m' and upwd='$pwd'");

  if($log->num_rows > 0) {
    while($loop = $log->fetch_assoc()) {
      $_SESSION['uid'] = $loop['uid'];
      $identifier = substr($loop['uname'], 0, 3);
      $redirect = $identifier . $_SESSION['uid'];
    }
  } else {
    $alertType = 'error';
    $alertMsg  = 'Incorrect email or password. Please try again.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In — NexusHub</title>
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

    body::after {
      content: '';
      position: fixed;
      bottom: -200px; left: -200px;
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(240,165,0,0.05) 0%, transparent 65%);
      pointer-events: none;
      z-index: 0;
    }

    .page-wrapper {
      width: 100%;
      max-width: 440px;
      position: relative;
      z-index: 1;
    }

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

    .brand h1 {
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      letter-spacing: -0.02em;
    }

    .brand p {
      font-size: 0.875rem;
      color: var(--muted);
      margin-top: 0.35rem;
      font-weight: 300;
    }

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

    .card-heading {
      font-family: 'Syne', sans-serif;
      font-size: 1.35rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 0.35rem;
    }

    .card-sub {
      font-size: 0.875rem;
      color: var(--muted);
      margin-bottom: 1.75rem;
      font-weight: 300;
    }

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

    .alert-icon { flex-shrink: 0; margin-top: 1px; }

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

    .input-wrap { position: relative; }

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

    .forgot-link {
      display: block;
      text-align: right;
      font-size: 0.8rem;
      color: var(--muted);
      text-decoration: none;
      margin-top: -0.9rem;
      margin-bottom: 1.35rem;
      transition: color 0.15s;
    }

    .forgot-link:hover { color: var(--accent); }

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
      box-shadow: 0 4px 24px rgba(240,165,0,0.2);
    }

    .btn-submit:hover {
      opacity: 0.92;
      transform: translateY(-1px);
      box-shadow: 0 8px 32px rgba(240,165,0,0.3);
    }

    .btn-submit:active { transform: translateY(0); }

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

    @media (max-width: 480px) {
      .card { padding: 2rem 1.25rem; }
    }
  </style>
</head>
<body>

<?php if (!empty($redirect)): ?>
<script>window.location.href = '/dashboard/<?php echo htmlspecialchars($redirect); ?>';</script>
<?php endif; ?>

<div class="page-wrapper">

  <div class="brand">
    <div class="brand-logo">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f0a500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
    </div>
    <h1>NexusHub</h1>
    <p>Web3 Wallet Platform</p>
  </div>

  <div class="card">

    <h2 class="card-heading">Welcome back</h2>
    <p class="card-sub">Sign in to access your wallet</p>

    <?php if (!empty($alertMsg)): ?>
    <div class="alert alert-<?php echo $alertType; ?>">
      <?php if ($alertType === 'error'): ?>
      <svg class="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php else: ?>
      <svg class="alert-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <?php endif; ?>
      <?php echo htmlspecialchars($alertMsg); ?>
    </div>
    <?php endif; ?>

    <form method="post" action="/signin/auth" novalidate>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="2,4 12,13 22,4"/></svg>
          <input class="form-input" type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="pwd">Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input class="form-input" type="password" id="pwd" name="pwd" placeholder="Enter your password" required>
        </div>
      </div>

      <a href="#" class="forgot-link">Forgot password?</a>

      <button type="submit" class="btn-submit">Sign In</button>

    </form>

    <div class="divider">or</div>

    <p class="auth-footer">New to NexusHub? <a href="/signup">Create an account</a></p>

  </div>
</div>
</body>
</html>
