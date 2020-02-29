<?php
include "../include/header.php";
?>
<!-- <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Profile</h4>
                            </div>
                            <div class="content">
                                    <form enctype="multipart/form-data" action="account.php" method="POST">
                                        <div class="row">
                                            <div class="col-md-8 pl-1">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Amount to deposit</label>
                                                    <input type="text" class="form-control" name="amount" placeholder="Amount">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 pr-1">
                                                <div class="form-group">
                                                    <label>Savings</label>
                                                    <input type="checkbox" checked class="form-control" name="savings">
                                                </div>
                                            </div>
                                            <div class="col-md-6 pl-1">
                                                <div class="form-group">
                                                    <label>Current</label>
                                                    <input type="checkbox" checked class="form-control" name="current">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-fill pull-right">Create</button>
                                        <div class="clearfix"></div>
                                    </form>
                            </div>
                        </div>
                    </div>

                    

                </div>
            </div>
        </div> -->

        <div class="container">
    <div class="row">
        <div class="col-sm-10 m-auto">            
            <section class="testimonial py-5" id="testimonial">
                <div class="container pt-5">			
                    <div class="row">
                        <div class="col-md-8 py-5 border">
                            <!-- <p>             -->
                                <h4 class="pb-4 text-center">Create Account</h4>
                            <!-- </p> -->
                            <form class="" action="../controller/account.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="token" value="sdkajsdaksjdklasjdaklsdjalkjs938092qpwoalsdalsdasdasd">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="exampleInputEmail1">Amount to deposit</label>
                                        <input required placeholder="Amount" type="email" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="sel1">Account Type</label>
                                        <select class="form-control" id="sel1">
                                            <option value="1">Current</option>
                                            <option value="2">Savings</option>
                                            <option value="3">Domiciliary</option>
                                        </select>
                                    </div>
                                </div>
                                    <button type="submit" class="btn mt-4 btn-primary float-right">Create</button>                                    
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