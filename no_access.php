<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #f19ed2, #91ddcf);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        .container h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #ff4c4c;
        }

        .container p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .container a {
            text-decoration: none;
            color: #fff;
            background-color: #ff4c4c;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .container a:hover {
            background-color: #e63b3b;
        }

        @media (max-width: 600px) {
            .container h1 {
                font-size: 2rem;
            }

            .container p {
                font-size: 1rem;
            }

            .container a {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Access Denied</h1>
    <p>You do not have permission to access this page.</p>
    <p>Redirecting to the homepage in 5 seconds...</p>
    <a href="index.php">Go to Homepage Now</a>
</div>

<script>
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 5000); // 5 seconds
</script>

</body>
</html>
