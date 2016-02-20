<?php
		include ('../../database_connection.php');

try{
						
		//start transaction
		$dbc->autocommit(FALSE);
		$insert_check = 'true';
		
		//define variables
		$p_label_name = htmlspecialchars($_GET['label_text']);
		$p_type = htmlspecialchars($_GET['type']);
		$p_select_values = htmlspecialchars($_GET['options']);
		if($p_type != 'select'){
			$p_select_values = NULL;
		}
		else{
			//format all option entries 
			//uppercase first letter and lowercase all other letters
				$pieces = explode(";", $p_select_values);
				foreach ($pieces as $options) {
				    ucfirst(strtolower($options));
					
				}
				$p_select_values = implode(";", $pieces);
		}
		//format all labels 
		//uppercase first letter and lowercase all other letters
		//$pieces_of_label = explode("\s", $p_label_name);
		$pieces_of_label = preg_split("/\s+/", $p_label_name);
		function myfunction(&$value,$key){
			$value = ucfirst(strtolower($value));
		}
		array_walk($pieces_of_label,"myfunction");
		//$p_label_value = implode(";", $pieces_of_label);
		print_r($pieces_of_label);
		
		//get number of things and increment by one
		$number_of_things = array();
		$stmt= $dbc->prepare("SELECT thing_id FROM create_user_things");
		if ($stmt->execute()){
	    	$stmt->bind_result($thing_id);
	    	while ($stmt->fetch()){
	    		
				echo $thing_id;
				$regrex_check = '/^thing(\d+)$/'; //remove dashes
				preg_match($regrex_check,$thing_id,$matches);
	        	$number_of_things[] = $matches[1];
			}
		} 
		else {
			throw new Exception("Execute Failure: Unable To Select from table");
		}
		$stmt -> close();
		
		sort($number_of_things);
		$last_element = end($number_of_things);
		$new_thing_id = $last_element + 1;
		$new_thing_id = 'thing'.$new_thing_id;
		print_r($number_of_things);
		echo $new_thing_id;
				
		//insert data into db. Use prepared statement 
	/*	$stmt2 = $dbc -> prepare("INSERT INTO create_user_things (thing_id, label_name, type, select_values) VALUES (?,?,?,?)");
		if(!$stmt2){
			$insert_check = 'false';
			throw new Exception("Prepare Failure: Unable To Insert Into Main Sample Table");	
		}
		else{
			$stmt2 -> bind_param('ssss', $new_thing_id,$p_label_name,$p_type,$p_select_values);
			if(!$stmt2 -> execute()){
				$insert_check = 'false';
				throw new Exception("Execution Failure: Unable To Insert Into Main Sample Table");
			}
			else{
				$rows_affected2 = $stmt2 ->affected_rows;
				$stmt2 -> close();
			}
		}
		*/
		//double check that thingid  is really just 'thing#'
	/*	$thing_check_regrex = '/^thing(\d+)$/'; //remove dashes
		$thing_type_check = preg_match($thing_check_regrex,$new_thing_id);
		if($thing_type_check == true && $insert_check == 'true'){
			//check which type of table you have
			$field_type = 'varchar(150)'; //default
			if($p_type == 'numeric_insert'){
				$field_type = 'decimal(5,2)';
			}
			
			//create column in store_user_things
			$stmt3 = $dbc -> prepare("ALTER TABLE store_user_things ADD $new_thing_id $field_type");
			if(!$stmt3){
				$insert_check = 'false';
				throw new Exception("Prepare Failure: Unable To Insert Into Main Sample Table");	
			}
			else{
				if(!$stmt3 -> execute()){
					$insert_check = 'false';
					throw new Exception("Execution Failure: Unable To Insert Into Main Sample Table");
				}
				else{
					$rows_affected3 = $stmt3 ->affected_rows;
					$stmt3 -> close();
				}
			}
		}
		else{
			$insert_check = 'false';
		}
		*/
				
		
		/*****************************************************************************
		 * Do One Last Check And Commit If You Had No Errors
		* ***************************************************************************/
						
		if($insert_check == 'true'){
			$dbc->commit();
			echo 'Data Submitted Successfully!';
			$submitted = 'true';					
		}
		else {
			throw new Exception("Final Error: Unable To Insert Info To DB. No Changes Made");		
		}
}
catch (Exception $e) { 
		if (isset ($dbc)){
       	 	$dbc->rollback ();
       		echo "Error:  " . $e; 
    	}	
}
?>