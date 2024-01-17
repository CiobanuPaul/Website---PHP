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
        $fimg = $_SESSION['fimg'];
    }
    else{
        header("Location: https://paul-ciobanu.42web.io/signin.html");
        exit();
    }

require('fpdf/fpdf.php');
?>
<html>
    <head>
        <title>
            Statistici diagrama
        </title>    
</head>
<body></body>
</html>

<?php
class PDF extends FPDF
{

	protected $widths;
    protected $aligns;
	protected $min_nb;

    function SetWidths($w)
    {
        // Set the array of column widths
        $this->widths = $w;
    }

	function Setnb($m){
		$this->min_nb = $m;
	}


    function SetAligns($a)
    {
        // Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		if($this->min_nb > $nb)
			$nb = $this->min_nb;
        $h = 4*$nb;
        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        // Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            // Draw the border
            $this->Rect($x,$y,$w,$h);
            // Print the text
            $this->MultiCell($w,4,$data[$i],0,$a);
            // Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        // Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',(string)$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }






// Page header
function Header()
{}

// Page footer
function Footer()
{}




function section1(){
	$centru=120;
    global $conn;
    $sql = "SELECT nume, count(id_user) as useri from Participare p, Activitate a 
        where p.id_activ = a.id_activ GROUP by p.id_activ, nume having count(id_user)>0;";
    $result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
        // Adjust the following code based on your table structure
        $this->SetFont('Arial','B',10);

        $this->Cell(70, 10, $row['nume'], 1);
        $this->Cell(70, 10, $row['useri'], 1);
        $this->Ln(); // Move to the next line
    }
}




}


$pdf = new PDF('P','mm','A4');

//A4 => w:210 h: 297
$left=9;
$right=9;
$pdf->SetLeftMargin($left);
$pdf->SetRightMargin($right);
$centru=192;
$pdf->AddPage();
$pdf->section1();
// $pdf->createTable();
$pdf->SetFont('Times','',12);

// $pdf->AddPage();
// Specify image file path, X and Y coordinates, width, and height
$x = 10;
$y = 130;
$width = 110;
$height = 0;  // 0 means auto height based on the aspect ratio

// Add image to PDF
$pdf->Image($fimg, $x, $y, $width, $height);

// Output the PDF
$pdf->Output();

?>
