<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DRAMS | Log in</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= URL::base(); ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/kpkhtml.min.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/pre-loader.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/animate.css">
    <link rel="stylesheet" href="<?= URL::base(); ?>dist/css/new-style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="<?= URL::base(); ?>dist/img/icon/logo.png">
</head>

<body class="hold-transition login-page">
<div id="particles-js"></div>
<div id="preloader">
    <div class="loader"><span></span><span></span><span></span><span></span></div>
</div>

<div class="login-box">
    <!--<div class="login-logo">
        <img src="<?= URL::base(); ?>dist/img/logo.png" style="width:300px;">
    </div>-->

    <div class="login-box-body">

        <!-- Flash Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger alert-dismissable">
                <button class="close" data-dismiss="alert">&times;</button>
                <strong>Note:</strong> <?= HTML::chars($message); ?>
            </div>
        <?php endif; ?>

        <h3>Sign in</h3>
        <b style="color:#40a64a"> DRAMS - Digital Records Analysis & Monitoring System </b>
        <br/><br/>
        <p>Enter your username and password</p>

        <?= Form::open('login/check', ['id' => 'entrypoint']); ?>
        <?= Form::hidden('csrf', Security::token()); ?>
        <?= Form::hidden('public_ip', '', ['id' => 'public_ip']); ?>
        <?= Form::hidden('geo_lat', '', ['id' => 'geo_lat']); ?>
        <?= Form::hidden('geo_lng', '', ['id' => 'geo_lng']); ?>
        <?= Form::hidden('geo_accuracy', '', ['id' => 'geo_accuracy']); ?>

        <div class="form-group has-feedback">
            <?= Form::input(
                'username',
                HTML::chars(Arr::get($_POST, 'username')),
                ['class' => 'form-control', 'placeholder' => 'Username']
            ); ?>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
            <?= Form::password(
                'password',
                '',
                ['class' => 'form-control', 'placeholder' => 'Password']
            ); ?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <!-- <div class="form-group">
            <label>Who You Are?</label>
            <select name="type" class="form-control" >
                <option value="">Please Select Type</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= HTML::chars($role->name); ?>">
                        <?= HTML::chars($role->label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div> -->

        <div class="row">
            <div class="col-xs-12">
                <?= Form::submit('login', 'Sign In', ['class' => 'btn btn-primary btn-block']); ?>
            </div>
        </div>

        <?= Form::close(); ?>

        <br>
        <a href="#" class="forgetformopen pull-right">I forgot my password</a>

    </div>
</div>

<!-- Password Recovery Modal -->
<div class="modal fade" id="forgetformmodel">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Password Recovery</h4>
            </div>

            <form method="post" action="<?= URL::site('login/forget'); ?>">
                <?= Form::hidden('csrf', Security::token()); ?>

                <div class="modal-body">

                    <div class="form-group">
                        <label>User Type</label>
                        <select name="ftype" class="form-control" required>
                            <option value="">Select</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= (int)$role->id; ?>">
                                    <?= HTML::chars($role->label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="fusername" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="femail" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Request</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- JS -->
<script src="<?= URL::base(); ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?= URL::base(); ?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?= URL::base(); ?>plugins/iCheck/icheck.min.js"></script>

<script>
$(function () {
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
    });

    $('.forgetformopen').on('click', function () {
        $('#forgetformmodel').modal('show');
    });

    setTimeout(function () {
        $('#preloader').fadeOut();
    }, 600);
});
</script>

<script>
// Server only sees the OpenVPN tunnel IP for logged-in users, so the real
// public IP has to be looked up client-side and submitted with the login form.
var publicIpPromise = fetch('https://api.ipify.org?format=json')
    .then(function (r) { return r.json(); })
    .then(function (data) {
        document.getElementById('public_ip').value = data.ip;
        return data.ip;
    })
    .catch(function () {
        return null;
    });

document.getElementById('entrypoint').addEventListener('submit', function (e) {
    var field = document.getElementById('public_ip');
    if (field.value) {
        return;
    }
    e.preventDefault();
    var form = this;
    publicIpPromise.then(function () {
        form.submit();
    });
});
</script>

<script>
// Best-effort precise location: requires the user's explicit browser
// permission grant and a secure (HTTPS) origin. Not awaited before submit —
// the permission prompt can sit unanswered indefinitely, so login must not
// wait on it. If it resolves before the user submits, it rides along;
// otherwise the login just proceeds without it.
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (pos) {
        document.getElementById('geo_lat').value = pos.coords.latitude;
        document.getElementById('geo_lng').value = pos.coords.longitude;
        document.getElementById('geo_accuracy').value = Math.round(pos.coords.accuracy);
    }, function () {
        // denied, unsupported, insecure origin, or timed out - ignore
    }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
}
</script>
<script src="<?= URL::base(); ?>dist/js/particles.js"></script>
<script src="<?= URL::base(); ?>dist/js/particles-app.js"></script>

</body>
</html>
