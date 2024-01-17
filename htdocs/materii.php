<?php
    ini_set('display_errors', 1);
    session_start();
    if(isset($_SESSION['id_user'], $_SESSION['nume'])){
        $id_user = $_SESSION['id_user'];
        $nume = $_SESSION['nume'];
        $email = $_SESSION['email'];
        $tip_user = $_SESSION['tip_user'];
        if($tip_user != 'profesor'){
            echo "Nu esti profesor!";
            exit();
        }
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
        Materii predate
    </title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
</head>
<body>
    <header>
        <h1>Materii predate</h1>
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
        $res = $conn->query("select * from Predare p, Materie m where id_prof = $id_user and p.id_materie=m.id_materie;");
        if($res->num_rows > 0){
            echo "<p>Materii predate:</p><ul>";
        }
        else{
            echo "<li>Nu preda nicio materie.<ul>";
        }
        while($info2 = $res->fetch_assoc()){
            echo "<li>{$info2['denumire']}: an {$info2['an']}, {$info2['domeniu']}</li>";
        }
        echo "</ul>";
    ?>

</body>
</html>