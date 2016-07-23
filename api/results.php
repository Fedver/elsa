<?php
	
	include("test.setup.php");

	$test = new Test($mysqli, "full");

	$results = $test->getAllTestsDone();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="utf-8" />
        <title>ELSA test</title>
		<script src='../js/jquery.min.js'></script>
		<script src='js/test.js'></script>
		<script src='../js/spin.js'></script>
		<script src='../js/jquery.spin.js'></script>
		<script src="../js/bootstrap.js"></script>
		<link rel="stylesheet" href="../css/bootstrap.css">
    </head>
    <body>
		<a href="test.php" target="_self">Vai ai test completi</a> || <a href="partial.php" target="_self">Vai ai test parziali</a> || <a href="../index.php" target="_self">Torna alla home</a>
		<div class="panel-group" id="accordion">
			<?php 
			for ($i= 0; $i < count($results['id']); $i++){

				list($precision, $recall, $fmeasure, $ndcg) = $test->calcIndicators($results['mapping'][$i], $results['result'][$i], $results['type'][$i]);
					
				echo '<div class="panel panel-default">
						<div class="panel-heading">
						  <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$i.'">
							<b>'.$results['titolo'][$i].'</b> '.$results['lingua'][$i].' <b>('.$results['type'][$i].')</b> il '.$results['date'][$i].' by '.$results['where'][$i].'
							</a>
						  </h4>
						</div>
						<div id="collapse'.$i.'" class="panel-collapse collapse">
						 <div class="panel-body">
						  <table class="table">
						   <tr>
						    <td><b>Header</b></td>
						    <td>
						     '.str_replace(";", "</td><td>", $results['header'][$i]).'
							</td>
						   </tr>
						    <td><b>Gold standard</b></td>
						    <td>
						     '.str_replace(";", "</td><td>", $test->removeWeights($results['mapping'][$i])).'
							</td>
						   </tr>
						    <td><b>API result</b></td>
						    <td>
						     '.str_replace(";", "</td><td>", $results['result'][$i]).'
							</td>
						   </tr>
						  </table>
						  <div>
						   <b>Precision:</b> '.$precision.'<br />
						   <b>Recall:</b> '.$recall.'<br />
						   <b>F-Measure:</b> '.$fmeasure.'<br />
						   <b>nDCG medio:</b> '.$ndcg.'<br />
						  </div>
						 </div>
						</div>
					  </div>';
			}
			?>
		</div>
		<?php
			list($final_precision_full, $final_recall_full, $final_fmeasure_full, $final_ndcg_full) = $test->getFinalEvaluation("full");
			list($final_precision_part, $final_recall_part, $final_fmeasure_part, $final_ndcg_part) = $test->getFinalEvaluation("partial");
		?>
		Medie (full):<br />
		<div>
			<b>Precision:</b> <?php echo $final_precision_full; ?><br />
			<b>Recall:</b> <?php echo $final_recall_full; ?><br />
			<b>F-Measure:</b> <?php echo $final_fmeasure_full; ?><br />
			<b>nDCG:</b> <?php echo $final_ndcg_full; ?><br />
		</div>
		<br />Medie (partial):<br />
		<div>
			<b>Precision:</b> <?php echo $final_precision_part; ?><br />
			<b>Recall:</b> <?php echo $final_recall_part; ?><br />
			<b>F-Measure:</b> <?php echo $final_fmeasure_part; ?><br />
			<b>nDCG:</b> <?php echo $final_ndcg_part; ?><br />
		</div>
    </body>
</html>