<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
    include "db_connection.php";
    session_start();
    if(isset($_SESSION['id_user'], $_SESSION['nume'])){
        $id_user = $_SESSION['id_user'];
        $nume = $_SESSION['nume'];
        $email = $_SESSION['email'];
        $tip_user = $_SESSION['tip_user'];
    }
    else{
        header("Location: https://paul-ciobanu.42web.io/signin.html");
        exit();
    }
echo "Buna $nume!!";
?>
<!doctype html>
<html lang="ro">
<head>
    <title>
        Activități-FMI
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <nav>
        <div>Acasa</div>
        <div>Inregistrare</div>
        <div><a href="signout.php">Delogare</a></div>
    <nav>
    
<main>
    <h2>Activitățile Facultății de Matematica și Informatică</h2>
    
    <p>Site-ul va permite inregistrarea si autentificarea a trei tipuri de utilizatori: studenti, profesori si candidati.</p>
    <p>Studentii vor avea acces la informatii despre orar, formatiuni de studii, si diverse activitati ce sunt realizate 
    exclusiv pentru pentru studenti sau si pentru studenti si pentru profesori. </p>
    <p>Profesorii vor avea acces la informatii asemanatoare, dar specifice profesorilor sau valabile si pentru studenti si pentru profesori.</p>
    <p>Candidatii nu sunt inmatriculati, dar ar si-au depus dosar pentru admitere. Acestia vor primi in continuare
    pe site informatii despre procesul de admitere</p>
    <p>Pentru fiecare activitate va exista un formular ce permite inscrierea unui utilizator la activitate.</p>
    
    <p>Aceasta este diagrama veche.</p>
    <img src="diag.png"></img>

    <p>Pe diagrama noua, acum exista si un tabel Materie.</p>
    <p>Intre Materie si Profesor exista tabelul asociativ Predare</p>
    

</main>
</body>
</html>
