<?php
    include "db_connection.php";
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
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
        Date personale
    </title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
</head>
<body>
    <header>
        <h1>Date personale</h1>
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

<?php
    $res = $conn->query("select * from User where id_user = $id_user;");
    $info = $res->fetch_assoc();
    echo "<h3>Informatii despre utilizatorul {$info['nume']} {$info['prenume']}</h3>";
    echo "<ul>";
    echo "<li><b>Categorie:</b> $tip_user</li>";
    echo "<li><b>Email:</b> $email</li>";
    echo "<li><b>Data nasterii:</b> {$info['data_n']}</li>";
    echo "<li><b>Telefon:</b> {$info['telefon']}</li>";
    echo "<li><b>CNP:</b> {$info['cnp']}</li>";

    if($tip_user == 'student'){
        $res = $conn->query("select * from Student where id_stud = $id_user;");
        $info2 = $res->fetch_assoc();
        echo "<li><b>Grupa:</b> {$info2['grupa']}</li>";

    }
    else if($tip_user == 'profesor'){
        $res = $conn->query("select * from Predare p, Materie m where id_prof = $id_user and p.id_materie=m.id_materie;");
        if($res->num_rows > 0){
            echo "<li>Materii predate:<ul>";
        }
        else{
            echo "<li>Nu preda nicio materie.<ul>";
        }
        while($info2 = $res->fetch_assoc()){
            echo "<li>{$info2['denumire']}</li>";
        }
        echo "</ul></li>";
    }
    else if($tip_user == 'candidat'){
        $res = $conn->query("select * from Candidat where id_candidat = $id_user;");
        $info2 = $res->fetch_assoc();
        echo "<li><b>Nr dosar:</b> {$info2['nr_dosar']}</li>";
        echo "<li><b>Domeniu:</b> {$info2['domeniu']}</li>";
    }
?>

    </body>
</html>
