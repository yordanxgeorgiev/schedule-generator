<?php
	$fileType = $_POST['format'];
	$data = unserialize($_POST['export_data']);
	
	if($fileType == 'csv') export_csv($data);
	else if($fileType == 'xlsx') export_xlsx($data);
	else if($fileType == 'pdf')
	{
		$header = $data[0];
		array_shift($data);		
		
		export_pdf($header, $data);
	}		
?>

<?php
	function export_csv($data)
	{
		$fp = fopen('exported/exported.csv', 'w');
		foreach($data as $value)
		{
			fputcsv($fp, $value);
		}
		fclose($fp);
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="exported.csv"');

		readfile('exported/exported.csv');
	}
	function export_xlsx($data)
	{
		require 'SimpleXLSXGen.php';
		$xlsx = SimpleXLSXGen::fromArray( $data );
		$xlsx->saveAs('exported/exported.xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="exported.xlsx"');

		readfile('exported/exported.xlsx');
	}
	
	function export_pdf($header, $data)
	{
				
		require 'tfpdf/tfpdf.php';
		
		class PDF extends TFPDF
		{
			function BasicTable($header, $data)
			{
				// Header
				foreach($header as $col)
				{
					if($col == 'DESCRIPTION')
					{
						$col = 'DESCR.';
					}
					$this->Cell(22,7,$col,1);							
				}
				$this->Ln();
				
				// Data
				foreach($data as $row)
				{
					foreach($row as $col)
					{
						$this->Cell(22,7,$col,1);
					}
					$this->Ln();
				}
			}
		}
		
		$path = getcwd().'\tfpdf\font\unifont\\';
		define("_SYSTEM_TTFONTS", $path);	
		
		$pdf = new PDF();
		// Add a Unicode font (uses UTF-8)		
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->SetFont('DejaVu','',10);
		$pdf->AddPage();
		$pdf->BasicTable($header,$data);
		$pdf->Output();
	}
	
	
?>