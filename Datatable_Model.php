<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Datatable_Model extends CI_Model 
{
	private $products = 'products';
	
	function get_single_product($id)
	{
		$result=$this->db->select("*")->where(array("id"=>$id))->get($this->products)->row();
		//echo "<pre>";
		return $result;
	}
	function get_products() 
	{		
//echo "<pre>";print_R($_REQUEST);	
		//total records
		$sqlCount = 'SELECT COUNT(id) AS row_count FROM ' . $this->products;
		$totalRecords = $this->db->query($sqlCount)->row()->row_count;
		
		//pagination
		$limit = '';
		$displayStart = $this->input->get_post('start', true);
		$displayLength = $this->input->get_post('length', true);		
		if (isset($displayStart) && $displayLength != '-1') {
            $limit = ' LIMIT ' . intval($displayStart) . ', ' . intval($displayLength);
        }
		
		$column=$this->input->get_post("order")['0']['column'];
		$colom_array=array("id","name","price","sale_price","sales_count","sale_date");
		$order_by_column=$colom_array[($column??0)];
		$order_by=$_REQUEST['order']['0']['dir'];
		$order = " ORDER BY {$order_by_column} {$order_by}";
		//filter
		
		$where = '';
		$searchVal=$this->input->get_post("search")['value'];
		if (isset($searchVal) && $searchVal != '') {
			$where .=" where id like '%{$searchVal}%' or name like '%{$searchVal}%' or  price like '%{$searchVal}%' or sale_price like '%{$searchVal}%' or sales_count like '%{$searchVal}%'  or sale_date like '%{$searchVal}%'  ";
		}
		
		
		//final records
		 $sql = 'SELECT  id, name, price, sale_price, sales_count, sale_date FROM ' . $this->products . $where . $order . $limit;
        $result = $this->db->query($sql)->result_array();
		
		//total rows
		$sql = 'SELECT count(*) as total FROM ' . $this->products . $where ;
        $totalFilteredRows = $this->db->query($sql)->row()->total;
		
		//display structure
		$echo = $this->input->get_post('draw', true);
        $output = array(
            "draw" => intval($echo),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFilteredRows,
            "data" => array()
        );
		
		//put into 'data' array
		//echo "<pre>	";		print_R($result);
		
		for($i=0;$i<count($result);$i++)	
		{
            $row=array();
            $row[0] = $result[$i]['id'];
			$row[1] = $result[$i]['name'];
			$row[2] = $result[$i]['price'];
			$row[3] = $result[$i]['sale_price'];
			$row[4] = $result[$i]['sales_count'];
			$row[5] = $result[$i]['sale_date'];            
		$row[6]='<button class=\'edit_input\' onclick="edit_me('.$result[$i]['id'].')" custom_attr='.$result[$i]['id'].'>Edit</button>&nbsp;&nbsp;<button class=\'delete\' id="delete_id_'.$result[$i]['id'].'" onclick="delete_item('.$result[$i]['id'].')" delete_attr='.$result[$i]['id'].' id='. $result[$i]['id'] .'>Delete</button>';
			//print_R($row);
            $output['data'][] = $row;
        }
		
		return $output;
	}
		function get_products1() {		
		//columns
		$columns = array(
            'id',
            'name',
            'price',
            'sale_price',
            'sales_count',
            'sale_date');
		
		//index column
		$indexColumn = 'id';
		
		//total records
		$sqlCount = 'SELECT COUNT(' . $indexColumn . ') AS row_count FROM ' . $this->products;
		$totalRecords = $this->db->query($sqlCount)->row()->row_count;
		
		//pagination
		$limit = '';
		$displayStart = $this->input->get_post('start', true);
		$displayLength = $this->input->get_post('length', true);
		
		if (isset($displayStart) && $displayLength != '-1') {
            $limit = ' LIMIT ' . intval($displayStart) . ', ' . intval($displayLength);
        }
		
		$uri_string = $_SERVER['QUERY_STRING'];
        $uri_string = preg_replace("/%5B/", '[', $uri_string);
        $uri_string = preg_replace("/%5D/", ']', $uri_string);

        $get_param_array = explode('&', $uri_string);
        $arr = array();
        foreach ($get_param_array as $value) {
            $v = $value;
            $explode = explode('=', $v);
            $arr[$explode[0]] = $explode[1];
        }
		
		$index_of_columns = strpos($uri_string, 'columns', 1);
        $index_of_start = strpos($uri_string, 'start');
        $uri_columns = substr($uri_string, 7, ($index_of_start - $index_of_columns - 1));
        $columns_array = explode('&', $uri_columns);
        $arr_columns = array();
		
		foreach ($columns_array as $value) {
            $v = $value;
            $explode = explode('=', $v);
            if (count($explode) == 2) {
                $arr_columns[$explode[0]] = $explode[1];
            } else {
                $arr_columns[$explode[0]] = '';
            }
        }
		
		//sort order
		$order = ' ORDER BY ';
        $orderIndex = $arr['order[0][column]'];
        $orderDir = $arr['order[0][dir]'];
        $bSortable_ = $arr_columns['columns[' . $orderIndex . '][orderable]'];
        if ($bSortable_ == 'true') {
            $order .= $columns[$orderIndex] . ($orderDir === 'asc' ? ' asc' : ' desc');
        }
		
		//filter
		$where = '';
        $searchVal = $arr['search[value]'];
        if (isset($searchVal) && $searchVal != '') {
            $where = " WHERE (";
            for ($i = 0; $i < count($columns); $i++) {
                $where .= $columns[$i] . " LIKE '%" . $this->db->escape_like_str($searchVal) . "%' OR ";
            }
            $where = substr_replace($where, "", -3);
            $where .= ')';
        }
		
		//individual column filtering
        $searchReg = $arr['search[regex]'];
        for ($i = 0; $i < count($columns); $i++) {
            $searchable = $arr['columns[' . $i . '][searchable]'];
            if (isset($searchable) && $searchable == 'true' && $searchReg != 'false') {
                $search_val = $arr['columns[' . $i . '][search][value]'];
                if ($where == '') {
                    $where = ' WHERE ';
                } else {
                    $where .= ' AND ';
                }
                $where .= $columns[$i] . " LIKE '%" . $this->db->escape_like_str($search_val) . "%' ";
            }
        }
		
		//final records
		$sql = 'SELECT SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $columns)) . ' FROM ' . $this->products . $where . $order . $limit;
        $result = $this->db->query($sql);
		
		//total rows
		$sql = "SELECT FOUND_ROWS() AS length_count";
        $totalFilteredRows = $this->db->query($sql)->row()->length_count;
		
		//display structure
		$echo = $this->input->get_post('draw', true);
        $output = array(
            "draw" => intval($echo),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFilteredRows,
            "data" => array()
        );
		
		//put into 'data' array
		foreach ($result->result_array() as $cols) {
            $row = array();
            foreach ($columns as $col) {
                $row[] = $cols[$col];
            }
			array_push($row, '<button class=\'edit\'>Edit</button>&nbsp;&nbsp;<button class=\'delete\' id='. $cols[$indexColumn] .'>Delete</button>');
            $output['data'][] = $row;
        }
		
		return $output;
	}
	function delete_product($id) {
		$sql = 'DELETE FROM ' . $this->products . ' WHERE id=' . $id;
		$this->db->query($sql);
		
		if ($this->db->affected_rows()) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	function edit_product($data) {
		$id=$data['id'];
		unset($data['id']);		
		$this->db->where('id', $id);
		$this->db->update($this->products, $data);
		//echo $this->db->last_query();
		return 1;
		
	}
	
	function add_product($data) 
	{		
		$this->db->insert($this->products, $data);
		//echo $this->db->last_query();
		return $this->db->insert_id();
			
	}
	
}

/* End of file Datatable_Model.php */
/* Location: ./application/models/Datatable_Model.php */