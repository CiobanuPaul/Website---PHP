<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db_connection.php";
$email = $_POST['email'];
$parola = $_POST['password'];
$res = $conn->query("select * from User where email = '$email' and parola = '$parola';");

if($res->num_rows !== 1){
    echo '<p>Email sau parola gresita<p>';
    exit();
}

session_start();
$row = $res->fetch_assoc();
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