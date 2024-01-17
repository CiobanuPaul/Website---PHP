<?php
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
        <h1>Activitatile facultatii</h1>
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
    // Extragere activitati disponibile
    $sql = "SELECT * FROM Activitate";
    $result = $conn->query($sql);

    // Verificam daca avem rezultate
    if ($result->num_rows > 0) {
        // Afișam activitatile disponibile pentru toate tipurile de utilizatori
        echo "<h2>Activitati disponibile pentru toti utilizatorii:</h2>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            if($row['tip'] != 'oricine')
                continue;
            echo "<li>{$row['nume']} <p>{$row['descriere']}</p></li>";
            // Verificam daca utilizatorul nu participa la aceasta activitate
            $id_activitate = $row['id_activ'];

            $sql_participare = "SELECT * FROM Participare WHERE id_user = ? AND id_activ = ?";
            $stmt_participare = $conn->prepare($sql_participare);
            $stmt_participare->bind_param("ii", $id_user, $id_activitate);
            $stmt_participare->execute();
            $result_participare = $stmt_participare->get_result();

            if ($result_participare->num_rows == 0) {
                // Daca studentul nu participa la activitate, afișam link catre formularul de înscriere
                echo "<a href='formular_inscriere.php?id_activitate={$row['id_activ']}'>Înscrie-te</a>";
            }
            else{
                echo "<p style='color:green;'>Inscris</p>";
            } 
        }
        $stmt_participare->close();
        echo "</ul>";

        // Afișam activitatile disponibile pentru tipul specific de utilizator
        echo "<h2>Activitati disponibile pentru $tip_user:</h2>";
        echo "<ul>";
        $result->data_seek(0); // Resetam pointer-ul rezultatului pentru a parcurge din nou
        while ($row = $result->fetch_assoc()) {
            if ($row['tip'] != $tip_user) {
                continue;
            }
            echo "<li>{$row['nume']} <p>{$row['descriere']}</p></li>";
            $id_activitate = $row['id_activ'];

            $sql_participare = "SELECT * FROM Participare WHERE id_user = ? AND id_activ = ?";
            $stmt_participare = $conn->prepare($sql_participare);
            $stmt_participare->bind_param("ii", $id_user, $id_activitate);
            $stmt_participare->execute();
            $result_participare = $stmt_participare->get_result();
            
            if ($result_participare->num_rows == 0) {
                // Daca studentul nu participa la activitate, afișam link catre formularul de înscriere
                echo "<a href='formular_inscriere.php?id_activitate={$row['id_activ']}'>Înscrie-te</a>";
            }  
            else{
                echo "<p style='color:green;'>Inscris</p>";
            } 
        }
        $stmt_participare->close();
        echo "</ul>";
    } else {
        echo "Nu exista activitati disponibile în acest moment.";
    }
    $conn->close();
?>

<a href="diagrama.php">Statistici participare</a>
</body>
<footer>
        <p>&copy; 2023 Facultatea de Matematică și Informatică | Toate drepturile rezervate</p>
</footer>
</html>