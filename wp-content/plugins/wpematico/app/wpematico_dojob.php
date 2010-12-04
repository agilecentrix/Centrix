<?php
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');


//function for PHP error handling
function wpematico_joberrorhandler($errno, $errstr, $errfile, $errline) {
	global $wpematico_dojob_message, $jobwarnings, $joberrors;
    
	//genrate timestamp
	if (!function_exists('memory_get_usage')) { // test if memory functions compiled in
		$timestamp="<span style=\"background-color:c3c3c3;\" title=\"[Line: ".$errline."|File: ".basename($errfile)."\">".date_i18n('Y-m-d H:i.s').":</span> ";
	} else  {
		$timestamp="<span style=\"background-color:c3c3c3;\" title=\"[Line: ".$errline."|File: ".basename($errfile)."|Mem: ".wpematico_formatBytes(@memory_get_usage(true))."|Mem Max: ".wpematico_formatBytes(@memory_get_peak_usage(true))."|Mem Limit: ".ini_get('memory_limit')."]\">".date_i18n('Y-m-d H:i.s').":</span> ";
	}

	switch ($errno) {
    case E_NOTICE:
	case E_USER_NOTICE:
		$massage=$timestamp."<span>".$errstr."</span>";
        break;
    case E_WARNING:
    case E_USER_WARNING:
		$jobwarnings += 1;
		$massage=$timestamp."<span style=\"background-color:yellow;\">".__('[WARNING]','wpematico')." ".$errstr."</span>";
        break;
	case E_ERROR: 
    case E_USER_ERROR:
		$joberrors += 1;
		$massage=$timestamp."<span style=\"background-color:red;\">".__('[ERROR]','wpematico')." ".$errstr."</span>";
        break;
	case E_DEPRECATED:
	case E_USER_DEPRECATED:
		$massage=$timestamp."<span>".__('[DEPRECATED]','wpematico')." ".$errstr."</span>";
		break;
	case E_STRICT:
		$massage=$timestamp."<span>".__('[STRICT NOTICE]','wpematico')." ".$errstr."</span>";
		break;
	case E_RECOVERABLE_ERROR:
		$massage=$timestamp."<span>".__('[RECOVERABLE ERROR]','wpematico')." ".$errstr."</span>";
		break;
	default:
		$massage=$timestamp."<span>[".$errno."] ".$errstr."</span>";
        break;
    }

	if (!empty($massage)) {

		$wpematico_dojob_message .= $massage."<br />\n";

		if ($errno==E_ERROR or $errno==E_CORE_ERROR or $errno==E_COMPILE_ERROR) {//Die on fatal php errors.
			die("Fatal Error:" . $errno);
		}
		//300 is most webserver time limit. 0= max time! Give script 5 min. more to work.
		@set_time_limit(300); 
		//true for no more php error hadling.
		return true;
	} else {
		return false;
	}
}

/**
* WPeMatico PHP class for WordPress
*
*/
class wpematico_dojob {

	private $jobid=0;
	private $cfg=array();
	private $job=array();
	private $feeds=array();
	private $posts=0;

