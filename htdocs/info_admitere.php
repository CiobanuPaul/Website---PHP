<?php
    session_start();
        if(isset($_SESSION['id_user'], $_SESSION['nume'])){
            $id_user = $_SESSION['id_user'];
            $nume = $_SESSION['nume'];
            $email = $_SESSION['email'];
            $tip_user = $_SESSION['tip_user'];
            if($tip_user != 'candidat'){
                echo "Nu esti candidat!";
                exit();
            }
        }
        else{
            header("Location: https://paul-ciobanu.42web.io/signin.html");
            exit();
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admitere</title>
</head>
<body>

    <header>
        <h1>Informatii admitere</h1>
    </header>

    <nav>
        <ul>
            <li><a href="activitati.php">Activități</a></li>
            <li><a href="date_personale.php">Date Personale</a></li>
            <?php
                if($tip_user == 'student')
                    echo '<li><a href="orar.php">Orar</a></li>';
                else if($tip_user == 'profesor')
                    echo '<li><a href="materii.php">Materii predate</a></li>';
                else
                    echo '<li><a href="info_admitere.php">Informatii admitere</a></li>';
            ?>
            <li><a href="signout.php">Deconectare</a></li>
        </ul>
    <nav>
    <p>Bun venit la pagina de informații despre admiterea la Facultatea de Matematică și Informatică. Aici veți găsi detalii importante despre procesul de admitere, criterii de selecție și termene relevante pentru admiterea din vara anului 2024.</p>

    <div>
        <h2>Termene importante pentru Admiterea 2024</h2>
        <p>1. Depunerea dosarelor: 1-15 iulie 2024</p>
        <p>2. Examenul de admitere: 25 iulie 2024</p>
        <p>3. Anunțarea rezultatelor: 1 august 2024</p>
    </div>

    <h2>Criterii de admitere</h2>
    <p>Admiterea la Facultatea de Matematică și Informatică în 2024 se va realiza pe baza următoarelor criterii:</p>
    <ul>
        <li>Medie generală la Bacalaureat</li>
        <li>Rezultatele obținute la examenul de admitere</li>
        <li>Interviul de admitere</li>
    </ul>

    <h2>Interviul de Admitere</h2>
    <p>În cadrul procesului de admitere, candidații vor susține un interviu care va evalua aptitudinile și motivația acestora pentru studiul la Facultatea de Matematică și Informatică. Pregătiți-vă pentru a discuta despre experiența dvs. în domeniul matematicii și informaticii și despre motivul pentru care doriți să urmați această facultate.</p>

    <footer>
        <p>Pentru informații suplimentare, vă rugăm să contactați secretariatul facultății.</p>
        <p>&copy; 2024 Facultatea de Matematică și Informatică</p>
    </footer>

</body>
</html>
