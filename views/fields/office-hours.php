<?php

function profile_cct_officehours_field_shell( $action, $options ) {
	
	if( is_object($action) ):
		$post = $action;
		$action = "display";
		$data = $options['args']['data'];
		$options = $options['args']['options'];
	endif;
	
	$field = Profile_CCT::get_object(); // prints "Creating new instance."
	
	$default_options = array(
		'type' => 'officehours',
		'label'=>'officehours',
		'description'=>'',
		'multiple'=>true,
		'show_multiple'=>true,
		'class'=>'officehours'
		);
	
	$options = (is_array($options) ? array_merge($default_options,$options): $default_options );
	
	$field->start_field($action,$options);
	
	if( $field->is_data_array( $data ) ):
		$count = 0;
		foreach($data as $item_data):
			profile_cct_officehours_field($item_data,$options,$count);
			$count++;
		endforeach;
		
	else:
		profile_cct_officehours_field($data,$options);
	endif;
	
	$field->end_field( $action, $options );

}
function profile_cct_officehours_field( $data, $options, $count = 0 ){

	extract( $options );
	$show = (is_array($show) ? $show : array());
	$field = Profile_CCT::get_object();
	$hour_min = 1;
    $hour_max = 12;
	$hour_array = range($hour_min, $hour_max);
	$minute_array = array('00','05');
	$minute_temp_range = range(10,55,5);
	$i = 2;
	foreach( $minute_temp_range as $minute ) {
		$minute_array[$i] = (string)$minute;
		$i++;
	}
	$period_array = array('AM','PM');
	
	echo "<div class='wrap-fields' data-count='".$count."'>";
	
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'start-hour', 'label'=>'Hour', 'size'=>2, 'value'=>$data['start-hour'], 'all_fields'=>$hour_array, 'type'=>'select','count'=>$count) );
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'start-minute', 'label'=>'Minute', 'size'=>2, 'separator'=>':', 'value'=>$data['start-minute'], 'all_fields'=>$minute_array, 'type'=>'select','count'=>$count) );
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'start-period', 'label'=>'Period', 'size'=>2, 'value'=>$data['start-period'], 'all_fields'=>$period_array, 'type'=>'select','count'=>$count) );
	
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'end-hour', 'label'=>'Hour', 'size'=>2,'separator'=>'-', 'value'=>$data['end-hour'], 'all_fields'=>$hour_array, 'type'=>'select','count'=>$count) );
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'end-minute', 'label'=>'Minute', 'size'=>2, 'separator'=>':', 'value'=>$data['end-minute'], 'all_fields'=>$minute_array, 'type'=>'select','count'=>$count) );
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'end-period', 'label'=>'Period', 'size'=>2, 'value'=>$data['end-period'], 'all_fields'=>$period_array, 'type'=>'select','count'=>$count) );
	
	$field->input_field( array( 'field_type'=>$type, 'multiple'=>$multiple,'field_id'=>'days', 'value'=>$data['days'], 'selected_fields'=>$data['days'], 'all_fields'=>profile_cct_list_of_days(), 'type'=>'multiple','count'=>$count) );
	
	if (!$count)
		echo '<hr />';
	else {
	 	echo ' <a class="remove-fields button" href="#">Remove</a>';
	 	echo '<hr />';
	}
	
	echo "</div>";
}


function profile_cct_officehours_display_shell( $action, $options, $data=null ) {
	
	if( is_object($action) ):
		$post = $action;
		$action = "display";
		$data = $options['args']['data'];
		$options = $options['args']['options'];
	endif;
	
	$field = Profile_CCT::get_object(); // prints "Creating new instance."
	
	$default_options = array(
		'type' => 'officehours',
		'width' => 'full',
		'hide_label'=>true,
		'before'=>'',
		'after' =>''
		);
	
	$options = (is_array($options) ? array_merge($default_options,$options): $default_options );
	
	if( !$field->is_array_empty($data , array('start-hour','start-minute','start-period','end-hour','end-minute','end-period') ) ||  $action == "edit" ):
		$field->start_field($action,$options);
		
		if( $field->is_data_array( $data ) ):
			foreach($data as $item_data):
				
				if( !$field->is_array_empty($item_data , array('start-hour','start-minute','start-period','end-hour','end-minute','end-period') ) ||  $action == "edit" ):
					profile_cct_officehours_display($item_data,$options);
					
				endif;
			endforeach;
			
		else:
			
			profile_cct_officehours_display($data,$options);
		endif;
		$field->end_field( $action, $options );
	else:
		echo $options['empty'];
	endif;

}
function profile_cct_officehours_display( $data, $options ){

	extract( $options );
	$show = (is_array($show) ? $show : array());
	
	$field = Profile_CCT::get_object();
	$separator = '';
	
	$field->display_text( array( 'field_type'=>$type, 'class'=>'officehours', 'type'=>'shell','tag'=>'div') );
	
	if ( isset( $data['days'] ) ) {
		foreach( $data['days'] as $day ) {
			$field->display_text( array( 'field_type'=>$type, 'class'=>'days','default_text'=>'Monday', 'separator'=>$separator, 'value'=>$day, 'type'=>'text' ) );
			$separator = ',';
		}
	}
	else
		$field->display_text( array( 'field_type'=>$type, 'class'=>'days', 'default_text'=>'Monday', 'type'=>'text' ) );
	
	
	$field->display_text( array( 'field_type'=>$type, 'class'=>'start-hour','default_text'=>'11', 'value'=>$data['start-hour'], 'type'=>'text' ) );
	$field->display_text( array( 'field_type'=>$type, 'class'=>'start-minute','default_text'=>'15', 'separator'=>':', 'value'=>$data['start-minute'], 'type'=>'text') );
	$field->display_text( array( 'field_type'=>$type, 'class'=>'start-period','default_text'=>'AM', 'value'=>$data['start-period'], 'type'=>'text') );
	
	$field->display_text( array( 'field_type'=>$type, 'class'=>'end-hour','default_text'=>'12', 'separator'=>'-', 'value'=>$data['end-hour'], 'type'=>'text' ) );
	$field->display_text( array( 'field_type'=>$type, 'class'=>'end-minute','default_text'=>'05', 'separator'=>':', 'value'=>$data['end-minute'], 'type'=>'text') );
	$field->display_text( array( 'field_type'=>$type, 'class'=>'end-period','default_text'=>'PM', 'value'=>$data['end-period'], 'type'=>'text') );
	
	$field->display_text( array( 'field_type'=>$type, 'type'=>'end_shell','tag'=>'div') );

}

function profile_cct_list_of_days() {
	return array(
		"Monday",
		"Tuesday",
		"Wednesday",
		"Thursday",
		"Friday",
		"Saturday",
		"Sunday"	
	);
}