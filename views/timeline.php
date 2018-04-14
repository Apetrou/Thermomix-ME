<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	if(isset($_GET['type']) && $_GET['type'] == 'muser') { ?>
		<script type="text/javascript">	
			getTimeline("<?=$_GET['type']?>");
		</script>
<?php
	}	else {
		$data = json_decode($_SESSION['selected_person'],true);
?>	
		<script type="text/javascript">	
			$(function(){
				getTimeline();
			});
		</script>
<?php
	}
?>

<div style="margin-top:90px">
	<?php if(isset($_GET['type']) && $_GET['type'] == "muser") { ?>
		<h2 id="consultant-heading" style="margin-top:30px; margin-left:80px;">User Timeline - <?php echo $_SESSION['user']['user_formatted_name'];?></h2>
	<?php } else { 
		if($data['flag'] == "cust") { ?>
			<h2 style="margin-top:30px; margin-left: 80px;">Customer Timeline - <?php echo $data['full_name'];?></h2>
		<?php } else { ?>
			<h2 id="consultant-heading" style="margin-top:30px; margin-left:80px;">User Timeline - <?php echo $data['full_name'];?></h2>
	<?php } } ?>


</div>

<div  id="timeline-container" style="margin-bottom:30px; margin-top: 30px">
	<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">
		<ul class="timeline"></ul>

	</div> 
</div>

<div id="cons-stock-container" class="hidden">
	<div class="title">Stock Levels</div>

	<div class="panel panel-thermox" style="margin-bottom:30px;">
		<div class="panel-heading">
			<h3 class="panel-title top"><?=$_SESSION['user']['user_formatted_name']?></h3>
		</div>
		<div class="panel">
			<div class="panel-body">
			<!-- <div id="stats-content" class="panel-body col-md-12" style="display: inline-block;">	 -->
				<table class="table table-striped table-hover" id="consultant_stock_table">
					<thead>
						<th>Item Name</th>
						<th>Item Code</th>
						<th>Item Quantity</th>
					</thead>
					<tbody id="consultant_stock_table_body">

					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<input id="consultant_title" type="hidden" value="User Timeline - <?php echo $data['user_full_name'];?>"> 