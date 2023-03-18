<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="robots" content="noindex, nofollow" />
    <title>:: Invoices ::</title>

    <!-- Font awesome css -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> 
    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
	<div id="invoice_container" class="container">
		<h1>Invoice</h1>
		<label>Item 1</label>
		<div class="row item_div">
			<div class="container col-md-6 mx-auto" data-line-number="1">
				<div class="form-group">
					<input type="text" name="name" id="item_name1" class="form-control item_name" placeholder="Name">
				</div>

				<div class="form-group">
					<input type="number" name="quantity" id="item_quantity1" class="form-control item_quantity" placeholder="Quantity" onkeyup="calc_line_total(1);">
				</div>

				<div class="form-group">
					<input type="number" name="unit_price" id="item_unit1" class="form-control item_unit" placeholder="Unit Price ($)" onkeyup="calc_line_total(1);">
				</div>

				<div class="form-group">
					<label for="tax">Tax:</label>
					<select name="tax" class="form-control item_tax" id="item_tax1" onchange="calc_totals();">
					  <option value="0">0%</option>
					  <option value="1">1%</option>
					  <option value="5">5%</option>
					  <option value="10">10%</option>
					</select>
				</div>
			</div>
			<div class="container col-md-4">
				<label>Line Total : <span class="line_total" id="line_total_1" line-number="1">0</span></label>
			</div>
		</div>
	</div>

	<div class="col-md-6 mx-auto">
		<i class="fa fa-plus-circle" style="float: right;font-size: 25px;color: green;cursor: pointer;" id="add_line"></i>
	</div>
	
	<br><hr style="border:1px solid #eee">

	<div class="container">
		<div class="row" style="text-align: right;">
			<h3>Subtotal without tax : <span id="subtotal">0</span></h3>
			<h3>Subtotal with tax : <span id="subtotal_tax">0</span></h3>
		</div>
		<div class="row">
			<label>Apply Discount</label>
			<div class="row form-group">
				<div class="col-md-6 form-group">
					<select name="disc_method" id="disc_method" onclick="disc_method();" class="form-control">
						<option value="1">Percentage</option>
						<option value="2">Amount ($)</option>
					</select>
				</div>
				<div class="col-md-6 discount_perc">
					<input type="number" name="disc_perc" id="disc_perc" placeholder="0%" class="form-control" max="100" min="0">
				</div>
				<div class="col-md-6 hide discount_amount">
					<input type="number" name="disc_amount" id="disc_amount" placeholder="Amount ($)" class="form-control" min="0">
				</div>
			</div>
			<div class="container">
				<label class="hide" id="disc_error" style="color: red;"></label>
			</div>
			<button type="button" class="btn btn-primary" onclick="add_discount();" style="float:right;">Add Discount</button>
		</div>
	</div>

	<br><hr style="border:1px solid #eee">

	<div class="container" style="text-align:right;">
		<h3>Total Amount : <span id="total_amount">0</span></h3>
	</div>

	<div class="container" style="text-align:right;">
		<button class="btn btn-primary" onclick="generate_invoice();">Generate Invoice</button>
	</div>

	<div class="modal fade" id="invoice_modal" role="dialog">
	    <div class="modal-dialog">
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Invoice Details</h4>
	        </div>
	        <div class="modal-body">
	          <div class="container" id="invoice_body"></div>
	          <div class="container" id="subtotal_body"></div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>

</html>

