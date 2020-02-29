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
    <h2>Title</h2>
    <div class="row">
        <div class="card-columns">
            <div class="card bg-light shadow">
            <div class="card-body text-center">
                <p class="card-text">Some text inside the first card</p>
            </div>
            </div>
            <div class="card bg-light shadow">
            <div class="card-body text-center">
                <p class="card-text">Some text inside the second card</p>
            </div>
            </div>
        </div>
    </div>
</div>
<?php
    include "../include/footer.php";
?>
