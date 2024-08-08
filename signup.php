<?php
include('session.php'); // Ensure session initialization and connection code is included

// Check if the session is started and the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: login71.php');
    exit();
}

include('login_a_check.php');

  

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heal and Glow - Create Account</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <?php include ('navbar.php'); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .signup-container {
            background-color: #F7F9F2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .signup-container .container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0;
        }
        .signup-container .jumbotron {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 40px;
            border-radius: 15px;
            flex: 1;
            margin-right: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 600px;
        }
        .signup-container .jumbotron h1 {
            font-size: 2em;
            font-weight: bold;
        }
        .signup-container .jumbotron span.edit {
            color: #F19ED2;
            font-style: italic;
        }
        .signup-container .panel {
            border-radius: 15px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin-left: 15px;
            background-color: #FFFFFF;
            max-width: 600px;
        }
        .signup-container .panel-heading {
            background-color: #91DDCF;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 20px;
            font-size: 1.7em;
            text-align: center;
            letter-spacing: 1px;
        }
        .signup-container .panel-body {
            padding: 30px;
            background-color: #E8C5E5;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        .signup-container .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 1.1em;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #CCC;
        }
        .signup-container .input-group-text {
            background-color: #F19ED2;
            color: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        .signup-container .btn-primary {
            background-color: #91DDCF;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-size: 1.3em;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .signup-container .btn-primary:hover {
            background-color: #E8C5E5;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .signup-container label {
            font-size: 1.2em;
            color: #343a40;
            font-weight: 600;
        }
        .signup-container a {
            color: #F19ED2;
            text-decoration: none;
            font-weight: 600;
        }
        .signup-container a:hover {
            text-decoration: underline;
            color: #000;
        }
        .signup-container .text-danger {
            color: #e74c3c;
        }
        @media (max-width: 992px) {
            .signup-container .jumbotron h1 {
                font-size: 1.8em;
            }
            .signup-container .panel-body {
                padding: 25px;
            }
        }
        @media (max-width: 768px) {
            .signup-container .container {
                flex-direction: column;
                align-items: center;
            }
            .signup-container .jumbotron, .panel {
                margin: 15px 0;
                width: 100%;
            }
            .signup-container .jumbotron h1 {
                font-size: 1.6em;
            }
        }
        @media (max-width: 576px) {
            .signup-container .jumbotron h1 {
                font-size: 1.4em;
            }
            .signup-container .panel-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
   
    <section class="signup-container">
        <div class="container">
            <div class="jumbotron">
                <h1>Shop Organic, Live Vibrantly! <br> Elevate Your Wellness with Organic Wonders. <span class="edit">Heal and Glow</span></h1>
                <br>
                <p>Get started by creating your account</p>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">Create Account</div>
                <div class="panel-body">
                    <form role="form" action="customer_success.php" method="POST">
                        <div class="form-group">
                            <label for="employeeName"><span class="text-danger">*</span> Username: </label>
                            <div class="input-group">
                                <input class="form-control" id="employeeName" type="text" name="employeeName" placeholder="Your Username" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-user" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="email"><span class="text-danger">*</span> Email: </label>
                            <div class="input-group">
                                <input class="form-control" id="mail" type="email" name="email" placeholder="Email" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="phoneNumber"><span class="text-danger">*</span> Contact: </label>
                            <div class="input-group">
                                <input class="form-control" id="phoneNumber" type="number" name="phoneNumber" placeholder="Contact" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-phone" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="address"><span class="text-danger">*</span> Address: </label>
                            <div class="input-group">
                                <input class="form-control" id="address" type="text" name="address" placeholder="Address" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-home" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="Password"><span class="text-danger">*</span> Password: </label>
                            <div class="input-group">
                                <input class="form-control" id="Password" type="password" name="Password" placeholder="Password" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-lock" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="companyName"><span class="text-danger">*</span> Hotel Name: </label>
                            <div class="input-group">
                                <input class="form-control" id="companyName" type="text" name="companyName" placeholder="Hotel Name" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-home" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="role"><span class="text-danger">*</span> User Role: </label>
                            <div class="input-group">
                                <input class="form-control" id="role" type="text" name="role" placeholder="User Role" required="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-home" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>

                        <div class="form-group">
                            <label for="age"><span class="text-danger">*</span> Age: </label>
                            <div class="input-group">
                                <input class="form-control" id="age" type="number" name="age" placeholder="Age">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="glyphicon glyphicon-phone" aria-hidden="true"></i></span>
                                </div>
                            </div>           
                        </div>
                        
                        <div class="form-group">
                            <button class="btn btn-primary btn-block" type="submit">Submit</button>
                        </div>
                        
                        <label style="margin-left: 5px;">or</label> <br>
                        <label style="margin-left: 5px;"><a href="login71.php">Have an account? Login.</a></label>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
</body>
</html>
