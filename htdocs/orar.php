<?php
    session_start();
        if(isset($_SESSION['id_user'], $_SESSION['nume'])){
            $id_user = $_SESSION['id_user'];
            $nume = $_SESSION['nume'];
            $email = $_SESSION['email'];
            $tip_user = $_SESSION['tip_user'];
            if($tip_user != 'student'){
                echo "Nu esti student!";
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
    <title>Orarul Studentului</title>
    <!-- Adaugă aici link-uri către stilurile tale CSS -->
</head>
<body>

    <header>
        <h1>Orar</h1>
        <p>Orarul cu materiile pentru semestrul curent.</p>
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
        

    <!-- Orarul pentru student -->
    <section id="orar-student">
        <table border=1>
            <tr>
                <th>Ora</th>
                <th>Luni</th>
                <th>Marți</th>
                <th>Miercuri</th>
                <th>Joi</th>
                <th>Vineri</th>
            </tr>
            <tr>
                <td>08:00 - 09:30</td>
                <td>Matematica</td>
                <td>Informatica</td>
                <td></td>
                <td></td>
                <td>Algebra Liniara</td>
            </tr>
            <tr>
                <td>10:00 - 11:30</td>
                <td>Programare Web</td>
                <td></td>
                <td>Analiza Matematica</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>12:00 - 13:30</td>
                <td></td>
                <td>Inteligenta Artificiala</td>
                <td></td>
                <td>Algoritmi si Structuri de Date</td>
                <td></td>
            </tr>
            <!-- Adaugă aici mai multe rânduri pentru restul zilelor și orelor -->
        </table>
    </section>

    <!-- Aici poți adăuga orice alte secțiuni specifice paginii de orar -->

    <!-- Subsolul paginii -->
    <footer>
        <p>&copy; 2023 Facultatea de Matematică și Informatică | Toate drepturile rezervate</p>
    </footer>

</body>
</html>