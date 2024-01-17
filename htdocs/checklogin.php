<?php
include "db_connection.php";
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$parola = filter_input(INPUT_POST, 'parola', FILTER_SANITIZE_STRING);
$stmt = $conn->prepare("SELECT * FROM User WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows !== 1){
    echo '<p>Email sau parola gresita<p>';
    exit();
}
else{
    $row = $res->fetch_assoc();
    $parola_stocata = $row['parola'];
    if(!password_verify($parola, $parola_stocata)){
        echo '<p>Email sau parola gresita<p>';
        exit();
    }
}
$stmt->close();


session_start();
$_SESSION['id_user'] = $row['id_user'];
$_SESSION['nume'] = $row['nume'];
$_SESSION['email'] = $row['email'];
$id_user = $row['id_user'];

$res1 = $conn->query("select * from Student where id_stud = '$id_user';");
if($res1->num_rows){
    $_SESSION['tip_user'] = 'student';
}
else{
    $res2 = $conn->query("select * from Profesor where id_prof = '$id_user';");
    if($res2->num_rows){
        $_SESSION['tip_user'] = 'profesor';
    }
    else{
        $res3 = $conn->query("select * from Candidat where id_candidat = '$id_user';");
        if($res3->num_rows){
            $_SESSION['tip_user'] = 'candidat';
        }
        else{
            echo 'Eroare!';
            exit(1);
        }
    }
}
header("Location: https://paul-ciobanu.42web.io/home.php");
?>