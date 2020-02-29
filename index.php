<?php
    require "include/header.php";
?>
<div class="container">
    <div class="row">
        <div class="col-sm-10 m-auto">            
            <section class="testimonial py-5" id="testimonial">
                <div class="container pt-5">			
                    <div class="row">
                        <div class="col-md-4 py-2 bg-primary text-white text-center ">
                            <div class=" ">
                                <div class="card-body">
                                    <img src="http://www.ansonika.com/mavia/img/registration_bg.svg" style="width:30%">
                                    <h2 class="py-3">Registration</h2>
                                    <p>Please click the below button if you have an account already.</p>
                                    <a class="btn btn-success" href="login.php">Login</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 py-5 border">
                            <h4 class="pb-4 text-center">Please fill with your details</h4>
                                <?php 
                                    if (isset($_GET['error'])) {
                                        if ($_GET['error'] == "emptyfields") {
                                            echo '<p class="text-danger">Fill in all fields</p>';
                                        }
                                        elseif ($_GET['error'] == "invalidmail") {
                                            echo '<p class="text-danger">Email format not correct (you@domain.com)</p>';
                                        }
                                        elseif ($_GET['error'] == "incorrectdetails") {
                                            echo '<p class="text-danger">Fill all fields correctly</p>';
                                        }
                                        elseif ($_GET['error'] == "weakpassword") {
                                            echo '<p class="text-danger">Password must be greater than 8 characters and have capital letter</p>';
                                        }
                                        elseif ($_GET['error'] == "passwordsdonotmatch") {
                                            echo '<p class="text-danger">Passwords do not match</p>';
                                        }
                                        if ($_GET['error'] == "emailtaken") {
                                            echo '<p class="text-danger">Email has already ben taken</p>';
                                        }
                                        if ($_GET['error'] == "notsuccessful") {
                                            echo '<p class="text-danger">Check your details and try again</p>';
                                        }
                                    }
                                    elseif (isset($_GET['signup' == 'success'])) {
                                            echo '<p class="text-success">Signup Successful</p>';
                                    }
                                ?>
                            <form class="" action="controller/index.php" method="POST" enctype="multipart/form-data">	
                                <input type="hidden" name="token" value="sdkajsdaksjdklasjdaklsdjalkjs938092qpwoalsdalsdasdasd">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input required placeholder="Firstname" type="text" name="firstname" class="form-control">
                                    </div>                            
                                    <div class="form-group col-md-6">
                                        <input required placeholder="Lastname" type="text" name="lastname" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input required placeholder="you@domain.com" type="email" name="email" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input required placeholder="+234-801-678-9012" type="text" name="phone" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input required placeholder="********" type="password" name="password" class="form-control">
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <input required placeholder="********" type="password" name="cpassword" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkbox" value="" id="tac" required="required">
                                            <label class="form-check-label" for="invalidCheck2">
                                                <small>By clicking Submit, you agree to our Terms & Conditions, Visitor Agreement and Privacy Policy.</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                    <button type="submit" class="btn mt-4 btn-primary float-right">Sign up</button>
                                    
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="script/index.js"></script>
<?php
    require "include/footer.php";
?>