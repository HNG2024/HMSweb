<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
             /* Reset some default styles */
        * {
            margin: 0;
            padding: 0;
        }

        /* Ensure the page takes the full height of the viewport */
        html, body {
            height: 100%;
        }

        /* Style the footer */
        .footer {
            background-color: #424240;
            padding: 70px 0;
        }

        .footer .container {
            max-width: 1070px;
            margin: auto;
        }

        .footer .row {
            display: flex;
            flex-wrap: wrap;
        }

        .footer ul {
            list-style: none;
        }

        .footer-col {
            width: 25%;
            padding: 0 15px;
        }

        .footer-col h4 {
            font-size: 18px;
            color: #ffffff;
            text-transform: capitalize;
            margin-bottom: 35px;
            font-weight: 500;
            position: relative;
        }

        .footer-col h4::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            background-color: #e91e63;
            height: 2px;
            box-sizing: border-box;
            width: 50px;
        }

        .footer-col ul li:not(:last-child) {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            font-size: 16px;
            text-transform: capitalize;
            color: #ffffff;
            text-decoration: none;
            font-weight: 300;
            color: #bbbbbb;
            display: block;
            transition: all 0.3s ease;
        }

        .footer-col ul li a:hover {
            color: #ffffff;
            padding-left: 8px;
        }

        .footer-col .social-links a {
            display: inline-block;
            height: 40px;
            width: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 0 10px 10px 0;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            color: #ffffffd5;
            transition: all 0.5s ease;
        }

        .footer-col .social-links a:hover {
            color: #24262b;
            background-color: #ffffff;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .footer-col {
                width: 50%;
                margin-bottom: 30px;
            }
        }

        @media (max-width: 574px) {
            .footer-col {
                width: 100%;
            }
        }

        /* Additional styles for the second CSS snippet */
        /* Style the columns in the footer */
        .container {
            width: 70%;
            margin: 0 auto;
        }

        .footer-col {
            float: left;
        }

        .footer-col ul {
            list-style-type: none;
            padding: 0;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col a {
            text-decoration: none;
            color: #fff;
            transition: color 0.3s ease;
        }

        .footer-col a:hover {
            color: #ffd700;
        }

        /* Media query for responsiveness */
        @media screen and (max-width: 768px) {
            .footer-col {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="services.php">Our Services</a></li>
                        <li><a href="pp.php">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="tac.php">Terms and Conditions</a></li>
                        <li><a href="return.php">Return & Refund Policy</a></li>
                        <li><a href="contactus.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Online Shop</h4>
                    <ul>
                        <li><a href="skin.php">Skin Care</a></li>
                        <li><a href="hair.php">Hair Care</a></li>
                        <li><a href="lip.php">Lip Care</a></li>
						 <li><a href="body.php">body Care</a></li>
                        <li><a href="hotel.php">Hotel Toiletries</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Connect with Us</h4>
                    <ul>
                        <li><a href="tel:+919344368388">+91 9344 368 388</a></li>
                        <li><a href="mailto:admin@hgstore.in">Admin@hgstore.in</a></li>
                        <li><a href="#">Navalur, OMR Chennai</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>