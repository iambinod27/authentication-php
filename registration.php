<?php 
    session_start();
    if(!isset($_SESSION['user'])){
        header('Location: index.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">    
        <?php 
            if(isset($_POST["submit"])) {
                $fullName =  $_POST['fullname'];
                $email =  $_POST['email'];
                $password =  $_POST['password'];
                $repeatPassword =  $_POST['repeat_password'];

                $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();

                if(empty($fullName) OR empty($email) OR empty($password) OR empty($repeatPassword)) {
                    array_push($errors, "All fields are required"); 
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Email is not valid");
                }

                if( strlen($password) < 8) {
                    array_push($errors, "Password must be at 8 characters long");
                }

                if($password != $repeatPassword) {
                    array_push($errors, "Password do not match");
                }
                
                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $rowCount = mysqli_num_rows($result);
                if($rowCount > 0) {
                    array_push($errors, "Email already Exists!!");
                }


                if(count($errors)>0){
                    foreach($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                } else {
                    // INSERT DATA IN DATABASE
                    $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ? , ?)";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    if($prepareStmt) {
                        mysqli_stmt_bind_param($stmt,"sss", $fullName, $email, $passwordHashed);
                        mysqli_stmt_execute($stmt);
                        echo "<div class='alert alert-success'>Register Successfully</div>";
                    } else {
                        die("Something went wrong");
                    }

                }
            }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
         <div class="form-group">
             <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" name="submit" value="Register" >
            </div>
        </form>
    </div>
</body>
</html>