	public function __construct($jobid) {
		global $wpdb,$wpematico_dojob_message, $jobwarnings, $joberrors;
		$jobwarnings=0;
		$joberrors=0;
		@ini_get('safe_mode','Off'); //disable safe mode
		@ini_set('ignore_user_abort','Off'); //Set PHP ini setting
		ignore_user_abort(true);			//user can't abort script (close windows or so.)
		$this->jobid=$jobid;			   //set job id
		$this->cfg=get_option('wpematico'); //load config
		$jobs=get_option('wpematico_jobs'); //load jobdata
		$this->job=wpematico_check_job_vars($jobs[$this->jobid],$this->jobid);//Set and check job settings
		
		//set function for PHP user defineid error handling
		if (defined(WP_DEBUG) and WP_DEBUG)
			set_error_handler('wpematico_joberrorhandler',E_ALL | E_STRICT);
		else
			set_error_handler('wpematico_joberrorhandler',E_ALL & ~E_NOTICE);
		//Set job start settings
		$jobs[$this->jobid]['starttime']=current_time('timestamp'); //set start time for job
		$jobs[$this->jobid]['cronnextrun']=wpematico_cron_next($jobs[$this->jobid]['cron']);  //set next run
		$jobs[$this->jobid]['lastpostscount'] = 0; // Lo pone en 0 y lo asigna al final

		update_option('wpematico_jobs',$jobs); //Save job Settings

		//check max script execution tme
		if (ini_get('safe_mode') or strtolower(ini_get('safe_mode'))=='on' or ini_get('safe_mode')=='1')
			trigger_error(sprintf(__('PHP Safe Mode is on!!! Max exec time is %1$d sec.','wpematico'),ini_get('max_execution_time')),E_USER_WARNING);
		// check function for memorylimit
		if (!function_exists('memory_get_usage')) {
			ini_set('memory_limit', apply_filters( 'admin_memory_limit', '256M' )); //Wordpress default
			trigger_error(sprintf(__('Memory limit set to %1$s ,because can not use PHP: memory_get_usage() function to dynamically increase the Memory!','wpematico'),ini_get('memory_limit')),E_USER_WARNING);
		}
		//run job parts
		$count = 0;
		$this->feeds = $this->job['campaign_feeds'] ; // --- Obtengo los feeds de la campa�a
    
		foreach($this->feeds as $feed)
			$count += $this->processFeed($this->job, $feed);         #- ---- Proceso todos los feeds      

		$this->posts += $count; 

		$this->job_end(); //call regualar job end
	}
	
	
/**
   * Processes a feed
   *
   * @param   $campaign   array    Campaign data
   * @param   $feed       URL string    Feed 
   * @return  The number of items added to database
   */
  private function processFeed(&$campaign, &$feed)  {
    @set_time_limit(0);
    // Log
	 trigger_error(sprintf(__('Processing feed %1s.','wpematico'),$feed),E_USER_NOTICE);
        
    // Access the feed
    $simplepie = fetchFeed($feed, false, $campaign['campaign_max']);

    // Get posts (last is first)
    $items = array();
    $count = 0;
    
    foreach($simplepie->get_items() as $item) {
/*       if($feed->hash == $this->getItemHash($item)) {   // a la primer coincidencia ya vuelve  
        if($count == 0) $this->log('No new posts');
        break;
      }
 */      
      if($this->isDuplicate($campaign, $feed, $item)) {
			trigger_error(__('Filtering duplicated posts.','wpematico'),E_USER_NOTICE);
			break;
      }
      
      $count++;
      array_unshift($items, $item);
      
      if($count == $campaign['campaign_max']) {
			trigger_error(sprintf(__('Campaign fetch limit reached at %1s.','wpematico'),$campaign['campaign_max']),E_USER_NOTICE);
        break;
      }
    }
    
    // Processes post stack
   foreach($items as $item) {					
      $this->processItem($campaign, $feed, $item);
      //$lasthash = $this->getItemHash($item);
   }
    
    // If we have added items, let's update the hash
    if($count) {
		trigger_error(sprintf(__('%s posts added','wpematico'),$count),E_USER_NOTICE);
    }
    
    return $count;
  }
  
   private function isDuplicate(&$campaign, &$feed, &$item) {
	// Agregar variables para chequear duplicados solo de esta campa�a o de cada feed ( grabados en post_meta) o por titulo y permalink
		global $wpdb, $wp_locale, $current_blog;
		$table_name = $wpdb->prefix . "posts";  
		$blog_id 	= $current_blog->blog_id;
		$title = $item->get_title();// . $item->get_permalink();
		$query="SELECT post_title,id FROM $table_name
					WHERE ((`post_status` = 'published') OR (`post_status` = 'publish' ))
					AND post_title = '".$title."'";
					//GROUP BY post_title having count(*) > 1" ;
		$row = $wpdb->get_row($query);
		trigger_error(sprintf(__('Checking if duplicated title \'%1s\'','wpematico'),$title).': '.((!! $row) ? __('Yes') : __('No')) ,E_USER_NOTICE);
		return !! $row;
  }


