<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Datatable CRUD Example using Codeigniter, MySQL, AJAX</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>



	<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.dataTables.min.css"/>
	<!--<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.modal.min.css"/>-->
	<script type= 'text/javascript' src="<?php echo base_url(); ?>assets/js/jquery-1.11.3.min.js"></script>
	<!--<script type= 'text/javascript' src="<?php echo base_url(); ?>assets/js/jquery.modal.min.js"></script>-->
	<script type= 'text/javascript' src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>

<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script>


	
	
	
</head>
<body>
	<div class="container-fluid">		
	<button type="button" id="id_button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>
 



<div id='error_msg' class="alert alert-info" style="display:none;">
  
</div>

	<table id="product-grid" class="display" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Price</th>
				<th>Sale Price</th>
				<th>Sale Count</th>
				<th>Sale Date</th>
				<th>Actions</th>
			</tr>
		</thead>
	</table>
	
	 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <form name="frm_add"  id="frm_add">
		  <input type="hidden" name="id" id="id">
          	<p>
			<label>Name</label>
			<input type="text" name="name" id="name" >
		</p>
		<p>
			<label>Price</label>
			<input type="text" name="price"  id="price" >
		</p>
		<p>
			<label>Sale Price</label>
			<input type="text" name="sale_price" id="sale_price">
		</p>
		<p>
			<label>Sale Count</label>
			<input type="text" name="sales_count" id="sales_count" >
		</p>
		<p>
			<label>Sale Date</label>
			<input type="text" name="sale_date" id="sale_date">
		</p>		          
        </div>
        <div class="modal-footer">
          <button type="submit" id="addNew">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </form>
      </div>
      
    </div>
  </div>
	
</div>
</body>
</html>
<script>
	
</script>

<script type= 'text/javascript'>
var data_var;
		var BASE_URL='<?php echo base_url(); ?>';		
		$(document).ready(function () {

			validator = $("#frm_add").validate({
				rules:{			
					'name':{
						required:true,							
					},
					'price':{
						required:true,				
					},			
				},
				messages:{			
					'name':{
						required:"Please enter sender name."
					},
					'price':{
						required:"Please enter sender email."			
					}
								
				}		
			});

		$("#frm_add").submit(function(e) 
		{						
			e.preventDefault();		////most most imp //don't forgot
			var form = $(this);
			if(! form.valid())							 
				return false;
						
			var str = $('#frm_add').serialize();
			$.ajax({
				type: "GET",
				url: BASE_URL+"/index.php/datatable/add_product",
				data: str,	
				success:function(response)
				{									
					$("#error_msg").show();
					var data=JSON.parse(response);					
					if(data.success==1)
					{
						$("#error_msg").html( data.msg );					
						$(".close").click();
						data_var.ajax.reload( null, false );				
					}
					else
					{
						$("#error_msg").html( data.msg );
						$(".close").click();
					}
				},
				error: function() {
					$("#msgAdd").html( "<span style='color: red'>Error adding a new product</span>" );
				}
			});				
		});

		data_var=$('#product-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": BASE_URL+"/index.php/datatable/get_products"
		});
		
	$("#id_button").on("click",function(){		
		$('#frm_add')[0].reset();		
	});
					
});
	
function delete_item(id)
	{
		if (confirm('Do you really want to delete record?')) 
		{
			$("#error_msg").show();
			$.ajax({
				type: "POST",
				url: BASE_URL+"/index.php/datatable/delete_product",
				data: 'id=' + id,
				cache: false,
				success: function() {		
					$('#error_msg').html("Record deleted successfuly");
					//$("#delete_id_"+id).parent().parent().hide();			
					//data_var.ajax.reload();	
					data_var.ajax.reload( null, false );
				},
				error: function() {
					$('#error_msg').html('Error deleting record');
				}
			});
		}
	}
	
	function edit_me(id)
	{			
		$("#id_button").click();
		
		$.ajax({
				type: "GET",
				url: BASE_URL+"/index.php/datatable/get_single_product",
				data: 'id=' + id,
				cache: false,
				success: function(response) {		
					var data= JSON.parse(response);
					$("#id").val(id);
					$("#name").val(data.name);
					$("#price").val(data.price);
					$("#sale_price").val(data.sale_price);
					$("#sales_count").val(data.sales_count);
					$("#sale_date").val(data.sale_date);
				},
				error: function() {
					alert('Error edit record');
				}
			});				
	}
	</script>