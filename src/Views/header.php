<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fixas-Bank</title>
    
    <!-- Primary Meta Tags -->
    <meta name="description" content="Fixas-Bank - Secure and convenient online banking platform">
    <meta name="keywords" content="banking, online banking, secure banking, Fixas-Bank">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/favicon.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo isset($_SESSION['email']) ? '../css/theme.css' : 'css/theme.css'; ?>">
    <link rel="stylesheet" href="<?php echo isset($_SESSION['email']) ? '../css/validation.css' : 'css/validation.css'; ?>">
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" defer></script>
    <script src="<?php echo isset($_SESSION['email']) ? '../script/index.js' : 'script/index.js'; ?>" defer></script>
    <script src="<?php echo isset($_SESSION['email']) ? '../script/validation.js' : 'script/validation.js'; ?>" defer></script>
</head>
<body>
    <header class="mb-5">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="<?php echo isset($_SESSION['email']) ? '../dashboard/index.php' : '#'; ?>">
                    <i class="bi bi-bank me-2"></i>Fixas-Bank
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarContent">
                    <?php if (isset($_SESSION['email'])): ?>
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $CURRENT_PAGE == "Dashboard" ? 'active' : ''; ?>" 
                                   href="../dashboard/index.php">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $CURRENT_PAGE == "Account" ? 'active' : ''; ?>" 
                                   href="../dashboard/account.php">
                                    <i class="bi bi-person-plus me-1"></i>Create Account
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $CURRENT_PAGE == "Deposit" ? 'active' : ''; ?>" 
                                   href="../dashboard/deposit.php">
                                    <i class="bi bi-cash-coin me-1"></i>Deposit
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $CURRENT_PAGE == "Withdraw" ? 'active' : ''; ?>" 
                                   href="../dashboard/withdraw.php">
                                    <i class="bi bi-cash me-1"></i>Withdraw
                                </a>
                            </li>
                        </ul>
                        
                        <div class="d-flex align-items-center">
                            <span class="text-secondary me-3">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']); ?>
                            </span>
                            <form action="../controller/logout.php" method="POST">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </button>
                            </form>
                        </div>
                        
                    <?php else: ?>
                        <div class="ms-auto">
                            <form class="d-flex gap-2" action="controller/login.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="token" value="<?php echo bin2hex(random_bytes(32)); ?>">
                                <div class="form-floating">
                                    <input class="form-control" type="email" id="email" placeholder="Email" name="email" required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="form-floating">
                                    <input class="form-control" type="password" id="password" placeholder="Password" name="password" required>
                                    <label for="password">Password</label>
                                </div>
                                <button class="btn btn-primary d-flex align-items-center" type="submit">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
        <?php if (isset($_SESSION['error']) || isset($_SESSION['success'])): ?>
            <div class="container mt-3">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php 
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php 
                            echo htmlspecialchars($_SESSION['success']);
                            unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </header>