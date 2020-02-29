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
        <div class="col-sm-6 bg-light">
            
        </div>
        <div class="col-sm-6 bg-light">
            
        </div>
    </div>
</div>
<?php
    include "../include/footer.php";
?>