<script type="text/javascript">
	var subtotal = 0;
	var subtotal_tax = 0;

	$(document).ready(function(){
	})

	$("#add_line").on('click',function(){
		let line_count = $('.item_div').length+1;
		var line_html = '<label>Item '+line_count+'</label><div class="row item_div"><div class="container col-md-6 mx-auto" data-line-number="'+line_count+'"><div class="form-group"><input type="text" name="name" class="form-control item_name" placeholder="Name"></div><div class="form-group"><input type="number" name="quantity" id="item_quantity'+line_count+'" class="form-control item_quantity" placeholder="Quantity" onkeyup="calc_line_total('+line_count+')"></div><div class="form-group"><input type="number" name="unit_price" id="item_unit'+line_count+'" class="form-control item_unit" placeholder="Unit Price ($)" onkeyup="calc_line_total('+line_count+')"></div><div class="form-group"><label for="tax">Tax:</label><select name="tax" class="form-control item_tax" id="item_tax'+line_count+'" onchange="calc_totals();"><option value="0">0%</option><option value="1">1%</option><option value="5">5%</option><option value="10">10%</option></select></div></div><div class="container col-md-4"><label>Line Total : <span class="line_total" id="line_total_'+line_count+'" line-number="'+line_count+'">0</span></label></div></div>';
		$("#invoice_container").append(line_html);
	})

	function calc_line_total(number){
		let line_total = $("#item_quantity"+number).val() * $("#item_unit"+number).val();
		line_total = isNaN(line_total) ? '0' : line_total;
		$("#line_total_"+number).text(line_total);
		calc_totals();
	}

	function calc_totals(){
		subtotal = 0;
		subtotal_tax = 0;
		$(".line_total").each(function(index,element){
			let line_number = $(element).attr('line-number');
			let tax = $("#item_tax"+line_number).val();
			let line_total = Number($(element).text());

			tax = isNaN(tax) ? 0 : tax;
			line_total = isNaN(line_total) ? 0 : line_total;

			subtotal += line_total;
			subtotal_tax += Math.round((line_total + (line_total * tax / 100)),2);
			$("#subtotal").text(subtotal);
			$("#subtotal_tax").text(subtotal_tax);
		})
	}

	function disc_method(){
		if($("#disc_method").val() == 1){
			$(".discount_amount").addClass("hide");
			$(".discount_perc").removeClass("hide");
		}else{
			$(".discount_amount").removeClass("hide");
			$(".discount_perc").addClass("hide");
		}
	}

	function add_discount(){
		let discount_type = $("#disc_method").val();
		let discount_value = 0;
		let total_amount = 0;
		let subtotal_tax = Number($("#subtotal_tax").text());
		if(discount_type == 1){
			discount_value = $("#disc_perc").val();
			if(discount_value > 100 || discount_value < 0){
				$("#disc_error").removeClass("hide").text("*Please enter the value between 0 and 100");
			}else{
				$("#disc_error").addClass("hide");
				total_amount = subtotal_tax - Math.round((subtotal_tax * discount_value / 100), 2);
			}
		}else{
			discount_value = $("#disc_amount").val();
			if(discount_value > subtotal_tax || discount_value < 0){
				$("#disc_error").removeClass("hide").text("*Please enter the value between 0 and "+subtotal_tax);
			}else{
				$("#disc_error").addClass("hide");
				total_amount = subtotal_tax - discount_value;
			}
		}
		$("#total_amount").text(total_amount);
	}

	function generate_invoice(){
		$("#invoice_body").html('');
		$(".line_total").each(function(index,element){
			let line_number = $(element).attr('line-number');
			let html = '<label>Item 1</label><br><span>Name : '+$("#item_name"+line_number).val()+'</span><br><span>Quantity : '+$("#item_quantity"+line_number).val()+'</span><br><span>Tax : '+$("#item_tax"+line_number).val()+'%</span><br><span>Line total : '+$(element).text()+'</span><br><hr>'
			$("#invoice_body").append(html);
		})
		let discount_type = $("#disc_method").val();
		let discount_value = (discount_type == 1) ? $("#disc_perc").val() : $("#disc_amount").val();
		discount_value = (discount_type == 1) ? discount_value+"%" : "$ ".discount_value;
		discount_type = (discount_type == 1) ? "Percentage" : "Amount ($)";

		let sub_html = '<label>Subtotal : '+$("#subtotal").text()+'</label><br><label>Subtotal with tax : '+$("#subtotal_tax").text()+'</label><br><label>Discount Type : '+discount_type+'</label><br><label>Discount Value : '+discount_value+'</label><br><label>Total Amount : '+$("#total_amount").text()+'</label>';
		$("#subtotal_body").html(sub_html);

		$('#invoice_modal').modal('show');
	}

</script>