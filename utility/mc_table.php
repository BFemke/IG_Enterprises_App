<?php
require('fpdf.php');

class PDF_MC_Table extends FPDF
{
	protected $widths;
	protected $aligns;

	private $employee_name;
	private $employee_id;


	function SetName($name)
	{
		// Set the name of the employee
		$this->employee_name = $name;
	}

	function SetID($id)
	{
		// Set the id of the employee
		$this->employee_id = $id;
	}

	function SetWidths($w)
	{
		// Set the array of column widths
		$this->widths = $w;
	}

	function SetAligns($a)
	{
		// Set the array of column alignments
		$this->aligns = $a;
	}

	function AddSpacer($h){
		$this->Ln($h);
	}

	function Row($data, $fillColour)
	{
		//styles
		// Color and font restoration
        $this->SetTextColor(0);
		$this->SetDrawColor(28, 28, 28);
        $this->SetLineWidth(.3);
        $this->SetFont('');
		$this->SetFontSize(12);
		//$this->$colourRow = $this->$colourRow === false ? true : false;

		// Calculate the height of the row
		$nb = 0;
		for($i=0;$i<count($data);$i++)
			$nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h = 7*$nb;
		// Issue a page break first if needed
		$this->CheckPageBreak($h);
		// Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			//Alternate row colours
			if(!$fillColour){
				$this->SetFillColor(252, 252, 252);
			}else{
				$this->SetFillColor(232, 232, 232);
			}

			$w = $this->widths[$i];
			$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			// Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			// Draw the border
			$this->Rect($x,$y,$w,$h, 'F');
			// Print the text
			$this->MultiCell($w,7,$data[$i],0,$a, true);
			// Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		// Go to the next line
		$this->Ln($h);
	}

	function TableHeader($data){
		//styles
		// Colors, line width and bold font
        $this->SetFillColor(77, 77, 77);
        $this->SetTextColor(255);
        $this->SetDrawColor(28, 28, 28);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
		$this->SetFontSize(14);

		// Calculate the height of the row
		$nb = 0;
		for($i=0;$i<count($data);$i++)
			$nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h = 7*$nb;
		// Issue a page break first if needed
		$this->CheckPageBreak($h);
		// Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w = $this->widths[$i];
			$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
			// Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			// Draw the border
			$this->Rect($x,$y,$w,$h, 'F');
			// Print the text
			$this->MultiCell($w,7,$data[$i],0,$a, true);
			// Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		// Go to the next line
		$this->Ln($h);
	}

	function SummarizeData($data){
	$sumData = array(0, 0, 0);

		//adds up all the hours, overtime hours, and standard hours
		foreach($data as $row){
			$sumData[0] += $row['total_hours'];
			$overtime = $row['total_hours'] > 8 ? $row['total_hours'] - 8 : 0;
			$sumData[1] += $row['total_hours'] - $overtime;
			$sumData[2] += $overtime;

		}

		return $sumData;
	}

	function SummaryTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(77,77,77);
        $this->SetTextColor(255);
        $this->SetDrawColor(28,28,28);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
		$this->SetFontSize(14);
        // Header
        $w = array(62, 62, 62);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(232, 232, 232);
        $this->SetTextColor(0);
        $this->SetFont('');
		$this->SetFontSize(12);
        // Data
        $this->Cell($w[0],6,$data[0],'LR',0,'C', true);
        $this->Cell($w[1],6,$data[1],'LR',0,'C', true);
        $this->Cell($w[2],6,$data[2],'LR',0,'C', true);
        $this->Ln();
        
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
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

	//loads all timesheets for particular employee within a particular time range
	function loadData($db, $employee_id, $from_date, $to_date){

        try{
            $query = 'SELECT t.*, e.last_name, e.first_name
                    FROM timesheet t
                    JOIN employee e ON t.employee_id = e.employee_id
                    WHERE t.employee_id = :employee_id AND work_date <= :to_date AND work_date >= :from_date
                    ORDER BY work_date ASC';
            $statement = $db->prepare($query);
            $statement->bindValue(':employee_id', $employee_id);
			$statement->bindValue(':from_date', $from_date);
			$statement->bindValue(':to_date', $to_date);
            $statement->execute();
            $timesheets = $statement->fetchAll();
            $statement->closeCursor();
        } catch (PDOException $e) {
            // Handle database error
            $error_message = $e->getMessage();
            include('../utility/database_error.php');
            exit();
        }

        return $timesheets;
    }

	function loadEmployee($db, $employee_id){
		try{
            $query = 'SELECT last_name, first_name, employee_id
                    FROM employee
                    WHERE employee_id = :employee_id
                    LIMIT 1';
            $statement = $db->prepare($query);
            $statement->bindValue(':employee_id', $employee_id);
            $statement->execute();
            $employee = $statement->fetch();
            $statement->closeCursor();
        } catch (PDOException $e) {
            // Handle database error
            $error_message = $e->getMessage();
            include('../utility/database_error.php');
            exit();
        }

		return $employee;
	}

    // Page header
    function Header()
    {
        // Logo
        $this->Image('../images/ig_logo.jpg',10, 6, 25);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(30);
        // Company
        $this->Cell(55, 10, 'IG Enterprises Ltd.', 0, 0, 'C');

		//prints employee information n
		$remainingWidth = $this->GetPageWidth() - $this->GetX() - 15;
		$this->Cell($remainingWidth, 6, $this->employee_name, 0, 1, 'R');
		$this->SetFontSize(12);
		$remainingWidth = $this->GetPageWidth() - $this->GetX() - 15;
		$this->Cell($remainingWidth, 6, $this->employee_id, 0, 0, 'R');
        // Line break
        $this->Ln(25);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

}
?>
