<?php  
namespace SSync;

class SSync_Endpoints{
	
	private $menu_name; 
	private $plugin_url;
	private $settingsNames;
	private $text;
 
	
	function __construct($param){

      $this->settingsNames = [
                        'ssync_secret',
                        'ssync_mode',
                        'ssync_hours',
                        'ssync_user_email',
                        'ssync_staging_email',
                        'ssync_staging_url', 
                        'ssync_live_url',
                        ];
		$this->plugin_url= $param['plugin_url']; 

      add_action('wp_ajax_nopriv_getmode', array($this,'get_mode')); 
      add_action('wp_ajax_nopriv_startsync', array($this,'get_database')); 
      add_action('wp_ajax_getmode', array($this,'get_mode')); 
      add_action('wp_ajax_startsync', array($this,'get_database')); 
      add_action('admin_notices', array($this,'display_messages')); 
      add_filter('allowed_http_origins',[$this,'allowed_http_origins']);
      add_action('init',[$this,'process_sql']);

   } 

   function display_messages(){
      if(isset($_GET['ssync_message']) && $_GET['ssync_message']=='success'){
?><div class="notice notice-success">
                     <p><?php  _e('Congratulations! Database was synchronized successfully.', 'tss'); ?></p>
                  </div><?php
      }
      if(isset($_GET['ssync_message']) && $_GET['ssync_message']=='unexpected-output'){
 ?><div class="notice notice-error">
                     <p><?php  _e('The live site returned an unexpected stream. Please ask the owner to check it.', 'tss'); ?></p>
                  </div><?php
      }
      if(isset($_GET['ssync_message']) && $_GET['ssync_message']=='error-key'){
     ?><div class="notice notice-error">
                     <p><?php  _e('Invalid Request.', 'tss'); ?></p>
                  </div><?php
      }
   }

   function get_mode(){

         die( get_option('ssync_mode','live') ); 
  
   }

	function allowed_http_origins($origins){
		$mode = get_option('ssync_mode','live');
		if($mode == 'live'){
			$withoutfinalslash = get_option('ssync_staging_url','');
		}else{
			$withoutfinalslash = get_option('ssync_live_url','');
		}
		if(substr($withoutfinalslash, -1) == '/') {
			$withoutfinalslash = substr($withoutfinalslash, 0, -1);
		} 
		$origins[] = $withoutfinalslash;
		return $origins;
	}

