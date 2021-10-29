<?php require_once 'controllers/authController.php';
if (isset($_SESSION['id'])) {
    header('location: index.php');
    exit(0);
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CDN-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="css/style.css">
    <title>Login | Project Professional</title>
</head>

<body class="auth-body">
    <div class="container">
        <div class="row p-4">
            <div class="col-md-6 offset-md-3 form-div">
            <form action="login.php" method="post" class="">
                    <h1 class="text-center">Sign in</h1>

                    <?php if (count($errors) > 0) : ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) : ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['errorSession'])) : ?>
                        <div class="alert alert-info">
                            <?php echo $_GET['errorSession']; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['loggedOut'])) : ?>
                        <div class="alert alert-warning">
                            <?php echo $_GET['loggedOut']; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['passwordReset'])) : ?>
                        <div class="alert alert-success">
                            <?php echo $_GET['passwordReset']; ?>
                        </div>
                    <?php endif; ?>

                    <div style="display: none;" id="googleAlert"class="alert alert-danger"></div>

                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input type="text" name="username" value="<?php echo $username; ?>" class="form-control form-control-lg">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="login-btn" class="btn btn-primary btn-block btn-lg">Sign in</button>
                    </div>
                    <hr>
                    <p class="text-center">Not yet a member? <a href="register.php">Register</a></p>



                </form>

            </div>
        </div>
    </div>

</body>

</html>