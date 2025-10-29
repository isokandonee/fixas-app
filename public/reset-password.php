<?php
require "include/header.php";
?>
<div class="container">
    <div class="row">
        <div class="col-sm-6 m-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Reset Password</h4>
                </div>
                <div class="card-body">
                    <?php 
                        if (isset($_GET['error'])) {
                            if ($_GET['error'] == "invalidtoken") {
                                echo '<div class="alert alert-danger">Invalid or expired reset token</div>';
                            } elseif ($_GET['error'] == "previouslyused") {
                                echo '<div class="alert alert-danger">Password was previously used. Please choose a different password</div>';
                            } elseif ($_GET['error'] == "weakpassword") {
                                echo '<div class="alert alert-danger">Password must meet security requirements</div>';
                            }
                        }
                        if (isset($_GET['status']) && $_GET['status'] == "success") {
                            echo '<div class="alert alert-success">Password has been reset successfully</div>';
                        }
                    ?>
                    <form id="resetRequestForm" action="controller/process-reset.php" method="POST" class="needs-validation" novalidate>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">
                                Please enter a valid email address
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Request Password Reset</button>
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