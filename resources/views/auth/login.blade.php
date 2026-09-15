<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign in · Worqsy</title>
  <link rel="stylesheet" href="{{ asset('css/worqsy.css') }}">
</head>
<body>
<div class="auth-page">
  <section class="auth-hero">
    <div class="brand"><span class="brand-mark">W</span><span>Worqsy</span></div>
    <div>
      <h1>Know what matters. Know who owns it.</h1>
      <p>Worqsy connects people, projects, responsibilities, review, progress, and lifecycle in one structured workspace.</p>
    </div>
    <div style="color:#94a3b8;font-size:13px">Work better, together.</div>
  </section>
  <section class="auth-panel">
    <div class="auth-box">
      <div class="auth-logo">Worqsy</div>
      <h2>Welcome back</h2>
      <p class="muted">Continue with your Google account.</p>
      @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
      <a class="btn btn-primary w-full" style="justify-content:center;margin-top:22px" href="{{ route('auth.google') }}">Continue with Google</a>

      @if(config('worqsy.allow_demo_login'))
      <div class="divider">LOCAL DEVELOPMENT</div>
      <form method="POST" action="{{ route('login.demo') }}">
        @csrf
        <div class="form-group">
          <label class="label">Demo email</label>
          <input class="input" name="email" value="{{ old('email','lukman@worqsy.local') }}">
        </div>
        <div class="form-group mt-3">
          <label class="label">Password</label>
          <input class="input" type="password" name="password" value="password">
        </div>
        <button class="btn btn-secondary w-full mt-4" style="justify-content:center">Sign in to demo workspace</button>
      </form>
      <p class="hint mt-3">Demo login is controlled by <code>WORQSY_ALLOW_DEMO_LOGIN</code>. Disable it in production.</p>
      @endif
    </div>
  </section>
</div>
</body>
</html>
