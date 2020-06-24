<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Orders</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Orders</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <?php echo $this->session->flashdata('success'); ?>
        </div>
        <?php elseif($this->session->flashdata('error')): ?>
        <div class="alert alert-error alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <?php echo $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Edit Order</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php base_url('orders/create') ?>" method="post" class="form-horizontal">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label for="date" class="col-sm-12 control-label">Date: <?php echo date('Y-m-d') ?></label>
              </div>
              <div class="form-group">
                <label for="time" class="col-sm-12 control-label">Date: <?php echo date('h:i a') ?></label>
              </div>

              <div class="col-md-4 col-xs-12 pull pull-left">

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-5 control-label" style="text-align:left;">Customer
                    Name</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="customer_first_name" name="customer_first_name"
                      placeholder="Customer Name" disabled
                      value="<?php echo $customer['firstname'] ?> <?php echo $customer['lastname'] ?>"
                      autocomplete="off" />
                  </div>
                </div>

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-5 control-label" style="text-align:left;">Customer
                    Address</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="customer_address" name="customer_address"
                      placeholder="Customer Address" disabled value="<?php echo $customer['address'] ?>"
                      autocomplete="off">
                  </div>
                </div>

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-5 control-label" style="text-align:left;">Customer
                    Phone</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="customer_phone" name="customer_phone"
                      placeholder="Customer Phone" disabled value="<?php echo $customer['phone'] ?>" autocomplete="off">
                  </div>
                </div>
              </div>


              <br /> <br />
              <table class="table table-bordered" id="product_info_table">
                <thead>
                  <tr>
                    <th style="width:30%">Product</th>
                    <th style="width:10%">Qty</th>
                    <th style="width:20%">Rate</th>
                    <th style="width:15%">Discount</th>
                    <th style="width:20%">Amount</th>
                    <th style="width:5%"><button type="button" id="add_row" class="btn btn-default"><i
                          class="fa fa-plus"></i></button></th>
                  </tr>
                </thead>

                <tbody>

                  <?php if(isset($order_data['order_item'])): ?>
                  <?php $x = 1; ?>
                  <?php foreach ($order_data['order_item'] as $key => $val): ?>
                  <?php //print_r($v); ?>
                  <tr id="row_<?php echo $x; ?>">
                    <td>
                      <select class="form-control select_group product" data-row-id="row_<?php echo $x; ?>"
                        id="product_<?php echo $x; ?>" name="product[]" style="width:100%;"
                        onchange="getProductData(<?php echo $x; ?>)" required>
                        <option value=""></option>
                        <?php foreach ($products as $k => $v): ?>
                        <option value="<?php echo $v['id'] ?>"
                          <?php if($val['product_id'] == $v['id']) { echo "selected='selected'"; } ?>>
                          <?php echo $v['name'] ?></option>
                        <?php endforeach ?>
                      </select>
                    </td>
                    <td><input type="number" min="0" name="qty[]" id="qty_<?php echo $x; ?>" class="form-control"
                        required onkeyup="getTotal(<?php echo $x; ?>)" value="<?php echo $val['qty'] ?>"
                        autocomplete="off"></td>
                    <td>
                      <input type="text" name="rate[]" id="rate_<?php echo $x; ?>" class="form-control" disabled
                        value="<?php echo "IDR ".number_format($val['rate'],2);?>" autocomplete="off">
                      <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" class="form-control"
                        value="<?php echo $val['rate']?>" autocomplete="off">
                    </td>
                    <td>
                      <input type="text" name="product_discount[]" id="product_discount_<?php echo $x; ?>"
                        class="form-control" disabled
                        value="<?php echo "IDR ".number_format($val['product_discount'],2);?>" autocomplete="off">
                      <input type="hidden" name="product_discount_value[]" id="product_discount_value_<?php echo $x; ?>"
                        class="form-control" value="<?php echo $val['product_discount'] ?>" autocomplete="off">
                    </td>
                    <td>
                      <input type="text" name="amount[]" id="amount_<?php echo $x; ?>" class="form-control" disabled
                        value="<?php echo "IDR ".number_format($val['amount'],2);?>" autocomplete="off">
                      <input type="hidden" name="amount_value[]" id="amount_value_<?php echo $x; ?>"
                        class="form-control" value="<?php echo $val['amount'] ?>" autocomplete="off">
                    </td>
                    <td><button type="button" class="btn btn-default" onclick="removeRow('<?php echo $x; ?>')"><i
                          class="fa fa-close"></i></button></td>
                  </tr>
                  <?php $x++; ?>
                  <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>

              <br /> <br />

              <div class="col-md-6 col-xs-12 pull pull-right">

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-5 control-label">Gross Amount</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="gross_amount" name="gross_amount" disabled
                      value="<?php echo "IDR ".number_format($order_data['order']['gross_amount'],2); ?>"
                      autocomplete="off">
                    <input type="hidden" class="form-control" id="gross_amount_value" name="gross_amount_value"
                      value="<?php echo $order_data['order']['gross_amount'] ?>" autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                  <label for="discount" class="col-sm-5 control-label">Discount</label>
                  <div class="col-sm-7">
                    <input type="text" data-type="currency" class="form-control" id="discount" name="discount" placeholder="Discount"
                      onkeyup="subAmount()"
                      value="<?php echo "IDR ".number_format($order_data['order']['total_discount'],2);?>"
                      autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                  <label for="net_amount" class="col-sm-5 control-label">Net Amount</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="net_amount" name="net_amount" disabled
                      value="<?php echo "IDR ".number_format($order_data['order']['net_amount'],2); ?>"
                      autocomplete="off">
                    <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value"
                      value="<?php echo $order_data['order']['net_amount'] ?>" autocomplete="off">
                  </div>
                </div>

                <div class="form-group">
                  <label for="paid_status" class="col-sm-5 control-label">Paid Status</label>
                  <div class="col-sm-7">
                    <select type="text" class="form-control" id="paid_status" name="paid_status"
                      onchange="showDiv('method', this)">
                      <option value="1"
                        <?php if($order_data['order']['paid_status'] == 1) { echo "selected='selected'"; } ?>>Paid
                      </option>
                      <option value="2"
                        <?php if($order_data['order']['paid_status'] == 2) { echo "selected='selected'"; } ?>>Unpaid
                      </option>
                    </select>
                  </div>
                </div>
                <?php if($order_data['order']['paid_status'] == 1): ?>
                  <div class="form-group" id="method">
                    <label for="pay_method" class="col-sm-5 control-label">Pay Method</label>
                    <div class="col-sm-7">
                      <select type="text" class="form-control" id="pay_method" name="pay_method">
                        <option value="none"
                          <?php if($order_data['order']['method'] == 'none' || '') { echo "selected='selected'"; } ?>>
                        </option>
                        <option value="Cash"
                          <?php if($order_data['order']['method'] == 'Cash') { echo "selected='selected'"; } ?>>Cash
                        </option>
                        <option value="Credit Card"
                          <?php if($order_data['order']['method'] == 'Credit Card') { echo "selected='selected'"; } ?>>
                          Credit Card</option>
                        <option value="Debit Card"
                          <?php if($order_data['order']['method'] == 'Debit Card') { echo "selected='selected'"; } ?>>
                          Debit Card</option>
                        <option value="Transfer"
                          <?php if($order_data['order']['method'] == 'Transfer') { echo "selected='selected'"; } ?>>
                          Transfer from Bank</option>
                        <option value="E-money"
                          <?php if($order_data['order']['method'] == 'E-money') { echo "selected='selected'"; } ?>>E-money
                        </option>
                      </select>
                    </div>
                  </div>
                <?php endif; ?>

              </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">

              <a target="__blank" href="<?php echo base_url() . 'orders/printDiv/'.$order_data['order']['id'] ?>"
                class="btn btn-default">Print</a>
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <a href="<?php echo base_url('orders/') ?>" class="btn btn-warning">Back</a>
            </div>
          </form>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->


  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";

  // function printOrder(id)
  // {
  //   if(id) {
  //     $.ajax({
  //       url: base_url + 'orders/printDiv/' + id,
  //       type: 'post',
  //       success:function(response) {
  //         var mywindow = window.open('', 'new div', 'height=400,width=600');
  //         // mywindow.document.write('<html><head><title></title>');
  //         // mywindow.document.write('<link rel="stylesheet" href="<?php //echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>" type="text/css" />');
  //         // mywindow.document.write('</head><body >');
  //         mywindow.document.write(response);
  //         // mywindow.document.write('</body></html>');

  //         mywindow.print();
  //         mywindow.close();

  //         return true;
  //       }
  //     });
  //   }
  // }

  $(document).ready(function () {
    $(".select_group").select2();
    // $("#description").wysihtml5();

    $("#mainOrdersNav").addClass('active');
    $("#manageOrdersNav").addClass('active');


    // Add new row in the table 
    $("#add_row").unbind('click').bind('click', function () {
      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      var row_id = count_table_tbody_tr + 1;

      $.ajax({
        url: base_url + '/orders/getTableProductRow/',
        type: 'post',
        dataType: 'json',
        success: function (response) {
          
          var html = '<tr id="row_' + row_id + '">' +
            '<td>' +
            '<select class="form-control select_group product" data-row-id="' + row_id +
            '" id="product_' + row_id +
            '" name="product[]" style="width:100%;" onchange="getProductData(' + row_id + ')">' +
            '<option value=""></option>';
          $.each(response, function (index, value) {
            html += '<option value="' + value.id + '">' + value.name + '</option>';
          });

          html += '</select>' +
            '</td>' +
            '<td><input type="number" name="qty[]" id="qty_' + row_id +
            '" class="form-control" onkeyup="getTotal(' + row_id + ')"></td>' +
            '<td><input type="text" name="rate[]" id="rate_' + row_id +
            '" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_' +
            row_id + '" class="form-control"></td>' +
            '<td><input type="text" name="product_discount[]" id="product_discount_' + row_id +
            '" class="form-control" disabled><input type="hidden" name="product_discount_value[]" id="product_discount_value_' +
            row_id + '" class="form-control"></td></td>' +
            '<td><input type="text" name="amount[]" id="amount_' + row_id +
            '" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_' +
            row_id + '" class="form-control"></td>' +
            '<td><button type="button" class="btn btn-default" onclick="removeRow(\'' + row_id +
            '\')"><i class="fa fa-close"></i></button></td>' +
            '</tr>';

          if (count_table_tbody_tr >= 1) {
            $("#product_info_table tbody tr:last").after(html);
          } else {
            $("#product_info_table tbody").html(html);
          }

          $(".product").select2();

        }
      });

      return false;
    });

  }); // /document

  function getTotal(row = null) {
    if (row) {
      //check if rate and product discount is int or not
      if (isNaN($("#rate_value_" + row).val())) {
        var rate_value = ($("#rate_value_" + row).val()).replace(/[^0-9.-]+/g, "");

      } else {
        var rate_value = ($("#rate_value_" + row).val());

      }
      if (isNaN($("#product_discount_value_" + row).val())) {
        var prod_disc = ($("#product_discount_value_" + row).val()).replace(/[^0-9.-]+/g, "");
      } else {
        var prod_disc = ($("#product_discount_value_" + row).val());
      }

      var total = (Number(rate_value) - Number(prod_disc)) * Number($("#qty_" + row).val());
      total = total.toFixed(2);
      $("#amount_" + row).val(Intl.NumberFormat('en-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(total));
      $("#amount_value_" + row).val(total);

      subAmount();

    } else {
      alert('no row !! please refresh the page');
    }
  }

  // get the product information from the server
  function getProductData(row_id) {
    var product_id = $("#product_" + row_id).val();
    if (product_id == "") {
      $("#rate_" + row_id).val("");
      $("#rate_value_" + row_id).val("");

      $("#product_discount_" + row_id).val("");
      $("#product_discount_value_" + row_id).val("");

      $("#qty_" + row_id).val("");

      $("#amount_" + row_id).val("");
      $("#amount_value_" + row_id).val("");

    } else {
      $.ajax({
        url: base_url + 'orders/getProductValueById',
        type: 'post',
        data: {
          product_id: product_id
        },
        dataType: 'json',
        success: function (response) {
          // setting the rate value into the rate input field

          $("#rate_" + row_id).val(Intl.NumberFormat('en-ID', {
            style: 'currency',
            currency: 'IDR'
          }).format(response.price));
          $("#rate_value_" + row_id).val(response.price);

          $("#product_discount_" + row_id).val(Intl.NumberFormat('en-ID', {
            style: 'currency',
            currency: 'IDR'
          }).format(response.discount));
          $("#product_discount_value_" + row_id).val(response.discount);

          $("#qty_" + row_id).val(1);
          $("#qty_value_" + row_id).val(1);

          var total = (Number(response.price) - Number(response.discount)) * 1;
          total = total.toFixed(2);
          $("#amount_" + row_id).val(Intl.NumberFormat('en-ID', {
            style: 'currency',
            currency: 'IDR'
          }).format(total));
          $("#amount_value_" + row_id).val(total);

          subAmount();
        } // /success
      }); // /ajax function to fetch the product data 
    }
  }

  // calculate the total amount of the order
  function subAmount() {
    var tableProductLength = $("#product_info_table tbody tr").length;
    var totalSubAmount = 0;
    for (x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);

      totalSubAmount = Number(totalSubAmount) + Number($("#amount_value_" + count).val());
    } // /for

    totalSubAmount = totalSubAmount.toFixed(2);


    // sub total
    $("#gross_amount").val(Intl.NumberFormat('en-ID', {
      style: 'currency',
      currency: 'IDR'
    }).format(totalSubAmount));
    $("#gross_amount_value").val(totalSubAmount);

    // total amount
    var totalAmount = (Number(totalSubAmount));
    totalAmount = totalAmount.toFixed(2);
    $("#net_amount").val(Intl.NumberFormat('en-ID', {
      style: 'currency',
      currency: 'IDR'
    }).format(totalAmount));
    $("#totalAmountValue").val(totalAmount);

    var discount = ($("#discount").val()).replace(/[^0-9.-]+/g, "");
    if (discount) {
      var grandTotal = Number(totalAmount) - Number(discount);
      grandTotal = grandTotal.toFixed(2);
      $("#net_amount").val(Intl.NumberFormat('en-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(grandTotal));
      $("#net_amount_value").val(grandTotal);
    } else {
      $("#net_amount").val(Intl.NumberFormat('en-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(totalAmount));
      $("#net_amount_value").val(totalAmount);

    } // /else discount 

    // var paid_amount = Number($("#paid_amount").val());
    // if(paid_amount) {
    //   var net_amount_value = Number($("#net_amount_value").val());
    //   var remaning = net_amount_value - paid_amount;
    //   $("#remaining").val(remaning.toFixed(2));
    //   $("#remaining_value").val(remaning.toFixed(2));
    // }

  } // /sub total amount

  // function paidAmount() {
  //   var grandTotal = $("#net_amount_value").val();

  //   if(grandTotal) {
  //     var dueAmount = Number($("#net_amount_value").val()) - Number($("#paid_amount").val());
  //     dueAmount = dueAmount.toFixed(2);
  //     $("#remaining").val(dueAmount);
  //     $("#remaining_value").val(dueAmount);
  //   } // /if
  // } // /paid amoutn function

  function removeRow(tr_id) {
    $("#product_info_table tbody tr#row_" + tr_id).remove();
    subAmount();
  }

  //Format number to currency
  $("input[data-type='currency']").on({
    keyup: function () {
      formatCurrency($(this));
    },
    blur: function () {
      formatCurrency($(this), "blur");
    }
  });

  function formatNumber(n) {
    // format number 1000000 to 1,234,567
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function formatCurrency(input, blur) {
    // appends $ to value, validates decimal side
    // and puts cursor back in right position.

    // get input value
    var input_val = input.val();

    // don't validate empty input
    if (input_val === "") {
      return;
    }

    // original length
    var original_len = input_val.length;

    // initial caret position 
    var caret_pos = input.prop("selectionStart");

    // check for decimal
    if (input_val.indexOf(".") >= 0) {

      // get position of first decimal
      // this prevents multiple decimals from
      // being entered
      var decimal_pos = input_val.indexOf(".");

      // split number by decimal point
      var left_side = input_val.substring(0, decimal_pos);
      var right_side = input_val.substring(decimal_pos);

      // add commas to left side of number
      left_side = formatNumber(left_side);

      // validate right side
      right_side = formatNumber(right_side);

      // On blur make sure 2 numbers after decimal
      if (blur === "blur") {
        right_side += "00";
      }

      // Limit decimal to only 2 digits
      right_side = right_side.substring(0, 2);

      // join number by .
      input_val = "IDR " + left_side + "." + right_side;

    } else {
      // no decimal entered
      // add commas to number
      // remove all non-digits
      input_val = formatNumber(input_val);
      input_val = "IDR " + input_val;

      // final formatting
      if (blur === "blur") {
        input_val += ".00";
      }
    }

    // send updated string to input
    input.val(input_val);

    // put caret back in the right position
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
  }

  function showDiv(divId, element) {
    document.getElementById(divId).style.display = element.value == 1 ? 'block' : 'none';
  }
</script>