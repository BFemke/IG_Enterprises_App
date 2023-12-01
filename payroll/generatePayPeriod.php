<?php
/*
Author: Barbara Emke
Date:   November 14, 2023
*/

session_start();

require_once('../utility/mc_table.php');

require_once('../utility/database.php');

$download = isset($_GET['download']) ? true : false;

if($download){
  
        // Access sent information
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];
      
      	//Inform user of date inpput being required
      	if($from_date === "" || $to_date === ""){
         	$_SESSION['confirmation'] = "You must enter dates to generate a pay period.";
          	header("Location: view_payroll.php");
          	exit();
        }

        // Column headings
        $header = array('Date', 'Slip ID', 'Total Hours', 'Overtime Hours', 'Comments/Notes');
        $sumHeader = array('Total hours', 'Standard Rate', 'Overtime');

        try{
            $query = 'SELECT DISTINCT e.last_name, e.first_name, e.employee_id
                FROM employee e
                JOIN timesheet t ON e.employee_id = t.employee_id
                WHERE t.work_date BETWEEN :from_date AND :to_date';
            $statement = $db->prepare($query);
            $statement->bindValue(':from_date', $from_date);
            $statement->bindValue(':to_date', $to_date);
            $statement->execute();
            $employees = $statement->fetchAll();
            $statement->closeCursor();
        } catch (PDOException $e) {
            // Handle database error
            $error_message = $e->getMessage();
            include('../utility/database_error.php');
            exit();
        }
  
  		$pdf = new PDF_MC_Table();
        $pdf->AliasNbPages();

        //generates a pdf for each employee
        foreach($employees as $employee){

            $header = array('Date', 'Slip ID', 'Total Hours', 'Overtime Hours', 'Comments/Notes');
            $sumHeader = array('Total hours', 'Standard Rate', 'Overtime');

            //loads data and call function print table
            $data = $pdf->LoadData($db, $employee['employee_id'], $from_date, $to_date);

            //load Employee information
            $name = $employee['first_name'] . " " . $employee['last_name'];
            $pdf->SetName($name);
            $id = "Employee ID: ". $employee['employee_id'];
            $pdf->SetID($id);

            //add new pdf page
            $pdf->AddPage();
            $pdf->SetFont('Times','',12);
            $pdf->SetWidths(array(30, 22, 22, 23, 89));
            $pdf->TableHeader($header);
  
  			$fillColour = false;

            //prints out timesheet table
            foreach ($data as $row) {
                $overtime = $row['total_hours'] > 8 ? $row['total_hours'] - 8 : 0;
                $pdf->Row(array($row['work_date'], $row['slip_id'], $row['total_hours'], $overtime, $row['comment']), $fillColour);
                $fillColour = $fillColour === false ? true : false;
            }

            //prints out summary table
            $pdf->addSpacer(10);
            $sumData = $pdf->SummarizeData($data);

            //calls function to make summary table
            $pdf->SummaryTable($sumHeader,$sumData);
          
          	$filename = $employee['first_name'] . "_" . $employee['last_name'] . "(" . $to_date . ").pdf";

        }
  		$tempPdfFile = 'master_payPeriods.pdf';
        $pdf->Output("D",$tempPdfFile);

}
else{
    // Access sent information
    $employee_id = $_GET['employee_id'];
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
  
    //Inform user of date inpput being required
    if($from_date === "" || $to_date === ""){
         $_SESSION['confirmation'] = "You must enter dates to generate a pay period.";
         header("Location: view_payroll.php");
      exit();
     }

    ob_start();

    // Instanciation of inherited class
    $pdf = new PDF_MC_Table();
    $pdf->AliasNbPages();

    //loads data and call functio print table
    $data = $pdf->LoadData($db, $employee_id, $from_date, $to_date);

    //load Employee information
    $employee = $pdf->loadEmployee($db, $employee_id);
    $name = $employee['first_name'] . " " . $employee['last_name'];
    $pdf->SetName($name);
    $id = "Employee ID: ". $employee['employee_id'];
    $pdf->SetID($id);

    $pdf->AddPage();
    $pdf->SetFont('Times','',12);

    // Column headings
    $header = array('Date', 'Slip ID', 'Total Hours', 'Overtime Hours', 'Comments/Notes');

    $pdf->SetWidths(array(30, 22, 22, 23, 89));
    $pdf->TableHeader($header);

    $fillColour = false;

    foreach ($data as $row) {
        $overtime = $row['total_hours'] > 8 ? $row['total_hours'] - 8 : 0;
        $pdf->Row(array($row['work_date'], $row['slip_id'], $row['total_hours'], $overtime, $row['comment']), $fillColour);
        $fillColour = $fillColour === false ? true : false;
    }

    $pdf->addSpacer(10);

    $sumData = $pdf->SummarizeData($data);

    $sumHeader = array('Total hours', 'Standard Rate', 'Overtime');

    //calls function to make summary table
    $pdf->SummaryTable($sumHeader,$sumData);

    ob_end_flush();
  	$filename = $employee['first_name'] . "_" . $employee['last_name'] . "(" . $to_date . ").pdf";
  
    $pdf->Output('D', $filename);
}
?>