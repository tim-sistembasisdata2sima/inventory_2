    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Product
        <small>Detail</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard/') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('products/') ?>">Products</a></li>
        <li class="active"><?php echo $detail_data['name']; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12 col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $detail_data['name']; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <img src="<?php echo base_url($detail_data['image']) ?>" alt="<?php echo $detail_data['name']?>"  width="400" height="auto" />'
              <table class="table table-bordered table-condensed table-hovered">
                <tr>
                  <th>Product name</th>
                  <td><?php echo $detail_data['name']; ?></td>
                </tr>
                <tr>
                  <th>SKU</th>
                  <td><?php echo $detail_data['sku']; ?></td>
                </tr>
                <tr>
                  <th>Price</th>
                  <td><?php echo $detail_data['price']; ?></td>
                </tr>
                <tr>
                  <th>Discount</th>
                  <td><?php echo $detail_data['discount']; ?></td>
                </tr>
                <tr>
                  <th>Quantity</th>
                  <?php 
                    $qty_status = '';
                    if($detail_data['qty'] <= 10) {
                        $qty_status = '<span class="label label-warning">Low !</span>';
                    } else if($detail_data['qty'] <= 0) {
                        $qty_status = '<span class="label label-danger">Out of stock !</span>';
                    }
                  ?>
                  <td><?php echo $detail_data['qty']; ?> <?php echo $qty_status; ?></td>
                </tr>
                <tr>
                  <th>Color</th>
                  <td><?php echo ($attribute_data); ?></td>
                </tr>
                <tr>
                  <th>Description</th>
                  <td><?php echo $detail_data['description']; ?></td>
                </tr>
                <tr>
                  <th>Brand</th>
                  <td><?php echo $brand_data['name']; ?></td>
                </tr>
                <tr>
                  <th>Category</th>
                  <td><?php echo $category_data['name']; ?></td>
                </tr>
                <tr>
                  <th>Availability</th>
                  <td><?php echo $detail_data['availability']; ?></td>
                </tr>
                <tr>
                  <th>Supplier</th>
                  <td><?php echo $supplier_data['name']; ?></td>
                </tr>
                <tr>
                  <th>Last inputed by</th>
                  <td><?php echo $user_data['username']; ?></td>
                </tr>
              </table>
            </div>
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

 
