<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT p.*,s.name as warehouse FROM stock_in_list p inner join warehouse_list s on p.warehouse_id = s.id  where p.id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            $$k = $v;
        }
    }
    $item_classifications = array();
    $item_list = $conn->query("SELECT * FROM `item_list` WHERE status = 1");
        while ($row = $item_list->fetch_assoc()) {
        $item_classifications[$row['id']] = array(); // Initialize an empty array for each item
    }
}
?>
<style>
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title"><?php echo isset($id) ? "Stock Transfer Details - ".$wrr : 'Stock Transfer Details' ?></h4>
    </div>
    <div class="card-body">
        <form action="" id="po-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-info">WRR No:</label>
                        <input type="text" class="form-control form-control-sm rounded-0" value="<?php echo isset($wrr) ? $wrr : '' ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="warehouse_id" class="control-label text-info">Warehouse</label>
                            <select name="warehouse_id" id="warehouse_id" class="custom-select select2">
                                <option <?php echo !isset($warehouse_id) ? 'selected' : '' ?> disabled></option>
                                <?php 
                                $warehouse = $conn->query("SELECT * FROM `warehouse_list` where status = 1 order by `name` asc");
                                while($row = $warehouse->fetch_assoc()):
                                ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo isset($warehouse_id) && $warehouse_id == $row['id'] ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <fieldset>
                    <legend class="text-info">Stock Transfer Form</legend>
                    <div class="row justify-content-center align-items-end">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="item_id" class="control-label">Variety</label>
                            <select name="item_id" id="item_id" class="custom-select select2">
                                <option disabled selected></option>
                                <?php $item_arr = array();
                    $item_list = $conn->query("SELECT * FROM `item_list` WHERE status = 1 ORDER BY `variety` ASC");
                    while ($row = $item_list->fetch_assoc()) {
                        $item_arr[$row['warehouse_id']][$row['id']] = $row;
                        echo '<option value="' . $row['id'] . '">' . $row['variety'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="classification" class="control-label">Classification</label>
                            <select name="classification" id="classification" class="custom-select select2" disabled>
                                <option disabled selected></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="lot_no" class="control-label">Batch/Lot No:</label>
                            <select name="lot_no" id="lot_no" class="custom-select select2" disabled>
                                <option disabled selected></option>
                            </select>
                        </div>
                    </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="packaging_size" class="control-label">Packaging Size</label>
                                <input type="number" step="any" class="form-control rounded-0" id="packaging_size">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="quantity" class="control-label">Quantity</label>
                                <input type="number" step="any" class="form-control rounded-0" id="quantity">
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group">
                                <button type="button" class="btn btn-flat btn-sm btn-primary" id="add_to_list">Add to List</button>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <table class="table table-striped table-bordered" id="list">
                <colgroup>
                    <col width="1%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                </colgroup>
                <thead>
                    <tr class="text-light bg-navy">
                        <th class="text-center py-1 px-2"></th>
                        <th class="text-center py-1 px-2">Item</th>
                        <th class="text-center py-1 px-2">Batch/Lot No:</th>
                        <th class="text-center py-1 px-2">Classification</th>
                        <th class="text-center py-1 px-2">Packaging Size</th>
                        <th class="text-center py-1 px-2">Quantity</th>
                        <th class="text-center py-1 px-2">Total kg</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(isset($id)):
                        $qry = $conn->query("SELECT p.*,i.variety,i.classification FROM `po_items` p inner join item_list i on p.item_id = i.id where p.po_id = '{$id}'");
                        while($row = $qry->fetch_assoc()):
                    ?>
                    <tr data-id="<?php echo $row['item_id']; ?>">
                        <td class="py-1 px-2 text-center">
                            <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
                        </td>
                        <td class="py-1 px-2 item">
                            <?php echo $row['variety']; ?>
                        </td>
                        <td class="py-1 px-2 text-center classification">
                            <?php echo $row['classification']; ?>
                        </td>
                        <td class="py-1 px-2 text-center quantity">
                            <?php echo $row['quantity']; ?>
                        </td>
                        <td class="py-1 px-2 text-center packaging_size">
                            <?php echo $row['packaging_size']; ?>
                        </td>
                        <td class="py-1 px-2 text-center total">
                            <?php echo $row['total']; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <tr id="grand_total_row">
                        <td class="py-1 px-2 text-center"></td>
                        <td class="py-1 px-2 text-info">Grand Total</td>
                        <td class="py-1 px-2"></td>
                        <td class="py-1 px-2"></td>
                        <td class="py-1 px-2"></td>
                        <td class="py-1 px-2"></td>
                        <td class="py-1 px-2 text-center">
                            <input type="text" class="form-control form-control-sm rounded-0" id="grand_total" readonly>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="remarks" class="text-info control-label">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control rounded-0"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="card-footer py-1 text-center">
    <button class="btn btn-flat btn-primary" type="submit" form="po-form">Save</button>
    <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=stock_in' ?>">Cancel</a>
</div>
</div>
<table id="clone_list" class="d-none">
    <tr>
        <td class="py-1 px-2 text-center">
            <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
        </td>
        <td class="py-1 px-2 text-center item">
        </td>
        <td class="py-1 px-2 text-center lot_no">
        </td>
        <td class="py-1 px-2 text-center classification">
        </td>
        <td class="py-1 px-2 text-center quantity">
        </td>
        <td class="py-1 px-2 text-center packaging_size">
        </td>
        <td class="py-1 px-2 text-center total">
        </td>
    </tr>
</table>
<script>
var items = $.parseJSON('<?php echo json_encode($item_arr) ?>')
    var costs = $.parseJSON('<?php echo json_encode($cost_arr) ?>')
    
    $(function(){
        $('.select2').select2({
            placeholder:"Please select here",
            width:'resolve',
        })
        $('#item_id').select2({
            placeholder:"Please select Warehouse first",
            width:'resolve',
        })

        $('#warehouse_id').change(function(){
            var warehouse_id = $(this).val()
            $('#item_id').select2('destroy')
            if(!!items[warehouse_id]){
                $('#item_id').html('')
                var list_item = new Promise(resolve=>{
                    Object.keys(items[warehouse_id]).map(function(k){
                        var row = items[warehouse_id][k]
                        var opt = $('<option>')
                            opt.attr('value',row.id)
                            opt.text(row.name)
                        $('#item_id').append(opt)
                    })
                    resolve()
                })
                list_item.then(function(){
                    $('#item_id').select2({
                        placeholder:"Please select variety here",
                        width:'resolve',
                    })
                })
            }else{
                list_item.then(function(){
                    $('#item_id').select2({
                        placeholder:"No Items Listed yet",
                        width:'resolve',
                    })
                })
            }

        })

        $('#add_to_list').click(function(){
            var supplier = $('#warehouse_id').val()
            var item = $('#item_id').val()
            var quantity = $('#quantity').val() > 0 ? $('#quantity').val() : 0;
            var packaging_size = $('#packaging_size').val()
            var total = parseFloat(quantity) *parseFloat(price)
            // console.log(supplier,item)
            var item_variety = items[supplier][item].variety || 'N/A';
            var item_classification = items[supplier][item].classification || 'N/A';
            var tr = $('#clone_list tr').clone()
            if(item == '' || quantity == '' || packaging_size == '' ){
                alert_toast('Form Item textfields are required.','warning');
                return false;
            }
            if($('table#list tbody').find('tr[data-id="'+item+'"]').length > 0){
                alert_toast('Item is already exists on the list.','error');
                return false;
            }
            tr.find('[name="item_id[]"]').val(item)
            tr.find('.item').html(item_variety+'<br/>'+item_classification)
            tr.find('[name="lot_no[]"]').val(packaging_size)
            tr.find('[name="packaging_size[]"]').val(packaging_size)
            tr.find('[name="quantity[]"]').val(quantity)
            tr.find('[name="price[]"]').val(price)
            tr.find('[name="total[]"]').val(total)
            tr.attr('data-id',item)
            tr.find('.quantity .visible').text(quantity)
            tr.find('.packaging_size').text(packaging_size)
            tr.find('.item').html(item_variety+'<br/>'+item_classification)
            tr.find('.cost').text(parseFloat(price).toLocaleString('en-US'))
            tr.find('.total').text(parseFloat(total).toLocaleString('en-US'))
            $('table#list tbody').append(tr)
            calc()
            $('#item_id').val('').trigger('change')
            $('#quantity').val('')
            $('#packaging_size').val('')
            tr.find('.rem_row').click(function(){
                rem($(this))
            })
            
            $('[name="discount_perc"],[name="tax_perc"]').on('input',function(){
                calc()
            })
            $('#warehouse_id').attr('readonly','readonly')
        })
        $('#po-form').submit(function (e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_po",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: function (xhr, status, error) {
                    console.error(error);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function (resp) {
                    if (resp.status == 'success') {
                        location.replace(_base_url_ + "admin/?page=stock_in/view_po&id=" + resp.id);
                    } else if (resp.status == 'failed' && !!resp.msg) {
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
                    $('html,body').animate({ scrollTop: 0 }, 'fast');
                }
            });
        });

        if ('<?php echo isset($id) && $id > 0 ?>' == 1) {
            $('#warehouse_id').trigger('change');
            $('#warehouse_id').prop('readonly', true);
            $('table#list tbody tr .rem_row').click(function () {
                rem($(this));
            });
        }

        function rem(_this) {
            _this.closest('tr').remove();
            updateGrandTotalRow();
        }

        function updateGrandTotalRow() {
            var grandTotalRow = $('table#list tbody tr#grand_total_row');
            if (grandTotalRow.length == 0) {
                grandTotalRow = $('#clone_list tr#grand_total_row').clone();
                grandTotalRow.find('.total').html(grandTotal.toFixed(2));
                $('table#list tbody').append(grandTotalRow);
            } else {
                grandTotalRow.find('.total').html(grandTotal.toFixed(2));
            }
        }
    });
</script>
