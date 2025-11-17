<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $to = "hr@codewarriorlabs.com";  
    $subjectLine = "New Contact Message: $subject";

    $body = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

    $headers = "From: $email";

if (mail($to, $subjectLine, $body, $headers)) {
    header("Location: thankyou.html");
    exit();
} else {
    echo "Error: Message Failed!";
}


}
?>