	function get_database(){

      global $wpdb;

      $mode = get_option('ssync_mode','live');
      if($mode!=='live') return;

      $current_key = get_option('ssync_secret','');
       
      if(!isset($_REQUEST['private_key']) 
         || empty(trim($current_key )) || $current_key !== trim($_REQUEST['private_key'])
      ){
         //die('Error');
      }

      ini_set('max_execution_time', 0);
 

         $tables = [ 
                     $wpdb->terms => 
                     [ 
                        'name' => 'terms',
                        'columns' => ['term_id', 'name', 'slug', 'term_group']
                     ],
                     $wpdb->termmeta => 
                     [ 
                        'name' => 'termmeta',
                        'columns' => ['meta_id', 'term_id', 'meta_key', 'meta_value']
                     ],
                     $wpdb->term_taxonomy => 
                     [ 
                        'name' => 'term_taxonomy',
                        'columns' => ['term_taxonomy_id', 'term_id', 'taxonomy', 'description', 'parent', 'count']
                     ],
                     $wpdb->term_relationships => 
                     [ 
                        'name' => 'term_relationships',
                        'columns' => ['object_id', 'term_taxonomy_id', 'term_order']
                     ],
                     $wpdb->users => 
                     [ 
                        'name' => 'users',
                        'columns' => ['ID', 'user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_activation_key', 'user_status', 'display_name' ]
                     ],
                     $wpdb->posts => 
                     [ 
                        'name' => 'posts',
                        'columns' => ['ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order', 'post_type', 'post_mime_type', 'comment_count']
                     ],
                  $wpdb->postmeta => 
                     [ 
                        'name' => 'postmeta',
                        'columns' => ['meta_id', 'post_id', 'meta_key', 'meta_value']
                     ],
                  $wpdb->links => 
                     [ 
                        'name' => 'links',
                        'columns' => ['link_id', 'link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_updated', 'link_rel', 'link_notes', 'link_rss',]
                     ],
                  $wpdb->comments => 
                     [ 
                        'name' => 'comments',
                        'columns' => ['comment_ID', 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_author_IP', 'comment_date', 'comment_date_gmt', 'comment_content', 'comment_karma', 'comment_approved', 'comment_agent', 'comment_type', 'comment_parent', 'user_id']
                     ],
                  $wpdb->commentmeta => 
                     [ 
                        'name' => 'commentmeta',
                        'columns' => ['meta_id', 'comment_id', 'meta_key', 'meta_value']
                     ],
                  $wpdb->options => 
                     [ 
                        'name' => 'options',
                        'columns' => ['option_id', 'option_name', 'option_value', 'autoload']
                     ],
                  ]; 

         $output = '';
  
         foreach ($tables as $live_name => $table_info) { 
            $lastID = 0;
            $output .= "DELETE FROM //PREFIX//$table_info[name]".($table_info['name']==='options' ? " WHERE option_name NOT IN('home','siteurl')" : '').'//DELIMITER//';   

            $result = $wpdb->get_results( "SELECT * FROM $live_name;" , ARRAY_A );
              
            if (count($result) > 0) { 
               $sequenceForTable = 0;
               $fields_list = implode( ',' , $table_info['columns'] );

               foreach( $result as $row ) {
                    
                  $values_array = [];

                  foreach ($table_info['columns'] as $key => $col) {
                     
                     if($key === 0) { 
                        //var_dump($key);
                        $lastID = $row[$col];
                     }

                        $values_array[] = "'".str_replace("'",'&#039;',$row[$col])."'";
                  }
                  $values_list = implode( ',' , $values_array );
                  if( isset($row['option_name']) && 
                  ($row['option_name'] == 'siteurl' || $row['option_name'] == 'home' || substr($row['option_name'],0,4) == 'ssyn') ){
                        continue;
                  }
                  $sql = "INSERT INTO //PREFIX//".$table_info['name']." ($fields_list) VALUES($values_list) ";
                  //update_option('tss_last',$sentence++); 
                  $output .= $sql.'//DELIMITER//';  
               }
            }  
               if( $live_name != $wpdb->term_relationships ){
                  $output .= ("ALTER TABLE //PREFIX//".$table_info['name']." AUTO_INCREMENT = ".($lastID+1).";//DELIMITER//");
                   
            }
         } 

        die('94==OK=='.$output);
 
   }

 
	function process_sql(){
      global $table_prefix, $wpdb;  

      if(!isset($_POST['ssync_stream'])){  
         //update_option('ssync_last_time',strtotime("2021-08-07 13:00:00"));
         return;
      }

      if(!isset($_POST['ssync_nonce']) || !wp_verify_nonce( $_POST['ssync_nonce'],'ssync_nonce')){
         
         header('Location: '. esc_url(add_query_arg(array('ssync_message'=>'error-key'), filter_input(INPUT_SERVER, 'REQUEST_URI'))));
         exit();
      } 

      $input_raw = file_get_contents('php://input');
      parse_str($input_raw,$input);
      $server_output = $input['ssync_stream']; 


      if(substr($server_output, 0, 8) == '94==OK=='){
         $server_output =  str_replace('94==OK==', '', $server_output);
         $server_output =  str_replace('//PREFIX//', $table_prefix,  $server_output); 
         $statements = explode('//DELIMITER//', $server_output);
         $ssyncsettings = [];
         foreach ($this->settingsNames as $value) {
            $ssyncsettings[$value] = get_option($value,'');
         } 
         //die(var_dump($ssyncsettings));
         foreach ($statements as $key => $sql) {
            if(trim($sql)=='') continue;
            //echo $sql.'<br/>';
            $wpdb->query($sql);
         }
         foreach ($this->settingsNames as $value) {  
            if($value=='ssync_last_time') continue;
            $sql = "INSERT INTO {$wpdb->options}(option_name,option_value) VALUES ('$value','".$ssyncsettings[$value]."');";
            $wpdb->query($sql);
            //echo $sql.'<br>'; 
         }
         $wpdb->query("INSERT INTO {$wpdb->options}(option_name,option_value) VALUES ('ssync_last_time','".time()."');");
         //die('QL'); 
         header('Location: '. esc_url(add_query_arg(array('ssync_message'=>'success'), filter_input(INPUT_SERVER, 'REQUEST_URI'))));
         exit();
      }
      else{
         header('Location: '. esc_url(add_query_arg(array('ssync_message'=>'unexpected-output'), filter_input(INPUT_SERVER, 'REQUEST_URI'))));
         exit();
      }
   }

}