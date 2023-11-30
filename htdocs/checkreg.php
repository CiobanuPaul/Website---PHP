<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db_connection.php";
$tip_user = $_POST['categorie'];
//de verificat daca tipul de email este cel potrivit categoriei

$email =$_POST['email'];
$nume =$_POST['nume'];
$prenume =$_POST['prenume'];
$parola =$_POST['parola'];
$cparola =$_POST['cparola'];
$cnp =$_POST['cnp'];
$tel =$_POST['telefon'];
$data_n =$_POST['data_n'];

if($parola !== $cparola){
    echo 'Eroare!';
    exit(1);
}
// echo $data_n;


$res = $conn->query("insert into User(nume, prenume, cnp, data_n, email, telefon, parola) values('$nume', '$prenume', '$cnp', '$data_n', '$email', '$tel', '$parola')");
if($res === FALSE){
    echo "Eroare!";
    exit(1);
}
$id_user = $conn->insert_id;
if($tip_user === 'student'){
    $grupa = $_POST['grupa'];
    $res = $conn->query("insert into Student(id_stud, grupa) values('$id_user', '$grupa')");
    if($res === FALSE){
        echo "Eroare!";
        exit(1);
    }
}
else if($tip_user === 'profesor'){
    $res = $conn->query("insert into Profesor(id_prof) values('$id_user')");
    if($res === FALSE){
        echo "Eroare!";
        exit(1);
    }
}
else if($tip_user === 'candidat'){
    $nr_dosar = $_POST['nr_dosar'];
    $domeniu = $_POST['domeniu'];
    $res = $conn->query("insert into Candidat(id_candidat, nr_dosar, domeniu) values('$id_user', '$nr_dosar', '$domeniu')");
    if($res === FALSE){
        echo "Eroare!";
        exit(1);
    }
}
else{
    echo "Eroare!";
    exit(1);
}
//de inserat si in subtabel
header("Location: https://paul-ciobanu.42web.io/signin.html");
exit();

?>

