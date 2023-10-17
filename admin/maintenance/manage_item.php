<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `item_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container-fluid">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="item-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : ''; ?>">
		<div class="form-group">
			<label for="name" class="control-label">Name</label>
			<input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name : ''; ?>">
		</div>
		<div class="form-group">
			<label for="classification" class="control-label">Classification</label>
			<select name="classification" id="classification" class="form-control">
				<option value="IMPORT">IMPORT</option>
				<option value="IMPORT">DRY</option>
				<option value="FG">FG</option>
				<option value="FG">FG-IMPORT</option>
				<option value="FG">FG-NSQCS</option>
				<option value="SFG">NEW BLENDED</option>
				<option value="SFG">NEW SFG</option>
				<option value="SFG">SFG</option>
				<option value="SFG">SFG-NSQCS</option>
				<option value="SFG">OLD SFG</option>
				<option value="SFG">OLD BLENDED</option>
        	
        		<option value="Safe Keep">Safe Keep</option>
			</select>
		</div>
		<div class="form-group">
			<label for="lot_no" class="control-label">Lot no</label>
			<input type="number" name="lot_no" id="name" class="form-control rounded-0" value="<?php echo isset($lot_no) ? $lot_no : ''; ?>">
		</div>
		<div class="form-group">
			<label for="warehouse_id" class="control-label">warehouse</label>
			<select name="warehouse_id" id="warehouse_id" class="custom-select select2">
			<option <?php echo !isset($warehouse_id) ? 'selected' : '' ?> disabled></option>
			<?php 
			$warehouse = $conn->query("SELECT * FROM `warehouse_list` where status = 1 order by `name` asc");
			while($row=$warehouse->fetch_assoc()):
			?>
			<option value="<?php echo $row['id'] ?>" <?php echo isset($warehouse_id) && $warehouse_id == $row['id'] ? "selected" : "" ?> ><?php echo $row['name'] ?></option>
			<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Status</label>
			<select name="status" id="status" class="custom-select select">
			<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
			<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
			</select>
		</div>
	</form>
</div>
<script>
  
  $(document).ready(function () {
    $('.select2').select2({ placeholder: "Please Select here", width: "relative" });
    $('#item-form').submit(function (e) {
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_item",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST', // Change 'type' to 'method'
            dataType: 'json',
            error: err => {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_loader();
            },
            success: function (resp) {
                if (resp.status === 'success') {
                    location.reload();
                } else if (resp.status === 'failed' && resp.msg) {
                    var el = $('<div>');
                    el.addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    end_loader();
                } else {
                    alert_toast("An error occurred", 'error');
                    end_loader();
                    console.log(resp);
                }
            }
        });
    });
});
</script>
