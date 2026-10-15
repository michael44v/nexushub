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
    if($chk && $chk->num_rows > 0) { $emailError = true; }
    else {
      $put = $conn->query("insert into user_details(uname,uemail,upwd,kyc_status) values('$u','$m','$pwd','Not Submitted')");
      if($put) { $success = true; }
      $new_id = $conn->insert_id;
      if ($new_id) {
          $conn->query("insert into bal(uid,bal) values('$new_id','0.00')");
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Wallet — NexusHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg: #07080a;
      --surface: #11141c;
      --surface2: #171b26;
      --border: rgba(255,255,255,0.08);
      --accent: #f0a500;
      --accent-hover: #ffb71a;
      --accent-dim: rgba(240,165,0,0.12);
      --accent-glow: rgba(240,165,0,0.25);
      --text: #f0f2f5;
      --muted: #808a9d;
      --error-bg: rgba(239,68,68,0.1);
      --error-border: rgba(239,68,68,0.3);
      --error-text: #f87171;
      --success-bg: rgba(34,197,94,0.1);
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
      padding: 2.5rem 1rem;
      position: relative;
      overflow-x: hidden;
    }

    /* Background grid */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background-image:
        linear-gradient(rgba(240,165,0,0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(240,165,0,0.035) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
      z-index: 0;
    }

    /* Glow orb */
    body::after {
      content: '';
      position: fixed;
      top: -250px; right: -250px;
      width: 800px; height: 800px;
      background: radial-gradient(circle, rgba(240,165,0,0.08) 0%, transparent 65%);
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
      margin-bottom: 2rem;
    }

    .brand-logo {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 56px; height: 56px;
      background: linear-gradient(135deg, rgba(240,165,0,0.2) 0%, rgba(240,165,0,0.05) 100%);
      border: 1px solid rgba(240,165,0,0.3);
      border-radius: 16px;
      margin-bottom: 1rem;
      box-shadow: 0 0 20px rgba(240,165,0,0.15);
    }

    .brand h1 {
      font-family: 'Syne', sans-serif;
      font-size: 1.75rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      color: #ffffff;
    }

    .brand p {
      font-size: 0.9rem;
      color: var(--muted);
      margin-top: 0.35rem;
      font-weight: 400;
    }

    /* Card */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 2.25rem 2rem;
      box-shadow:
        0 0 0 1px rgba(255,255,255,0.03),
        0 32px 64px rgba(0,0,0,0.6);
      backdrop-filter: blur(12px);
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
      border-radius: 12px;
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

    .alert a { color: inherit; font-weight: 600; text-decoration: underline; }

    /* Form */
    .form-group { margin-bottom: 1.25rem; }

    .form-label {
      display: block;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 0.45rem;
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
      border-radius: 12px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      padding: 0.85rem 1rem 0.85rem 2.75rem;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      -webkit-appearance: none;
    }

    .form-input::placeholder { color: #4a5264; }

    .form-input:focus {
      border-color: rgba(240,165,0,0.5);
      background: rgba(240,165,0,0.03);
      box-shadow: 0 0 0 3px var(--accent-glow);
    }

    /* Captcha Component */
    .captcha-card {
      background: rgba(0,0,0,0.3);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 1rem;
      margin-top: 1.25rem;
      margin-bottom: 1.25rem;
    }

    .captcha-row {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .captcha-canvas-wrap {
      flex-shrink: 0;
      background: #f3f4f6;
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2px;
      border: 1px solid rgba(255,255,255,0.2);
    }

    #captchaCanvas {
      display: block;
      border-radius: 6px;
    }

    .captcha-input-wrap {
      flex: 1;
    }

    .captcha-input {
      width: 100%;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 10px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      padding: 0.75rem 0.85rem;
      outline: none;
      transition: border-color 0.2s;
    }

    .captcha-input:focus {
      border-color: rgba(240,165,0,0.5);
    }

    .captcha-actions {
      display: flex;
      align-items: center;
      gap: 1.25rem;
      margin-top: 0.75rem;
      font-size: 0.8rem;
    }

    .captcha-btn {
      background: none;
      border: none;
      color: var(--accent);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      font-size: 0.8rem;
      font-weight: 500;
      transition: opacity 0.2s;
      padding: 0;
    }

    .captcha-btn:hover {
      opacity: 0.8;
      text-decoration: underline;
    }

    /* Submit Button */
    .btn-submit {
      width: 100%;
      padding: 0.95rem 1rem;
      background: var(--accent);
      color: #07080a;
      font-family: 'Syne', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      letter-spacing: 0.03em;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      margin-top: 0.5rem;
      box-shadow: 0 4px 20px rgba(240,165,0,0.25);
    }

    .btn-submit:hover:not(:disabled) {
      background: var(--accent-hover);
      transform: translateY(-1px);
      box-shadow: 0 8px 28px rgba(240,165,0,0.35);
    }

    .btn-submit:disabled {
      background: #2a2e3d;
      color: #5c6479;
      cursor: not-allowed;
      box-shadow: none;
      opacity: 0.7;
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin: 1.5rem 0;
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
      font-weight: 600;
      transition: opacity 0.15s;
    }

    .auth-footer a:hover { opacity: 0.8; }

    @media (max-width: 520px) {
      .card { padding: 1.75rem 1.25rem; }
      .captcha-row { flex-direction: column; align-items: stretch; }
      .captcha-canvas-wrap { align-self: center; }
    }
  </style>
</head>
<body>
<div class="page-wrapper">

  <div class="brand">
    <div class="brand-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f0a500" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
    </div>
    <h1>NexusHub</h1>
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
    <script>setTimeout(function(){ window.location.href = '/signin/success'; }, 1000);</script>
    <?php endif; ?>

    <form method="post" action="/signup" id="signupForm" novalidate>

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

      <!-- Captcha Component -->
      <div class="captcha-card">
        <label class="form-label" style="margin-bottom:0.6rem;">Security Verification</label>
        <div class="captcha-row">
          <div class="captcha-canvas-wrap">
            <canvas id="captchaCanvas" width="130" height="42"></canvas>
          </div>
          <div class="captcha-input-wrap">
            <input type="text" id="captchaInput" class="captcha-input" placeholder="Type the numbers" autocomplete="off" />
          </div>
        </div>
        <div class="captcha-actions">
          <button type="button" class="captcha-btn" id="refreshCaptchaBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
            New Code
          </button>
          <span style="color:var(--border);">|</span>
          <button type="button" class="captcha-btn" id="audioCaptchaBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
            Vision Impaired
          </button>
        </div>
      </div>

      <button type="submit" id="submitBtn" class="btn-submit" disabled>Create Wallet</button>

    </form>

    <div class="divider">or</div>

    <p class="auth-footer">Already have an account? <a href="/signin/auth">Sign in</a></p>

  </div>
</div>

<script>
let currentCaptchaCode = '';

function generateCaptchaNumbers() {
  let digits = '';
  for (let i = 0; i < 4; i++) {
    digits += Math.floor(Math.random() * 10);
  }
  return digits;
}

function drawCaptcha() {
  const canvas = document.getElementById('captchaCanvas');
  const ctx = canvas.getContext('2d');
  currentCaptchaCode = generateCaptchaNumbers();

  // Clear canvas
  ctx.fillStyle = '#f3f4f6';
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  // Background noise lines
  for (let i = 0; i < 5; i++) {
    ctx.strokeStyle = `rgba(${Math.floor(Math.random()*150)}, ${Math.floor(Math.random()*150)}, ${Math.floor(Math.random()*150)}, 0.4)`;
    ctx.lineWidth = 1 + Math.random();
    ctx.beginPath();
    ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
    ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
    ctx.stroke();
  }

  // Draw numbers
  ctx.font = 'bold 24px "DM Sans", sans-serif';
  ctx.textBaseline = 'middle';

  const startX = 15;
  const spacing = 26;

  for (let i = 0; i < currentCaptchaCode.length; i++) {
    const char = currentCaptchaCode[i];
    ctx.save();
    const x = startX + i * spacing;
    const y = canvas.height / 2 + (Math.random() * 4 - 2);
    const angle = (Math.random() - 0.5) * 0.3;

    ctx.translate(x, y);
    ctx.rotate(angle);

    ctx.fillStyle = `rgb(${Math.floor(Math.random()*80)}, ${Math.floor(Math.random()*80)}, ${Math.floor(Math.random()*80)})`;
    ctx.fillText(char, 0, 0);
    ctx.restore();
  }

  // Check validation state
  validateCaptcha();
}

function validateCaptcha() {
  const input = document.getElementById('captchaInput').value.trim();
  const submitBtn = document.getElementById('submitBtn');
  if (input === currentCaptchaCode) {
    submitBtn.disabled = false;
  } else {
    submitBtn.disabled = true;
  }
}

document.getElementById('refreshCaptchaBtn').addEventListener('click', function() {
  document.getElementById('captchaInput').value = '';
  drawCaptcha();
});

document.getElementById('audioCaptchaBtn').addEventListener('click', function() {
  if ('speechSynthesis' in window) {
    window.speechSynthesis.cancel();
    const spoken = currentCaptchaCode.split('').join(' ');
    const utterance = new SpeechSynthesisUtterance('Verification code is ' + spoken);
    utterance.rate = 0.85;
    window.speechSynthesis.speak(utterance);
  } else {
    alert('Code: ' + currentCaptchaCode);
  }
});

document.getElementById('captchaInput').addEventListener('input', validateCaptcha);

// Initialize on page load
window.addEventListener('load', drawCaptcha);
</script>
</body>
</html>
