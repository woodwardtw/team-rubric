<?php 
/*
Plugin Name: Team Rubric Helper 
Plugin URI:  https://github.com/
Description: Relies on ACF Pro and Gravity Forms
Version:     1.0
Author:      Tom Woodward
Author URI:  http://bionicteaching.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'prefix_load_scripts');

function prefix_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('team-rubric-main-js', plugin_dir_url( __FILE__) . 'js/team-rubric-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'team-rubric-main-css', plugin_dir_url( __FILE__) . 'css/team-rubric-main.css');
}

function team_rubric_build_form($content){
	global $post;
	if (get_post_type($post->ID) === 'team'){
		$html = '[gravityform id="1" title="false" description="false" ajax="true"]<div id="rubric">';
	$html .= team_rubric_members();
	$html .='<div id="table-holder"><table id="team-rubric-table">';
	$html .= '<tr><th>Name</th><th>Do your part</th><th>Share ideas</th><th>Work towards<br>agreement</th><th>Keep a positive<br>attitude</th><th>Be competent</th></tr>';
	if($post->post_type === 'team'){
		if( have_rows('members') ){

	 	// loop through the rows of data
	    while ( have_rows('members') ) : the_row();

	        // display a sub field value
	        $html .= '<tr>';
	        $html .= '<td id="' . sanitize_title(get_sub_field('member_name')) . '">'. get_sub_field('member_name') . '</td>';
	        $html .= '<td>' . team_rubric_rating_maker('your-part') . '</td>';
	        $html .= '<td>' . team_rubric_rating_maker('share-ideas') . '</td>';
	        $html .= '<td>' . team_rubric_rating_maker('agreement') . '</td>';
	        $html .= '<td>' . team_rubric_rating_maker('attitude') . '</td>';
	        $html .= '<td>' . team_rubric_rating_maker('competent') . '</td>';	        
	    endwhile;
		}
	
	}
		return $html . '</table></div></div>' . team_reporting() . $content;
	} 
	else {
		return $content;
	}
	
}

add_filter( 'the_content', 'team_rubric_build_form' );

function team_rubric_members(){
	global $post;
	$members = '<div class="team-rubric-question">Select your name</div><select id="identity" required><option selected disabled value=""></option>';
	if($post->post_type === 'team'){
		if( have_rows('members') ){
			while ( have_rows('members') ) : the_row();
				$members .= '<option value="' . sanitize_title(get_sub_field('member_name')) . '">' . get_sub_field('member_name') . '</option>';
			endwhile;
		}
	}
	return $members . '</select>';
}

function team_rubric_rating_maker($id){
	$list  = '<select id="'.$id.'"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>';
	return $list;
}

function team_rubric_members_submitted(){
	global $post;
	$members = '<div id="team-rubric-submitted">';
	if($post->post_type === 'team'){
		if( have_rows('members') ){
			while ( have_rows('members') ) : the_row();
				$members .= '<div id="' . sanitize_title(get_sub_field('member_name')) . '-check">' . get_sub_field('member_name') . '</div>';
			endwhile;
		}
	}
	return $members . '</div>';
}



function team_reporting(){
	if(current_user_can('administrator')){
		global $post;
		echo team_rubric_members_submitted();
		$team = get_the_title($post->ID);
		$search_criteria = array(
	    	'status'        => 'active',
	    	 'field_filters' => array(
		        'mode' => 'any',
		        array(
		            'key'   => '6',
		            'value' => $team
		        )
		    )
		);
	  $form_id = 1;
	  $sorting = array( 'key' => 5, 'direction' => 'ASC', 'is_numeric' => false );  
	  $paging          = array( 'offset' => 0, 'page_size' => 200);
	  $total_count     = 0;

	  $entries = GFAPI::get_entries($form_id, $search_criteria, $sorting, $paging, $total_count );
	  $people = getTeam();
	  $scores = '';
	  $assignment = [];
	  foreach ($entries as $key => $entry) {
	  		array_push($assignment, $entry[5]);
	  		//array_push($people, $entry[1]);
	  		$score = substr($entry[2], 1, -1);
	  		$scores .= $score . ',';
	        //print("<pre>".print_r($scores,true)."</pre>"); 
	        //print("<pre>".print_r($entry,true)."</pre>"); 
	  }
	 	 echo make_charts($people, $scores, $assignment);
	 	 echo '<script> const data = [' . $scores . ']</script>'; 
		}	
}

function make_charts($people, $scores, $assignment){
	 $unique_assignments = array_unique($assignment);
     $html = '';
     foreach ($unique_assignments as $key => $assignment_base) {
	   $html .= '<div id="' . sanitize_title($assignment_base) . '">';
	   $html .= '<h2>' . $assignment_base . '</h2>';
	   $html .= tableMaker($people);
	   // foreach ($assignment as $a_key => $value) {
	   // 		if ($value === $assignment_base){
	   // 			//$html .= tableMaker($people);
	   // 		}
	   // }
	}
	return $html . '</div>';
}

function tableMaker($people){
	$unique_people = array_unique($people);
	$table = '';
	 foreach ($unique_people as $key => $person) {
	 	$table .= '<table class="'. $person .'-data rubric-table">';
	 	$table .= '<tr><th>Name</th><th>Do your part</th><th>Share ideas</th><th>Work towards<br>agreement</th><th>Keep a positive<br>attitude</th><th>Be competent</th></tr>';
	 }
	 return $table . '</table>';
}

function getTeam(){
	global $post;
	$members = [];
	if( have_rows('members') ){
			while ( have_rows('members') ) : the_row();
				$name = sanitize_title(get_sub_field('member_name'));
				array_push($members,$name);
			endwhile;
		}
	return $members;
}

//ACF JSON SAVER
add_filter('acf/settings/save_json', 'team_rubric_json_save_point');
 
function team_rubric_json_save_point( $path ) {
    
    // update path
    $path = plugin_dir_path( __FILE__ )  . '/acf-json';
    // return
    return $path;
    
}


add_filter('acf/settings/load_json', 'team_rubric_json_load_point');

function team_rubric_json_load_point( $paths ) {
    
    // remove original path (optional)
    unset($paths[0]);
    
    
    // append path
    $paths[] = plugin_dir_path( __FILE__ )  . '/acf-json';
    
    
    // return
    return $paths;
    
}





//team custom post type

// Register Custom Post Type team
// Post Type Key: team

function create_team_cpt() {

  $labels = array(
    'name' => __( 'Teams', 'Post Type General Name', 'textdomain' ),
    'singular_name' => __( 'Team', 'Post Type Singular Name', 'textdomain' ),
    'menu_name' => __( 'Team', 'textdomain' ),
    'name_admin_bar' => __( 'Team', 'textdomain' ),
    'archives' => __( 'Team Archives', 'textdomain' ),
    'attributes' => __( 'Team Attributes', 'textdomain' ),
    'parent_item_colon' => __( 'Team:', 'textdomain' ),
    'all_items' => __( 'All Teams', 'textdomain' ),
    'add_new_item' => __( 'Add New Team', 'textdomain' ),
    'add_new' => __( 'Add New', 'textdomain' ),
    'new_item' => __( 'New Team', 'textdomain' ),
    'edit_item' => __( 'Edit Team', 'textdomain' ),
    'update_item' => __( 'Update Team', 'textdomain' ),
    'view_item' => __( 'View Team', 'textdomain' ),
    'view_items' => __( 'View Teams', 'textdomain' ),
    'search_items' => __( 'Search Teams', 'textdomain' ),
    'not_found' => __( 'Not found', 'textdomain' ),
    'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
    'featured_image' => __( 'Featured Image', 'textdomain' ),
    'set_featured_image' => __( 'Set featured image', 'textdomain' ),
    'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
    'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
    'insert_into_item' => __( 'Insert into team', 'textdomain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this team', 'textdomain' ),
    'items_list' => __( 'Team list', 'textdomain' ),
    'items_list_navigation' => __( 'Team list navigation', 'textdomain' ),
    'filter_items_list' => __( 'Filter Team list', 'textdomain' ),
  );
  $args = array(
    'label' => __( 'team', 'textdomain' ),
    'description' => __( '', 'textdomain' ),
    'labels' => $labels,
    'menu_icon' => '',
    'supports' => array('title', 'editor', 'revisions', 'author', 'trackbacks', 'custom-fields', 'thumbnail',),
    'taxonomies' => array(),
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'can_export' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'exclude_from_search' => false,
    'show_in_rest' => true,
    'publicly_queryable' => true,
    'capability_type' => 'post',
    'menu_icon' => 'dashicons-universal-access-alt',
  );
  register_post_type( 'team', $args );
  
  // flush rewrite rules because we changed the permalink structure
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
add_action( 'init', 'create_team_cpt', 0 );

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5d7837958d986',
	'title' => 'Team Members',
	'fields' => array(
		array(
			'key' => 'field_5d78379b3b478',
			'label' => 'Members',
			'name' => 'members',
			'type' => 'repeater',
			'instructions' => 'Enter team member name below.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 0,
			'max' => 0,
			'layout' => 'table',
			'button_label' => 'Add team member',
			'sub_fields' => array(
				array(
					'key' => 'field_5d78381d0a34f',
					'label' => 'Member Name',
					'name' => 'member_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'team',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'the_content',
		1 => 'excerpt',
		2 => 'discussion',
		3 => 'comments',
		4 => 'format',
		5 => 'page_attributes',
		6 => 'featured_image',
		7 => 'send-trackbacks',
	),
	'active' => true,
	'description' => '',
));

endif;