   /**
   * Processes an item
   *
   * @param   $campaign   object    Campaign database object   
   * @param   $feed       object    Feed database object
   * @param   $item       object    SimplePie_Item object
   */
	function processItem(&$campaign, &$feed, &$item) {
		global $wpdb;
		trigger_error(sprintf(__('Processing item %1s','wpematico'),$item->get_title()),E_USER_NOTICE);
		 
		 // Item content
		$content = $this->parseItemContent($campaign, $feed, $item);            
		
		// Item date
	/*     if($campaign->feeddate && ($item->get_date('U') > (current_time('timestamp', 1) - $campaign->frequency) && $item->get_date('U') < current_time('timestamp', 1)))
			$date = $item->get_date('U');
		 else */
		$date = null;
			
		 // Categories
		$categories = $campaign['campaign_categories']; //$this->getCampaignData($campaign->id, 'categories');
			
		 // Meta
		$meta = array(
			'wpe_campaignid' => $this->jobid, //campaign['jobid'],
			'wpe_feed' => $feed,
			'wpe_sourcepermalink' => $item->get_permalink()
		);  
		 
		 // Create post
		$postid = $this->insertPost($wpdb->escape($item->get_title()), $wpdb->escape($content), $date, $categories, $campaign['campaign_posttype'], $campaign['campaign_author'], $campaign['campaign_allowpings'], $campaign['campaign_commentstatus'], $meta);
		
		
		// Attaching images uploaded to created post in media library 
		if(($this->cfg['imgcache'] || $this->job['campaign_imgcache']) && ($this->cfg['imgattach'])) {
			$images = $this->parseImages($content);
			$urls = $images[2];  
			if(sizeof($urls)) { // Si hay alguna imagen en el content
				trigger_error(__('Attaching images.','wpematico'),E_USER_NOTICE);
				foreach($urls as $imagen_src) {
					$this->insertfileasattach($imagen_src,$postid);
				}
			}
		}
				
		 // If pingback/trackbacks
		if($campaign['campaign_allowpings']) {
			trigger_error(__('Processing item pingbacks','wpematico'),E_USER_NOTICE);
			
			require_once(ABSPATH . WPINC . '/comment.php');
			pingback($content, $postid);      
		}        
	}
  

