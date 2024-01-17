<?php
include "db_connection.php";
require 'jpgraph/src/jpgraph.php';
require 'jpgraph/src/jpgraph_bar.php';

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
<html>
   <head>
      <title>
         Diagrama activitatilor
      </title>
   </head>
<body>
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
$sql = "SELECT nume, count(id_user) as useri from Participare p, Activitate a 
where p.id_activ = a.id_activ GROUP by p.id_activ, nume having count(id_user)>0;";
$result = $conn->query($sql);
$num_results = $result->num_rows;
$joburi = array();
$angajati = array();
for ($i=0; $i <$num_results; $i++) {
   $row = $result->fetch_assoc();
   array_push($joburi, $row["nume"].' ');
   array_push($angajati,intval($row["useri"]));
   echo 'Activitate '.$row["nume"]."   ".$row["useri"].'# ';}

$nr = rand(1, 10000);
$fimg = "jpgraph-bars$nr.png";

// $data =[40,60,25,34];

$graph = new Graph(600,700, 'auto');

// $theme_class= new VividTheme;
// $graph->SetTheme($theme_class);
// $graph->SetShadow();
$graph->SetScale("textlin");

$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);

$graph->xaxis->SetTickLabels($joburi);
#$graph->xaxis->SetTextLabelInterval(10);
$bplot = new BarPlot($angajati);
$graph->Add($bplot);


// $p1 = new PiePlot3D($angajati);
// $p1->ExplodeSlice(1);
// $p1->SetCenter(0.5);
// $p1->SetLegends($joburi);
$graph->title->Set("Bar Plots");

$graph->SetMargin(40,80,40,40);
$graph->legend->Pos(0.05,0.5, 'right', 'center');
$graph->legend->SetColumns(1);

// Display the graph
$graph->Stroke($fimg);

if(file_exists($fimg)) echo '<img src="'. $fimg .'" />';
else echo 'Unable to create: '. $fimg;
$_SESSION['fimg'] = $fimg;
?>

<br>
<a href="print.php">Printati schema</a>
</body>
</html>