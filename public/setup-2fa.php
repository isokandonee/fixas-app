<?php
require "include/header.php";
require_once "controller/TwoFactorAuth.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$twoFA = TwoFactorAuth::getInstance();
$secret = $twoFA->generateSecret($_SESSION['user_id']);
$qrCode = $twoFA->getQRCode($_SESSION['user_id'], $_SESSION['email']);
?>
<div class="container">
    <div class="row">
        <div class="col-sm-8 m-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Two-Factor Authentication Setup</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Enhanced Security!</strong> Two-factor authentication adds an extra layer of security to your account.
                    </div>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                                switch($_GET['error']) {
                                    case 'invalid_code':
                                        echo 'Invalid verification code. Please try again.';
                                        break;
                                    case 'setup_failed':
                                        echo 'Failed to setup 2FA. Please try again.';
                                        break;
                                    default:
                                        echo 'An error occurred. Please try again.';
                                }
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="setup-steps">
                        <h5>Step 1: Install an Authenticator App</h5>
                        <p>If you haven't already, install an authenticator app on your mobile device:</p>
                        <ul>
                            <li><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google Authenticator for Android</a></li>
                            <li><a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">Google Authenticator for iOS</a></li>
                        </ul>

                        <hr>

                        <h5>Step 2: Scan QR Code</h5>
                        <p>Open your authenticator app and scan this QR code:</p>
                        <div class="text-center">
                            <img src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=<?php echo urlencode($qrCode); ?>" 
                                 alt="QR Code" class="img-thumbnail">
                        </div>
                        
                        <div class="manual-entry mt-3">
                            <p>If you can't scan the QR code, enter this code manually:</p>
                            <code class="d-block p-2 bg-light"><?php echo chunk_split($secret, 4, ' '); ?></code>
                        </div>

                        <hr>

                        <h5>Step 3: Verify Setup</h5>
                        <p>Enter the 6-digit code from your authenticator app to verify the setup:</p>
                        <form action="controller/verify-2fa-setup.php" method="POST" class="needs-validation" novalidate>
                            <?php
                                $security = Security::getInstance();
                                $csrf_token = $security->generateCSRFToken();
                            ?>
                            <input type="hidden" name="csrf_token" value="<?php echo $security->escapeOutput($csrf_token); ?>">
                            
                            <div class="form-group">
                                <input type="text" class="form-control" name="code" 
                                       pattern="[0-9]{6}" maxlength="6" required
                                       placeholder="Enter 6-digit code">
                                <div class="invalid-feedback">
                                    Please enter a valid 6-digit code
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Verify and Enable 2FA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="script/validation.js"></script>
<?php require "include/footer.php"; ?>