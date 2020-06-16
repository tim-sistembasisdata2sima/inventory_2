<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Products';

		$this->load->model('model_products');
		$this->load->model('model_brands');
        $this->load->model('model_category');
        $this->load->model('model_attributes');
        $this->load->model('model_suppliers');
        $this->load->model('model_users');
	}

    /* 
    * It only redirects to the manage product page
    */
	public function index()
	{
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->render_template('products/index', $this->data);	
	}

    /*
    * It Fetches the products data from the product table 
    * this function is called from the datatable ajax function
    */
	public function fetchProductData()
	{
		$result = array('data' => array());

		$data = $this->model_products->getProductData();

		foreach ($data as $key => $value) {

            // $color_attribute = $this->model_attributes->getAttributeValueData($value['attribute_value_id']);
            $supplier_data = $this->model_suppliers->getSupplierData($value['supplier_id']);
			// button
            $buttons = '';
            if(in_array('updateProduct', $this->permission)) {
    			$buttons .= '<a href="'.base_url('products/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
            }

            if(in_array('deleteProduct', $this->permission)) { 
    			$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            }
            if(in_array('viewProduct', $this->permission)) {
                $buttons .= '<a href="'.base_url('products/detail/'.$value['id']).'" class="btn btn-default">Detail</a>';
            }
			

			$img = '<img src="'.base_url($value['image']).'" alt="'.$value['name'].'" class="img-circle" width="50" height="50" />';

            $availability = ($value['availability'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

            $qty_status = '';
            if($value['qty'] <= 10) {
                $qty_status = '<span class="label label-warning">Low !</span>';
            } else if($value['qty'] <= 0) {
                $qty_status = '<span class="label label-danger">Out of stock !</span>';
            }


			$result['data'][$key] = array(
				$img,
				$value['sku'],
				$value['name'],
				$value['price'],
                $value['discount'],
                $value['qty'] . ' ' . $qty_status,
                json_decode($value['attribute_value']),
                $availability,
                $supplier_data['name'],
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}	

    /*
    * If the validation is not valid, then it redirects to the create page.
    * If the validation for each input field is valid then it inserts the data into the database 
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function create()
	{
		if(!in_array('createProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
		$this->form_validation->set_rules('sku', 'SKU', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required');
        $this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
		$this->form_validation->set_rules('availability', 'Availability', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {
            // true case
            $user_id = $this->session->userdata('id');
        	$upload_image = $this->upload_image();

        	$data = array(
        		'name' => $this->input->post('product_name'),
        		'sku' => $this->input->post('sku'),
        		'price' => $this->input->post('price'),
                'discount' => $this->input->post('discount'),
                'qty' => $this->input->post('qty'),
        		'image' => $upload_image,
        		'attribute_value' => json_encode($this->input->post('attribute_value')),
        		'description' => $this->input->post('description'),
        		'brand_id' => $this->input->post('brands'),
        		'category_id' => $this->input->post('category'),
                'availability' => $this->input->post('availability'),
                'supplier_id' => $this->input->post('supplier'),
                'user_id' => $user_id
        	);

        	$create = $this->model_products->create($data);
        	if($create == true) {
        		$this->session->set_flashdata('success', 'Successfully created');
        		redirect('products/', 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('products/create', 'refresh');
        	}
        }
        else {
            // false case

        	// attributes 
        	// $attribute_data = 

        	// $attributes_final_data = array();
        	// foreach ($attribute_data as $k => $v) {
        	// 	$attributes_final_data[$k]['attribute_data'] = $v;

        	// 	$value = $this->model_attributes->getAttributeValueData($v['id']);

        	// 	$attributes_final_data[$k]['attribute_value'] = $value;
        	// }

        	$this->data['attributes'] =$this->model_attributes->getAttributeValueData();
			$this->data['brands'] = $this->model_brands->getActiveBrands();        	
            $this->data['category'] = $this->model_category->getActiveCategory();  
            $this->data['suppliers'] = $this->model_suppliers->getActiveSuppliers();        	

            $this->render_template('products/create', $this->data);
        }	
	}

    /*
    * This function is invoked from another function to upload the image into the assets folder
    * and returns the image path
    */
	public function upload_image()
    {
    	// assets/images/product_image
        $config['upload_path'] = 'assets/images/product_image';
        $config['file_name'] =  uniqid();
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1000';

        // $config['max_width']  = '1024';s
        // $config['max_height']  = '768';

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('product_image'))
        {
            $error = $this->upload->display_errors();
            return $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $type = explode('.', $_FILES['product_image']['name']);
            $type = $type[count($type) - 1];
            
            $path = $config['upload_path'].'/'.$config['file_name'].'.'.$type;
            return ($data == true) ? $path : false;            
        }
    }

    /*
    * If the validation is not valid, then it redirects to the edit product page 
    * If the validation is successfully then it updates the data into the database 
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function update($product_id)
	{      
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if(!$product_id) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        $this->form_validation->set_rules('sku', 'SKU', 'trim|required');
        $this->form_validation->set_rules('price', 'Price', 'trim|required');
        $this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('availability', 'Availability', 'trim|required');
        

        if ($this->form_validation->run() == TRUE) {
            // true case
            $user_id = $this->session->userdata('id');

            $data = array(
                'name' => $this->input->post('product_name'),
                'sku' => $this->input->post('sku'),
                'price' => $this->input->post('price'),
                'discount' => $this->input->post('discount'),
                'qty' => $this->input->post('qty'),
                'description' => $this->input->post('description'),
                'attribute_value' => json_encode($this->input->post('attribute_value')),
                'brand_id' => $this->input->post('brands'),
                'category_id' => $this->input->post('category'),
                'availability' => $this->input->post('availability'),
                'supplier_id' => $this->input->post('supplier'),
	    		'user_id' => $user_id
            );

            
            if($_FILES['product_image']['size'] > 0) {
                $upload_image = $this->upload_image();
                $upload_image = array('image' => $upload_image);
                
                $this->model_products->update($upload_image, $product_id);
            }

            $update = $this->model_products->update($data, $product_id);
            if($update == true) {
                $this->session->set_flashdata('success', 'Successfully updated');
                redirect('products/', 'refresh');
            }
            else {
                $this->session->set_flashdata('errors', 'Error occurred!!');
                redirect('products/update/'.$product_id, 'refresh');
            }
        }
        else {
            
            // false case
            $this->data['brands'] = $this->model_brands->getActiveBrands();         
            $this->data['category'] = $this->model_category->getActiveCategory(); 
            $this->data['attributes'] =$this->model_attributes->getAttributeValueData();
            $this->data['suppliers'] = $this->model_suppliers->getActiveSuppliers();                 

            $product_data = $this->model_products->getProductData($product_id);
            $this->data['product_data'] = $product_data;
            $this->render_template('products/edit', $this->data); 
        }   
	}

    /*
    * It removes the data from the database
    * and it returns the response into the json format
    */
	public function remove()
	{
        if(!in_array('deleteProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
        $product_id = $this->input->post('product_id');

        $response = array();
        if($product_id) {
            $delete = $this->model_products->remove($product_id);
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
    public function detail($product_id){
        if(!in_array('viewProduct', $this->permission)) {
			redirect('dashboard', 'refresh');
        }
        if(!$product_id) {
            redirect('products', 'refresh');
        }
        
        
        // $availability = ($value['availability'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

        // $qty_status = '';
        // if($product_data['qty'] <= 10) {
        //     $qty_status = '<span class="label label-warning">Low !</span>';
        // } else if($product_data['qty'] <= 0) {
        //     $qty_status = '<span class="label label-danger">Out of stock !</span>';
        // }
        

		$detail_data = $this->model_products->getProductData($product_id);
        $this->data['detail_data'] = $detail_data;
        
        $attribute_data = $detail_data['attribute_value'];
        $this->data['attribute_data'] = $attribute_data;

        $brand_data = $this->model_brands->getBrandData($detail_data['brand_id']);
        $this->data['brand_data'] = $brand_data;
        
        $category_data = $this->model_category->getCategoryData($detail_data['category_id']);
        $this->data['category_data'] = $category_data;

        $supplier_data = $this->model_suppliers->getSupplierData($detail_data['supplier_id']);
        $this->data['supplier_data'] = $supplier_data;
        
        $user_id = $this->session->userdata('id');
        $user_data = $this->model_users->getUserData($user_id);
        $this->data['user_data'] = $user_data;
        
        $this->render_template('products/detail', $this->data);

    }

}