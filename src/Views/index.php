<?php
    require_once __DIR__ . '/header.php';
?>

<?php
    if (!isset($_SESSION['email'])) {        
    header("Location: ../login.php?error=notloggedin");
    exit();
    }
?>
<?php
    require_once __DIR__ . '/../Controllers/connect.php';
    $id = $_SESSION['user_id'];
    $sql = mysqli_query($conn, "SELECT * from transaction WHERE destination_id OR source_id = '$id'");

    $fetch = mysqli_fetch_array($sql);

?>
<div class="container">
    <h2>Dashboard</h2>
    <div class="row">
        <div class="card-columns">
            <div class="card bg-light shadow">
            <div class="card card-title">
                <p class="card-header">Account Number</p>
            </div>
            <div class="card-body text-center">
                <p class="card-text text-warning">
                    <?php
                        require_once __DIR__ . '/../Controllers/connect.php';
                        $id = $_SESSION['user_id'];
                        $sql = mysqli_query($conn, "SELECT * from transaction WHERE destination_id OR source_id = '$id'");

                        $fetch = mysqli_fetch_array($sql);
                    ?>
                </p>
            </div>
            </div>
            <div class="card bg-light shadow">
            <div class="card card-title">
                <p class="card-header">Account Balance</p>
            </div>
            <div class="card-body text-center">
                <p class="card-text text-warning">
                    <?php
                        require_once __DIR__ . '/../Controllers/connect.php';
                        $id = $_SESSION['user_id'];
                        $sql = mysqli_query($conn, "SELECT * from user_account WHERE user_id = '$id'");
                        $fetch = mysqli_fetch_array($sql);
                        $acc = $fetch['account_number'];
                        if ($acc > 0) {
                            echo $acc;
                        }
                        else{
                            echo "No Account Yet";
                        }
                    ?>
                </p>
            </div>
            </div>
        </div>
    </div>
</div>
<?php
    require_once __DIR__ . '/footer.php';
?>
