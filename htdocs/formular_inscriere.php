<?php
    include "db_connection.php";
    require ("fpdf/fpfd.php");
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
        Formular inscriere
    </title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
</head>
<body>
    <header>
        <h1>Inscriere activitate</h1>
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
        $res = $conn->query("Select * from Activitate where tip='$tip_user' or tip='oricine';");
        $activitati = $res->fetch_all(MYSQLI_ASSOC);

        // Verifică dacă s-a trimis formularul
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_activitate = $_POST['id_activitate'];
            $res = $conn->query("select * from Participare p where p.id_activ=$id_activitate and id_user=$id_user;");
            if($res->num_rows > 0){
                echo "Sunteti deja inscris la aceasta activitate!";
                exit();
            }
            $res = $conn->query("select * from Activitate where id_activ=$id_activitate;");
            $row = $res->fetch_assoc();
            $tip = $row['tip'];
            if($tip != $tip_user && $tip != 'oricine'){
                echo "Eroare!";
                exit(1);
            }
            $res = $conn->query("insert into Participare values($id_user, $id_activitate);");
            if($res == false){
                echo 'Eroare!';
                exit(1);
            }
            require_once('phpmailer/class.phpmailer.php');
			require_once('phpmailer/mail_config.php');
			
			$mailBody = "Ati completat formularul de inscriere la activitatile facultatii de matematica si informatica! \n";
            $mailBody .= "Activitate: " . $row['nume'] . ". \n";
			$mailBody .= "Descriere: \n";
			$mailBody .= $row['descriere'];
			$mailBody .= "  \nVa asteptam!";

			$mail = new PHPMailer(true); 
			$mail->IsSMTP();

            try {
				 
                $mail->SMTPDebug  = 0;                     
                $mail->SMTPAuth   = true; 

                $toEmail=$email;
                $numeFrom='FMI';

                $mail->SMTPSecure = "ssl";                 
                $mail->Host       = "smtp.gmail.com";      
                $mail->Port       = 465;                   
                $mail->Username   = $username;  			// GMAIL username
                $mail->Password   = $password;            // GMAIL password
                // $mail->AddReplyTo('ciobanuioanpaul43@gmail.com', 'DAW - project');
                $mail->AddAddress($toEmail, $nume);
                // $mail->addCustomHeader("BCC: ".$email);
               
                $mail->SetFrom($email, $numeFrom);
                $mail->Subject = 'Inscriere activitate FMI';
                $mail->AltBody = 'To view this post you need a compatible HTML viewer!'; 
                $mail->MsgHTML($mailBody);
                
                $mail->Send();
                
                $returnMsg = 'Your message has been submitted successfully.'; 
                
              }
               catch (phpmailerException $e) {
                                                echo $e->errorMessage(); //error from PHPMailer
                                              }
                
            echo "V-ati inscris cu succes! Ati primit mail de confirmare.";
            exit();
        }
    ?>


    <form method="post">
            <label for="id_activitate">Selectează activitatea:</label>
            <select name="id_activitate" id="id_activitate">
                <?php foreach ($activitati as $row) : ?>
                    <option value="<?php echo $row['id_activ']; ?>" 
                        <?php if(isset($_GET['id_activitate']) && $row['id_activ'] == $_GET['id_activitate'])
                            {echo 'selected';} 
                        ?>
                    >
                        <?php echo $row['nume']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <br>

            <label>
                <input type="checkbox" name="participare" value="Da" required> Particip
            </label>

            <br>

            <input type="submit" value="Trimite">
    </form>
</body>
</html>
    