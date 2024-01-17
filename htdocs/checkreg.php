<?php
include "db_connection.php";

if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){ 
    // Google reCAPTCHA API secret key 
    $secret_key = '6LfIE04pAAAAAG8oW4GXUiBd4GviyDd4IwwpPbQE'; 
     
    // reCAPTCHA response verification
    $verify_captcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']); 
    
    // Decode reCAPTCHA response 
    $verify_response = json_decode($verify_captcha); 
     
    // Check if reCAPTCHA response returns success 
    if(!$verify_response->success){
        echo "Nu s-a putut verifica reCAPTCHA!";
        exit();
    }
}
else{
    echo "Trebuie sa bifati reCAPTCHA!";
    exit();
}


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
$sql = "SELECT 1 FROM User WHERE cnp = ? OR email = ? OR telefon = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $cnp, $email, $tel);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows > 0){
    echo '<p>Utilizatorul deja exista!</p>';
    exit();
}
$stmt->close();

$conn->begin_transaction();
//inseram noul utilizator in tabelul User, respectiv in tabelul asociat categoriei de utilizator
$insertUserQuery = "INSERT INTO User(nume, prenume, cnp, data_n, email, telefon, parola) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmtUser = $conn->prepare($insertUserQuery);
$stmtUser->bind_param("sssssss", $nume, $prenume, $cnp, $data_n, $email, $tel, $parola);
$stmtUser->execute();
$id_user = $conn->insert_id;
$stmtUser->close();

//STUDENT
if($tip_user === 'student'){
    $grupa = intval(filter_input(INPUT_POST, 'grupa', FILTER_SANITIZE_NUMBER_INT));
    try{
        $insertStudentQuery = "INSERT INTO Student(id_stud, grupa) VALUES (?, ?)";
        $stmtStudent = $conn->prepare($insertStudentQuery);
        $stmtStudent->bind_param("ii", $id_user, $grupa);
       
        if (!$stmtStudent->execute()) {
            $conn->rollback();
            echo "Eroare8!";
            exit(1);
        }
        $stmtStudent->close();
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
        $insertProfesorQuery = "INSERT INTO Profesor(id_prof) VALUES (?)";
        $stmtProfesor = $conn->prepare($insertProfesorQuery);
        $stmtProfesor->bind_param("i", $id_user);
        if (!$stmtProfesor->execute()) {
            $conn->rollback();
            echo "Eroare9!";
            exit(1);
        }
        $stmtProfesor->close();
        echo "OK1";
        if (isset($_POST['materii']) && is_array($_POST['materii'])) {
            $selectedMaterii = $_POST['materii'];
            foreach ($selectedMaterii as $selectedValue) {
                $selectMaterieQuery = "SELECT id_materie FROM Materie WHERE denumire = ?";
                $stmtSelectMaterie = $conn->prepare($selectMaterieQuery);
                $stmtSelectMaterie->bind_param("s", $selectedValue);
                $stmtSelectMaterie->execute();
                $resultSelectMaterie = $stmtSelectMaterie->get_result();                
                
                if ($resultSelectMaterie->num_rows != 1) {
                    $conn->rollback();
                    echo "Eroare9.1!";
                    exit(1);
                }

                echo "OK1";
                $stmtSelectMaterie->close();

                $id_materie = $resultSelectMaterie->fetch_assoc()['id_materie'];
                $insertPredareQuery = "INSERT INTO Predare(id_prof, id_materie) VALUES (?, ?)";
                $stmtPredare = $conn->prepare($insertPredareQuery);
                $stmtPredare->bind_param("ii", $id_user, $id_materie);                
                echo "OK1";

                if (!$stmtPredare->execute()) {
                    $conn->rollback();
                    echo "Eroare9.2!";
                    exit(1);
                }
                $stmtPredare->close();
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
        $mess = $e->getMessage();
        echo $mess;
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
        $insertCandidatQuery = "INSERT INTO Candidat(id_candidat, nr_dosar, domeniu) VALUES (?, ?, ?)";
        $stmtCandidat = $conn->prepare($insertCandidatQuery);
        $stmtCandidat->bind_param("iis", $id_user, $nr_dosar, $domeniu);
        
        if (!$stmtCandidat->execute()) {
            echo "Eroare10!";
            $conn->rollback();
            exit(1);
        }
        $stmtCandidat->close();
    }
    catch (Exception $e){
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

