<?php
echo '
		<br><br>
		<form action="../export/export.php" method="post" style="font-size:18px; text-align:center; margin-bottom:50px;">			
			Export format: 
			<input type="radio" id="csv" name="format" value="csv">
			<label for="csv">CSV</label>
			<input type="radio" id="xlsx" name="format" value="xlsx">
			<label for="xlsx">XLSX</label>
			<input type="radio" id="pdf" name="format" value="pdf">
			<label for="pdf">PDF</label>	
			<input type=\'hidden\' name=\'export_data\' value=\''.$export_data.'\'> 
			<input type="submit" value="Export" name="export" style="font-size:18px; margin-left:20px;">			
		</form>
	';
?>