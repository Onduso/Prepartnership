<?php 
// $month = '2019-03-31';
// $merge_array = array_merge(
							// $this->finance_model->transactions_raised_in_month_not_cleared('CHQ', $month, 'outstanding_cheque_amount'),
							// $this->finance_model->transactions_raised_in_past_not_cleared('CHQ', $month, 'outstanding_cheque_amount'),
							// $this->finance_model->transactions_raised_in_month_cleared_in_future('CHQ', $month, 'outstanding_cheque_amount'),
							// $this->finance_model->transactions_raised_in_past_cleared_in_future('CHQ', $month, 'outstanding_cheque_amount')
							// );
// print_r($merge_array);							


$grid_array = $this -> finance_model -> build_dashboard_array('2019-03-31');

$none_requested_params = isset($grid_array['parameters']['no']) ? $grid_array['parameters']['no'] : array();

$requested_params = isset($grid_array['parameters']['yes']) ? $grid_array['parameters']['yes'] : array();

if(empty($none_requested_params) && empty($requested_params)){
 	?>
 	<div class='row'>
 		<div class='col-xs-12'>
 			<div class='well' style="text-align: center;">No Parameters and kindly contact system admin to populate  parameters </div>
 		</div>
 		
 	</div>
 	<?php //break;
		}else{
	?>
<div class='row'>
	<div class='col-xs-12'>
		<form class='form-horizontal form-groups-bordered validate'>

			<div class="form-group">
				<label class="control-label col-xs-3">Parameter</label>
				<div class='col-xs-9' id=''>
					<select class="form-control select2" multiple="multiple">
						<option>Select parameter</option>
						<option>Budget Variance</option>
						<option>Count of petty cash transactions</option>
						<option>Percent petty cash transaction</option>
						<option>Bank statement available</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-3">FCP</label>
				<div class="col-xs-9">
					<select class="form-control select2" multiple="multiple">
						<option>Select FCP</option>
						<option>KE0200</option>
						<option>KE0415</option>
						<option>KE0719</option>
						<option>KE0910</option>
					</select>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-xs-3">Risk Levels</label>
				<div class="col-xs-9">
					<select class="form-control select2" multiple="multiple">
						<option>Select Risk Level</option>
						<option>Low</option>
						<option>Medium</option>
						<option>High</option>
					</select>
				</div>
			</div>

			<div class="form-group">

				<div class="col-xs-offset-6 col-xs-6">
					<button class="btn btn-primary">
						Filter
					</button>
				</div>
			</div>
		</form>

	</div>
</div>
<hr/>
<div class='row'>
	<div class='col-xs-12'>
		
		<table  class='table table-striped table-responsive datatable'>
			<thead>
				
				<tr>
					<th rowspan="2">FCP ID</th>
					<th rowspan="2">Risk</th>
					<?php if(!empty($none_requested_params)){?>
					<th colspan="<?= count($none_requested_params); ?>">Non Requested Parameters</th>
					<?php } ?>
					<?php if(!empty($requested_params)){?>
					<th colspan="<?= count($requested_params); ?>">Requested Parameters</th>
					<?php } ?>
				</tr>
				<tr>
				
				<?php 
				
				if(!empty($none_requested_params)){
				 foreach ($none_requested_params as $none_requested_param) {
				 ?>
				     
				     <th><?= $none_requested_param; ?></th>
				 <?php }
						}
				?>
				<!--Requested Parameters-->
				
				<?php 
				if(!empty($requested_params)){
				 foreach ($requested_params as $requested_param) {
				 ?>
				     
				     <th><?= $requested_param; ?></th>
				     
				 <?php }
						}
				?>
				
				</tr>
			</thead>
			
			<tbody>
				<?php 
				 foreach ($grid_array['fcps_with_risks'] as $fcp_id => $value) { 
				?>
				   <tr>
				   	 <td><?= $fcp_id; ?></td>
				   	 <td><?= $value['risk']; ?></td>
				   	 <?php
				   	 if(isset($value['params'])){
				   	  foreach ($value['params'] as $param) { 
				   	 ?>
				   	   <td><?= $param; ?></td>
				   	  <?php }
							}
				   	  ?>
				   </tr>
				<?php } ?>
			</tbody>
			
		</table>

		

	</div>

</div>
<?php } ?>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		var datatable = $(".datatable").dataTable({
			dom : 'lBfrtip',
			buttons : ['pdf', 'csv', 'excel', 'copy']

		});

		$(".dataTables_wrapper select").select2({
			minimumResultsForSearch : -1
		});
	});

</script>