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
                                    <a class="btn btn-success" href="index.php">Sign up</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 py-5 border">
                            <!-- <p>             -->
                                <h4 class="pb-4 text-center">Login here</h4>
                            <!-- </p> -->
                            <form class="" action="controller/login.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="token" value="sdkajsdaksjdklasjdaklsdjalkjs938092qpwoalsdalsdasdasd">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <input required placeholder="you@domain.com" type="email" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <input required placeholder="********" type="password" name="password" class="form-control">
                                    </div>
                                </div>
                                    <button type="submit" class="btn mt-4 btn-primary float-right">Login</button>
                                    
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php
    require "include/footer.php";
?>