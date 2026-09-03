<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DRAMS | Verify Code</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= URL::base(); ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/kpkhtml.min.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/pre-loader.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/animate.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/new-style.css">
    <link rel="shortcut icon" href="<?= URL::base(); ?>dist/img/icon/logo.png">
</head>

<body class="hold-transition login-page">
<div id="particles-js"></div>

<div class="login-box">
    <div class="login-box-body">

        <?php if (!empty($message)): ?>
            <div class="alert alert-info alert-dismissable">
                <button class="close" data-dismiss="alert">&times;</button>
                <?= HTML::chars($message); ?>
            </div>
        <?php endif; ?>

        <h3>Verify it's you</h3>
        <b style="color:#40a64a">DRAMS - Digital Records Analysis &amp; Monitoring System</b>
        <br/><br/>
        <p>We sent a verification code to your <?= ($channel === 'email') ? 'e-mail address' : 'WhatsApp number'; ?>. Enter it below to continue.</p>

        <p id="otp-timer" style="font-weight:bold;"></p>

        <?= Form::open('login/verify_otp', array('id' => 'otpform')); ?>
        <?= Form::hidden('csrf', Security::token()); ?>

        <div class="form-group has-feedback">
            <?= Form::input(
                'otp',
                '',
                array('class' => 'form-control', 'placeholder' => 'Enter code', 'autocomplete' => 'one-time-code', 'autofocus' => 'autofocus')
            ); ?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <?= Form::submit('verify', 'Verify', array('class' => 'btn btn-primary btn-block', 'id' => 'otp-submit')); ?>
            </div>
        </div>

        <?= Form::close(); ?>

        <br>
        <?= Form::open('login/resend_otp'); ?>
        <?= Form::hidden('csrf', Security::token()); ?>
        <button type="submit" class="btn btn-link" id="resend-btn" style="padding:0;" disabled>Resend code</button>
        <?= Form::close(); ?>

        <a href="<?= URL::site('login'); ?>" class="pull-right">Back to login</a>

    </div>
</div>

<!-- JS -->
<script src="<?= URL::base(); ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?= URL::base(); ?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?= URL::base(); ?>dist/js/particles.js"></script>
<script src="<?= URL::base(); ?>dist/js/particles-app.js"></script>

<script>
(function () {
    var secondsLeft = <?= (int)$seconds_left; ?>;
    var resendCooldown = <?= (int)$resend_in; ?>;
    var timerEl = document.getElementById('otp-timer');
    var submitBtn = document.getElementById('otp-submit');
    var resendBtn = document.getElementById('resend-btn');

    function render() {
        if (secondsLeft <= 0) {
            timerEl.style.color = '#dd4b39';
            timerEl.textContent = 'Code expired. Please resend a new code.';
            submitBtn.disabled = true;
            resendBtn.disabled = false;
            return;
        }
        timerEl.style.color = '';
        var mins = Math.floor(secondsLeft / 60);
        var secs = secondsLeft % 60;
        timerEl.textContent = 'Code expires in ' + mins + ':' + (secs < 10 ? '0' : '') + secs;
        resendBtn.disabled = resendCooldown > 0;
    }

    render();

    var interval = setInterval(function () {
        secondsLeft--;
        if (resendCooldown > 0) {
            resendCooldown--;
        }
        render();
        if (secondsLeft <= 0) {
            clearInterval(interval);
        }
    }, 1000);
})();
</script>

</body>
</html>
