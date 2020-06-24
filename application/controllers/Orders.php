<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Orders';

		$this->load->model('model_orders');
		$this->load->model('model_products');
		$this->load->model('model_customers');
		
	}

	/* 
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Manage Orders';
		$this->render_template('orders/index', $this->data);		
	}

	/*
	* Fetches the orders data from the orders table 
	* this function is called from the datatable ajax function
	*/
	public function fetchOrdersData()
	{
		$result = array('data' => array());

		$data = $this->model_orders->getOrdersData();
		
		foreach ($data as $key => $value) {
			$customers_data = $this->model_customers->getCustomerData($value['customer_id']);

			$count_total_item = $this->model_orders->countOrderItem($value['id']);
			$date = date('d M Y', $value['ordered_at']);
			$time = date('h:i a', $value['ordered_at']);

			$order_date = $date . ' ' . $time;

			// button
			$buttons = '';

			if(in_array('viewOrder', $this->permission)) {
				$buttons .= '<a target="__blank" href="'.base_url('orders/printDiv/'.$value['id']).'" class="btn btn-default"><i class="fa fa-print"></i></a>';
			}

			if(in_array('updateOrder', $this->permission) && $value['paid_status'] == 2) {
				$buttons .= ' <a href="'.base_url('orders/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			}

			if(in_array('deleteOrder', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}

			if($value['paid_status'] == 1) {
				$paid_status = '<span class="label label-success">Paid</span>';	
			}
			else {
				$paid_status = '<span class="label label-warning">Not Paid</span>';
			}
			$name = $customers_data['firstname'] .' '.$customers_data['lastname'];
			$net_amount = "IDR ".number_format($value['net_amount'],2);
			$result['data'][$key] = array(
				$value['bill_no'],
				$name,
				$customers_data['phone'],
				$order_date,
				$count_total_item,
				$net_amount,
				$paid_status,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database 
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{
		if(!in_array('createOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Add Order';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$order_id = $this->model_orders->create();
        	
        	if($order_id) {
        		$this->session->set_flashdata('success', 'Successfully created');
        		redirect('orders/update/'.$order_id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/create/', 'refresh');
        	}
        }
        else {
            // false case
			$this->data['products'] = $this->model_products->getActiveProductData(); 
            $this->data['customers'] = $this->model_customers->getCustomerData();

            $this->render_template('orders/create', $this->data);
        }	
	}

	public function addNewCustomer()
	{

		if(!in_array('createCustomer', $this->permission)) {
			redirect('orders', 'refresh');
		}

		$this->form_validation->set_rules('customer_first_name', 'customer first name', 'trim|required');
		$this->form_validation->set_rules('customer_last_name', 'customer last name', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {        	
        	
			$customer_id = $this->model_orders->addNewCustomer();
        	
        	if($customer_id) {
        		$this->session->set_flashdata('success', 'Successfully add new customer');
        		redirect('orders/create/'.$customer_id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/create/', 'refresh');
        	}
        }
        else {
            // false case  	

            $this->render_template('orders/create', $this->data);
        }	

	}

	/*
	* It gets the customer id passed from the ajax method.
	* It checks retrieves the particular product data from the product id 
	* and return the data into the json format.
	*/
	public function getCustomerValueById()
	{
		$customer_id = $this->input->post('customer_id');
		if($customer_id) {
			$customer_data = $this->model_customers->getCustomerData($customer_id);
			echo json_encode($customer_data);
		}
	}

	/*
	* It gets the product id passed from the ajax method.
	* It checks retrieves the particular product data from the product id 
	* and return the data into the json format.
	*/
	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}

	/*
	* It gets the all the active product inforamtion from the product table 
	* This function is used in the order page, for the product selection in the table
	* The response is return on the json format.
	*/
	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	/*
	* If the validation is not valid, then it redirects to the edit orders page 
	* If the validation is successfully then it updates the data into the database 
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function update($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Update Order';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$update = $this->model_orders->update($id);
        	
        	if($update == true) {
        		$this->session->set_flashdata('success', 'Successfully updated');
        		redirect('orders/', 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        }
        else {
            // false case

        	$result = array();
        	$orders_data = $this->model_orders->getOrdersData($id);

    		$result['order'] = $orders_data;
			$orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);

    		foreach($orders_item as $k => $v) {
    			$result['order_item'][] = $v;
    		}

    		$this->data['order_data'] = $result;
			
			$this->data['customer'] = $this->model_customers->getCustomerData($orders_data['customer_id']);      	
			
			$this->data['products'] = $this->model_products->getActiveProductData();  
			    	
			
            $this->render_template('orders/edit', $this->data);
        }
	}

	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if(!in_array('deleteOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$order_id = $this->input->post('order_id');

        $response = array();
        if($order_id) {
            $delete = $this->model_orders->remove($order_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Successfully removed"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing the product information";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refersh the page again!!";
        }

        echo json_encode($response); 
	}

	/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/
	public function printDiv($id)
	{
		$this->load->model('model_company');

		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$customer = $this->model_customers->getCustomerData($order_data['customer_id']);
			$company = $this->model_company->getCompanyData(1);

			$this->load->model('model_users');
			$user = $this->model_users->getUserData($order_data['user_id']);

			$date_text = ($order_data['paid_status'] == 1) ? "Pay Date" : "Order Date";
			$date_time = ($order_data['paid_status'] == 1) ? $order_data['paid_at'] : $order_data['ordered_at'];
			$date = date('d M Y H:i', $date_time);
			$paid_status = ($order_data['paid_status'] == 1) ? "Paid" : "Unpaid";
			
			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>AdminLTE 2 | Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
			  <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">
			</head>
			<body onload="window.print();">
			
			<div class="wrapper">
			  <section class="invoice">
			    <!-- title row -->
			    <div class="row">
			      <div class="col-xs-12">
			        <h2 class="page-header">
					'.$company['name'].'
					  <small class="pull-right">'.$date_text.': '.$date.' '.$user['firstname'].' '.$user['lastname'].'</small>
			        </h2>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- info row -->
			    <div class="invoice-info">
			      
			      <table class="invoice-col">
					<tr>
						<th><b>Bill ID</b></th>
						<td><b>:</b></td>
						<td>'.$order_data['bill_no'].'</td>
					</tr>
					<tr>
						<th><b>Name</b></th>
						<td><b>:</b></td>
						<td>'.$customer['firstname'].' '.$customer['lastname'].'</td>
					</tr>
					<tr>
						<th><b>Address</b></th>
						<td><b>:</b></td>
						<td>'.$customer['address'].'</td>
					</tr>
					<tr>
						<th><b>Phone</b></th>
						<td><b>:</b></td>
						<td>'.$customer['phone'].'</td>
					</tr>
			      </table>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
			            <th>Product name</th>
			            <th>Price</th>
			            <th>Discount</th>
			            <th>Promo Price</th>
			            <th>Qty</th>
			            <th>Amount</th>
			          </tr>
			          </thead>
			          <tbody>'; 

			          foreach ($orders_items as $k => $v) {
						$promo = $v['rate'] - $v['product_discount'];
						$promo_price = "IDR ".number_format($promo,2);


			          	$product_data = $this->model_products->getProductData($v['product_id']); 
			          	
			          	$html .= '<tr>
				            <td>'.$product_data['name'].'</td>
				            <td>'."IDR ".number_format($v['rate'],2).'</td>
				            <td>'."IDR ".number_format($v['product_discount'],2).'</td>
				            <td>'.$promo_price.'</td>
				            <td>'.$v['qty'].'</td>
				            <td>'."IDR ".number_format($v['amount'],2).'</td>
			          	</tr>';
			          }
			          
			          $html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <div class="row">
			      
			      <div class="col-xs-6 pull pull-right">

			        <div class="table-responsive">
			          <table class="table">
			            <tr>
			              <th style="width:50%">Gross Amount:</th>
			              <td>'."IDR ".number_format($order_data['gross_amount'],2).'</td>
			            </tr>';
			            
			            $html .=' <tr>
			              <th>Discount:</th>
			              <td>'."IDR ".number_format($order_data['total_discount'],2).'</td>
			            </tr>
			            <tr>
			              <th>Net Amount:</th>
			              <td>'."IDR ".number_format($order_data['net_amount'],2).'</td>
			            </tr>
			            <tr>
			              <th>Paid Status:</th>
			              <td>'.$paid_status.'</td>
						</tr>
			            <tr>
			              <th>Method:</th>
			              <td>'.$order_data['method'].'</td>
						</tr>
			          </table>
			        </div>
			      </div>
			      <!-- /.col -->
			    </div>
				<!-- /.row -->';
				if($order_data['paid_status'] == 1){
					$text = "Thank you for buying our product!";
				}else {
					$text = "Thank you for ordering our product!";
				}
				$html .='
				<p style="text-align:center"><em>'.$text.'</em></p>
				<p style="text-align:center; font-size:1.2rem">'.$company['name'].' | telephone: '.$company['phone'].' | address: '.$company['address'].'<br> '.$company['country'].'</p>

			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			  echo $html;
		}
	}

}