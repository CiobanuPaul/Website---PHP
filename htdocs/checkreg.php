<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db_connection.php";
//de inceput site-ul propriu zis

//sanitizing inputs
$tip_user = $_POST['categorie'];
$email =filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$nume =filter_input(INPUT_POST, 'nume', FILTER_SANITIZE_STRING);
$prenume =filter_input(INPUT_POST, 'prenume', FILTER_SANITIZE_STRING);
$parola_raw =filter_input(INPUT_POST, 'parola', FILTER_SANITIZE_STRING);
$cparola =filter_input(INPUT_POST, 'cparola', FILTER_SANITIZE_STRING);
$cnp =filter_input(INPUT_POST, 'cnp', FILTER_SANITIZE_NUMBER_INT);
$tel =filter_input(INPUT_POST, 'telefon', FILTER_SANITIZE_NUMBER_INT);
$data_n =filter_input(INPUT_POST, 'data_n', FILTER_SANITIZE_STRING);

if ($email===false || $nume===false || $prenume===false || $parola_raw===false
   || $cparola===false || $cnp===false || $tel===false || $data_n===false)
    {
        echo 'Eroare1!';
        exit(1);
    }

if($parola_raw !== $cparola){
    echo '<p>Parola nu coincide cu parola confirmata!</p>';
    exit();
}
$parola = password_hash($parola_raw, PASSWORD_BCRYPT);


//verificam daca tipul emailului coincide cu tipul de utilizator
$pos = strrpos($email, '@');
if($pos !== false)
    $email_type = substr($email, $pos + 1);
else{
    echo 'Eroare3!';
    exit(1);
}

if($tip_user == 'student' && $email_type != 's.unibuc.ro'){
    echo 'Nu aveti adresa de email de student!';
    exit(1);
}
if($tip_user == 'profesor' && $email_type != 'fmi.unibuc.ro'
                            && $email_type != 'unibuc.ro'){
    echo 'Nu aveti adresa de email de profesor!';
    exit(1);
}


//verificam daca utilizatorul deja exista
$res = $conn->query("select 1 from User where cnp = '$cnp' or email='$email' or telefon='$tel'");
if($res->num_rows > 0){
    echo '<p>Utilizatorul deja exista!</p>';
    exit();
}


$conn->begin_transaction();
//inseram noul utilizator in tabelul User, respectiv in tabelul asociat categoriei de utilizator
$res = $conn->query("insert into User(nume, prenume, cnp, data_n, email, telefon, parola) values('$nume', '$prenume', '$cnp', '$data_n', '$email', '$tel', '$parola')");
if($res === FALSE){
    $conn->rollback();
    echo "Eroare7!";
    exit(1);
}
$id_user = $conn->insert_id;

//STUDENT
if($tip_user === 'student'){
    $grupa = intval(filter_input(INPUT_POST, 'grupa', FILTER_SANITIZE_NUMBER_INT));
    try{
        $res = $conn->query("insert into Student(id_stud, grupa) values('$id_user', '$grupa')");
        if($res === FALSE){
            $conn->rollback();
            echo "Eroare8!";
            exit(1);
        }
    }
    catch (Exception $e){
        $conn->rollback();
        echo "Eroare8: verificati corectitudinea datelor!";
        exit(1);
    }
}


//PROFESOR
else if($tip_user === 'profesor'){
    try{
        $res = $conn->query("insert into Profesor(id_prof) values('$id_user')");
        if($res === FALSE){
            $conn->rollback();
            echo "Eroare9!";
            exit(1);
        }

        if (isset($_POST['materii']) && is_array($_POST['materii'])) {
            $selectedMaterii = $_POST['materii'];
            foreach ($selectedMaterii as $selectedValue) {
                $res = $conn->query("select id_materie from Materie where denumire='$selectedValue'");
                if($res->num_rows != 1){
                    $conn->rollback();
                    echo "Eroare9.1!";
                    exit(1);
                }
                $id_materie = $res->fetch_assoc()['id_materie'];
                $res = $conn->query("insert into Predare(id_prof, id_materie) values('$id_user', '$id_materie')");
                if($res === FALSE){
                    $conn->rollback();
                    echo "Eroare9.2!";
                    exit(1);
                }
            }
        }
        else{
            echo 'Nu ati selectat materii!';
            $conn->rollback();
            exit();
        }
    }
    catch (Exception $e){
        $conn->rollback();
        echo "Eroare9: verificati corectitudinea datelor!";
        exit(1);
    }
}


//CANDIDAT
else if($tip_user === 'candidat'){
    $nr_dosar = intval(filter_input(INPUT_POST, 'nr_dosar', FILTER_SANITIZE_NUMBER_INT));
    $domeniu = $_POST['domeniu'];

    if($domeniu != 'informatica' && $domeniu != 'matematica' && $domeniu != 'cti'){
        echo "Eroare9.5!";
        $conn->rollback();
        exit(1);
    }
    try{
        $res = $conn->query("insert into Candidat(id_candidat, nr_dosar, domeniu) values('$id_user', '$nr_dosar', '$domeniu')");
        if($res === FALSE){
            echo "Eroare10!";
            $conn->rollback();
            exit(1);
        }
    }
    catch (Exception $e){
        $conn->rollback();
        $conn->rollback();
        echo "Eroare10: verificati corectitudinea datelor!";
        exit(1);
    }
}
else{
    echo "Eroare11!";
    $conn->rollback();
    exit(1);
}
$conn->commit();
header("Location: https://paul-ciobanu.42web.io/signin.html");
exit();

?>

