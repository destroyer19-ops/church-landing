<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // In a real application, you would send an email here.
    // For now, we will just display the submitted data.

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Thank You - Christ Embassy Love Church  Barking</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link rel='stylesheet' href='css/style.css'>
</head>
<body class='bg-gray-100 text-gray-800'>
    <div class='container mx-auto px-6 py-20 text-center'>
        <h1 class='text-4xl font-extrabold text-gray-800 mb-4'>Thank You, $name!</h1>
        <p class='text-lg text-gray-600 mb-4'>Your message has been received. We will get back to you at $email shortly.</p>
        <p class='text-lg text-gray-600'><strong>Your message:</strong><br>$message</p>
        <a href='index.html' class='mt-8 inline-block bg-red-700 text-white py-3 px-6 rounded-full text-lg font-semibold hover:bg-red-800 transition duration-300'>Go back to the Home Page</a>
    </div>
</body>
</html>";
} else {
    header("Location: contact.html");
}
?>