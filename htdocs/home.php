<?php //de vazut daca sa elimin verificarea tipului de user la orar
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
?>
<!doctype html>
<html lang="ro">
<head>
    <title>
        Activități-FMI
    </title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
</head>
<body>
    <header>
        <h1>Bun venit pe platforma noastră, <?php echo $nume; ?>!</h1>
        <p>Aceasta este platforma online a Facultății de Matematică și Informatică. Aici vei găsi informații utile, resurse academice și vei putea participa la diverse activități organizate de facultate.</p>
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
            <li><a href="openai.php">Asistenta chat 24/7</a></li>
            <li><a href="signout.php">Deconectare</a></li>
        </ul>
    <nav>
    
<main>

    <section id="despre-facultate">
        <h2>Despre Facultate</h2>
        <p>Facultatea noastră se remarcă prin excelența în educație și cercetare în domeniile matematicii și informaticii. Suntem dedicați dezvoltării tale academice și profesionale.</p>
        <p>Descoperă resursele noastre, evenimentele speciale și participă la activitățile care vor îmbogăți experiența ta universitară.</p>
    </section>

</main>
    <footer>
        <p>&copy; 2023 Facultatea de Matematică și Informatică | Toate drepturile rezervate</p>
    </footer>

</body>
</html>
