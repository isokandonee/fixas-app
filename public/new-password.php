<?php
require "include/header.php";
?>
<div class="container">
    <div class="row">
        <div class="col-sm-6 m-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Set New Password</h4>
                </div>
                <div class="card-body">
                    <?php 
                        if (!isset($_GET['token'])) {
                            echo '<div class="alert alert-danger">Invalid request</div>';
                            exit();
                        }
                        $token = $_GET['token'];
                    ?>
                    <form id="newPasswordForm" action="controller/process-new-password.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$">
                            <div id="password-strength" class="password-strength"></div>
                            <small class="form-text text-muted">
                                Password must contain at least 8 characters, including uppercase, lowercase, numbers and special characters
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">
                                Passwords do not match
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Set New Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="script/validation.js"></script>
<?php
require "include/footer.php";
?>