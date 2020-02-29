<?php
include "../include/header.php";
?>
<?php
    if (!isset($_SESSION['email'])) {        
    header("Location: ../login.php?error=notloggedin");
    exit();
    }
?>

<div class="container">
    <div class="row">
        <div class="col-sm-10 m-auto">            
            <section class="testimonial py-5" id="testimonial">
                <div class="container pt-5">			
                    <div class="row">
                        <div class="col-md-8 py-5 shadow border">
                            <h4 class="pb-4 text-center">Create Account</h4>
                            <form class="" action="../controller/account.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="token" value="sdkajsdaksjdklasjdaklsdjalkjs938092qpwoalsdalsdasdasd">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="account">Account Type</label>
                                        <select required class="form-control" id="account" name="account">
                                            <option name="account" class="form-control-option" value="1">Current</option>
                                            <option name="account" class="form-control-option" value="2">Savings</option>
                                            <option name="account" class="form-control-option" value="3">Domiciliary</option>
                                        </select>
                                    </div>
                                </div>
                                    <button type="submit" class="btn mt-4 btn-primary float-right">Create Account</button>                                    
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php
    include "../include/footer.php";
?>