 /**
  * Adjunta un archivo ya subido al postid dado
  */
 	function insertfileasattach($filename,$postid) {
  		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
		  'post_mime_type' => $wp_filetype['type'],
		  'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		  'post_content' => '',
		  'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename, $postid );
		trigger_error(__('Attaching file:').$filename,E_USER_NOTICE);
		if (!$attach_id)
			trigger_error(__('Sorry, your attach could not be inserted. Something wrong happened.').print_r($filename,true),E_USER_WARNING);
		// you must first include the image.php file for the function wp_generate_attachment_metadata() to work
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id,  $attach_data );
	}
  
 /**
  * Devuelve todas las imagenes del contenido
  */
   function parseImages($text){    
		preg_match_all('/<img(.+?)src=\"(.+?)\"(.*?)>/', $text, $out);
		return $out;
	}
 
 	/* Guardo imagen en mi servidor
	EJEMPLO 
	guarda_imagen("http://ablecd.wz.cz/vendeta/fuhrer/hitler-pretorians.jpg","/usr/home/miweb.com/web/iimagen.jpg");
	Si el archivo destino ya existe guarda una copia de la forma "filename[n].ext"
	***************************************************************************************/
	function guarda_imagen ($url_origen,$archivo_destino){ 
		$mi_curl = curl_init ($url_origen); 
		if(!$mi_curl) {
			return false;
		}else{
			$i = 1;
			while (file_exists( $archivo_destino )) {
				$file_extension  = strrchr($archivo_destino, '.');    //Will return .JPEG         substr($url_origen, strlen($url_origen)-4, strlen($url_origen));
				$file_name = substr($archivo_destino, 0, strlen($archivo_destino)-strlen($file_extension));
				$archivo_destino = $file_name."[$i]".$file_extension;
				$i++;
			}
			$fs_archivo = fopen ($archivo_destino, "w"); 
//			if(is_writable($fs_archivo)) {
				curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo); 
				curl_setopt ($mi_curl, CURLOPT_HEADER, 0); 
				curl_exec ($mi_curl); 
				curl_close ($mi_curl); 
				fclose ($fs_archivo); 
				return $archivo_destino;
//			}
		}
	} 

 
  /**
   * Parses an item content
   *
   * @param   $campaign       object    Campaign database object   
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   */
	function parseItemContent(&$campaign, &$feed, &$item) {  
		$content = $item->get_content();
    
		 // Caching images
		if($this->cfg['imgcache'] || $this->job['campaign_imgcache']) {
			$images = $this->parseImages($content);
			$urls = $images[2];  

			if(sizeof($urls)) { // Si hay alguna imagen en el feed
				trigger_error(__('Uploading images.','wpematico'),E_USER_NOTICE);

				foreach($urls as $imagen_src) {
					$bits = @file_get_contents($imagen_src);
					$name = substr(strrchr($imagen_src, "/"),1);
					$afile = wp_upload_bits( $name, NULL, $bits);
					if(!$afile['error']) 
						$content = str_replace($imagen_src, $afile['url'], $content);
					else {  // Si no la pudo subir intento con mi funcion
						trigger_error('wp_upload_bits error:'.print_r($afile,true).', trying custom function.',E_USER_WARNING);
						$upload_dir = wp_upload_dir();
						$imagen_dst = $upload_dir['path'] . str_replace('/','',strrchr($imagen_src, '/'));
						$imagen_dst_url = $upload_dir['url']. '/' . str_replace('/','',strrchr($imagen_src, '/'));

						if(in_array(str_replace('.','',strrchr($imagen_dst, '.')),explode(',','jpg,gif,png,tif,bmp'))) {   // -------- Controlo extensiones permitidas
							trigger_error('imagen_src='.$imagen_src.' <b>to</b> imagen_dst='.$imagen_dst.'<br>',E_USER_NOTICE);
							$newfile = $this->guarda_imagen($imagen_src, $imagen_dst);
						}
						if($newfile)	$content = str_replace($imagen_src, $imagen_dst_url, $content);
						else trigger_error('Upload file failed:'.$imagen_dst,E_USER_WARNING);
					}
				} 
			}
		}
    
/*     // Template parse           TODO
    $vars = array(
      '{content}',
      '{title}',
      '{permalink}',
      '{feedurl}',
      '{feedtitle}',
      '{feedlogo}',
      '{campaigntitle}',
      '{campaignid}',
      '{campaignslug}'
    );
    
    $replace = array(
      $content,
      $item->get_title(),
      $item->get_link(),
      $feed->url,
      $feed->title,
      $feed->logo,
      $campaign->title,
      $campaign->id,
      $campaign->slug
    );
    
    $content = str_ireplace($vars, $replace, ($campaign->template) ? $campaign->template : '{content}');
 */    
		// Rewrite
		$rewrites = $this->job['campaign_rewrites'];
		for ($i = 0; $i < count($this->job['campaign_rewrites']['origin']); $i++) {
			$origin = $this->job['campaign_rewrites']['origin'][$i];
			if(isset($this->job['campaign_rewrites']['rewrite'][$i])) {
			  $reword = isset($this->job['campaign_rewrites']['relink'][$i]) 
							  ? '<a href="'. $this->job['campaign_rewrites']['relink'][$i] .'">' . $this->job['campaign_rewrites']['rewrite'][$i] . '</a>' 
							  : $this->job['campaign_rewrites']['rewrite'][$i];
			  
			if($this->job['campaign_rewrites']['regex'][$i]) {
				$content = preg_replace($origin, $reword, $content);
			}else
				$content = str_ireplace($origin, $reword, $content);
			}else if(isset($this->job['campaign_rewrites']['relink'][$i])) 
						$content = str_ireplace($origin, '<a href="'. $this->job['campaign_rewrites']['relink'][$i] .'">' . $origin . '</a>', $content);
		}
		// End rewrite
		
		return $content;
	} // End ParseItemContent

 /**
   * Writes a post to blog  *   *  
   * @param   string    $title            Post title
   * @param   string    $content          Post content
   * @param   integer   $timestamp        Post timestamp
   * @param   array     $category         Array of categories
   * @param   string    $status           'draft', 'published' or 'private'
   * @param   integer   $authorid         ID of author.
   * @param   boolean   $allowpings       Allow pings
   * @param   boolean   $comment_status   'open', 'closed', 'registered_only'
   * @param   array     $meta             Meta key / values
   * @return  integer   Created post id
   */
  function insertPost($title, $content, $timestamp = null, $category = null, $status = 'draft', $authorid = null, $allowpings = true, $comment_status = 'open', $meta = array()) {
    $date = ($timestamp) ? gmdate('Y-m-d H:i:s', $timestamp + (get_option('gmt_offset') * 3600)) : null;
    $postid = wp_insert_post(array(
    	'post_title' 	            => $title,
  		'post_content'  	        => $content,
  		'post_content_filtered'  	=> $content,
  		'post_category'           => $category,
  		'post_status' 	          => $status,
  		'post_author'             => $authorid,
  		'post_date'               => $date,
  		'comment_status'          => $comment_status,
  		'ping_status'             => $allowpings
    ));
    	
		foreach($meta as $key => $value) 
			$this->insertPostMeta($postid, $key, $value);			
		
		return $postid;
  }
  
  /**
   * insertPostMeta   *   *
   */
	function insertPostMeta($postid, $key, $value) {
		global $wpdb;
		
		$result = $wpdb->query( "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value ) " 
					                . " VALUES ('$postid','$key','$value') ");
					
		return $wpdb->insert_id;		
	}
    
	private function job_end() {
		$jobs=get_option('wpematico_jobs');
		$jobs[$this->jobid]['lastrun']=$jobs[$this->jobid]['starttime'];
		$jobs[$this->jobid]['lastruntime']=current_time('timestamp')-$jobs[$this->jobid]['starttime'];
		$jobs[$this->jobid]['starttime']='';
		$jobs[$this->jobid]['postscount'] += $this->posts; // Suma los posts procesados 
		$jobs[$this->jobid]['lastpostscount'] = $this->posts; // posts procesados esta vez
		update_option('wpematico_jobs',$jobs); //Save Settings
		$this->job['lastrun']=$jobs[$this->jobid]['lastrun'];
		$this->job['lastruntime']=$jobs[$this->jobid]['lastruntime'];
		trigger_error(sprintf(__('Job done in %1s sec.','wpematico'),$this->job['lastruntime']),E_USER_NOTICE);
		}
	
	public function __destruct() {
	global $wpematico_dojob_message;
	//Send mail with log
		$sendmail=false;
		if ($joberrors>0 and $this->job['mailerroronly'] and !empty($this->job['mailaddresslog']))
			$sendmail=true;
		if (!$this->job['mailerroronly'] and !empty($this->job['mailaddresslog']))
			$sendmail=true;
		if ($sendmail) {
			$mailbody = "WPeMatico Log"."\n";
			$mailbody .= __("Campaign Name:","wpematico")." ".$this->job['name']."\n";
			if (!empty($joberrors))
				$mailbody.=__("Errors:","wpematico")." ".$joberrors."\n";
			if (!empty($jobwarnings))
				$mailbody.=__("Warnings:","wpematico")." ".$jobwarnings."\n";

			$mailbody.="\n".$wpematico_dojob_message;
			wp_mail($this->job['mailaddresslog'],__('WPeMatico Log from','wpematico').' '.date_i18n('Y-m-d H:i').': '.$this->job['name'] ,$mailbody,'','');  //array($this->logdir.$this->logfile  
		}
		
		$Suss = sprintf(__('Job done in %1s sec.','wpematico'),$this->job['lastruntime']) . '  ' . sprintf(__('Processed Posts: %1s','wpematico'),$this->posts);
		$message = '<div id="message" class="updated fade">'.$Suss.'  <a href="JavaScript:Void(0);" style="font-weight: bold; text-decoration: none;" onclick="jQuery(\'\#log_message\').toggle();">' . __('Show detailed Log','wpematico') . '.</a></div>';
		$wpematico_dojob_message = $message .'<div id="log_message" style="display:none;" class="error fade">'.$wpematico_dojob_message.'</div>';

		return $wpematico_dojob_message;
	}
}
?>