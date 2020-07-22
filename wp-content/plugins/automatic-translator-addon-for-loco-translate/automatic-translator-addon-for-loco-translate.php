<?php
/*
Plugin Name:Automatic Translate Addon For Loco Translate
Description:Auto language translator add-on for Loco Translate official plugin version 2.3.3 or lower to translate plugins and themes translation files into any language via fully automatic machine translations via IBM Watson Translate API.
Version:1.9.1
License:GPL2
Text Domain:loco-translate-addon
Domain Path:languages
Author:Cool Plugins
Author URI:https://coolplugins.net/
 */
namespace LocoAutoTranslateAddon;
use LocoAutoTranslateAddon\Helpers\Helpers;
use LocoAutoTranslateAddon\Helpers\Atlt_rollback;
 /**
 * @package Loco Automatic Translate Addon
 * @version 1.9
 */
if (!defined('ABSPATH')) {
    die('WordPress Environment Not Found!');
}

define('ATLT_FILE', __FILE__);
define('ATLT_URL', plugin_dir_url(ATLT_FILE));
define('ATLT_PATH', plugin_dir_path(ATLT_FILE));
define('ATLT_VERSION', '1.9.1');

class LocoAutoTranslate
{
    public function __construct()
    { 
        register_activation_hook( ATLT_FILE, array( $this, 'atlt_activate' ) );
        register_deactivation_hook( ATLT_FILE, array( $this, 'atlt_deactivate' ) );
        if(is_admin()){

        // Only loged in user can perform this AJAX request
        add_action('wp_ajax_atlt_rollback_request', array($this, 'atlt_rollback_request'));
        add_action('plugins_loaded', array($this, 'atlt_check_required_loco_plugin'));
        /*** Template Setting Page Link inside Plugins List */
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this,'atlt_settings_page_link'));
        add_action( 'admin_enqueue_scripts', array( $this,'atlt_enqueue_scripts') );
        add_action('wp_ajax_free_autotranslate_handler',array($this,'atlt_free_autotranslate_handler'), 100);
        add_action('wp_ajax_free_test_api_provider',array($this,'atlt_free_test_api_provider'));
        add_action('init',array($this,'checkStatus'));
        add_action('init',array($this,'updateSettings'));
        add_action('plugins_loaded', array($this,'include_files'));
        }   
    }
    
    /**
     * This function hooked with AJAX to rollback loco translate plugin
     */
    public function atlt_rollback_request(){

        require ATLT_PATH . 'includes/Helpers/Atlt_rollback.php';
        $request = new Atlt_rollback();
        $response = $request->rollback();
        die(json_encode($response) );

    }

    public function use_loco_autotranslate_notice(){
          //get the current screen
    $screen = get_current_screen();
    $show_in_screens = array('loco-translate_page_loco-atlt',
                        'loco-translate_page_loco-atlt-register',
                        'loco-translate_page_loco-plugin',
                        'loco-translate_page_loco-theme');
    //return if not plugin settings page 
    //To get the exact your screen ID just do ver_dump($screen)

    $rollback_notice = '';
    if( $screen->id != 'loco-translate_page_loco-atlt' ){
        $link = admin_url( 'admin.php?page=loco-atlt' );
        $rollback_notice = __(' You can rollback through <a href="'.$link.'">settings</a> page.', 'loco-translate-addon');
    }

    if (in_array($screen->id, $show_in_screens ) )
        {
        $loco_vesion=loco_plugin_version();
        if (version_compare( $loco_vesion, '2.4', '>=')) {
            if (current_user_can('activate_plugins')) {
                $url= admin_url( 'admin.php?page=loco-config&action=apis');
                echo '<div class="error"><p style="font-size:16px;">' . 
                sprintf(__(
                    '<strong style="color:red">Notice from <a href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/" target="_blank">Loco Addon</a> Owner:</strong> <strong>Automatic Translate Addon For Loco Translate</strong> is only compatible with Loco Translate official plugin version <strong>2.3.3</strong> or lower. Currently you are using Loco Translate official plugin version 2.4.0 or higher. Please rollback to use all features of automatic translate addon.'.$rollback_notice, 
                'loco-translate-addon'),
                 esc_url($url)
                 ) . '</p></div>';
             }
          }
        }
    }
    // update settings
    public function updateSettings(){
        if(get_option( 'atlt-ratingDiv')){
            update_option('atlt-already-rated',get_option( 'atlt-ratingDiv'));
            delete_option( 'atlt-ratingDiv');
        }
    }
    /**
     * create 'settings' link in plugins page
     */
    public function atlt_settings_page_link($links){
        $links[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'admin.php?page=loco-atlt') ) .'">Settings</a>';
        $links[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'admin.php?page=loco-atlt-register') ) .'">License</a>';
        return $links;
    }

   /*
   |----------------------------------------------------------------------
   | required php files
   |----------------------------------------------------------------------
   */
   public function include_files()
   {
  
      if ( is_admin() ) {
            include_once ATLT_PATH .'includes/Helpers/Helpers.php';
            include_once ATLT_PATH . 'includes/Core/class.settings-api.php';
            include_once ATLT_PATH . 'includes/Core/class.settings-panel.php';
            new Core\Settings_Panel();
            include_once ATLT_PATH . "includes/ReviewNotice/class.review-notice.php";
            new ALTLReviewNotice\ALTLReviewNotice(); 
            include_once ATLT_PATH . 'includes/Feedback/class.feedback-form.php';
            new FeedbackForm\FeedbackForm();
            include_once ATLT_PATH . 'includes/Register/LocoAutomaticTranslateAddonPro.php';
   
            include_once ATLT_PATH .'includes/ibm-translator/class.ibm-translator.php';
        } 
        
   }
   /*
   |----------------------------------------------------------------------
   | Ajax callback handler
   |----------------------------------------------------------------------
   */
  public function atlt_free_autotranslate_handler()
  {
      // verify request
    if ( ! wp_verify_nonce($_REQUEST['nonce'], 'atlt_nonce' ) ) {
        echo  $this->errorResponse('Request Time Out. Please refresh your browser window.');
        die();
        } else {
            // user status
           $status=Helpers::atltVerification();
           if($status['type']=="free" && $status['allowed']=="no"){
                echo  $this->errorResponse('You have consumed API daily limit');
                die();
             }

           // get request vars
           if (empty($_REQUEST['data'])) {
            echo  $this->errorResponse('No String Found');
            die();
           }  
       if(isset($_REQUEST['data'])){
           $responseArr=array();
           $response=array();
           $requestData = $_REQUEST['data'];
           $targetLang=$_REQUEST['targetLan'];
           $sourceLang=$_REQUEST['sourceLan'];
           if($targetLang=="nb" || $targetLang=="nn"){
               $targetLang="no";
           }
           $request_chars  = $_REQUEST['requestChars'];
           $totalChars  = $_REQUEST['totalCharacters'];
           $requestType=$_REQUEST['strType'];  
          $apiType=$_REQUEST['apiType'];  
           $stringArr= json_decode(stripslashes($requestData),true);  
      
      if($apiType=="yandex"){
           // grab API keys
           $api_key = Helpers::getAPIkey("yandex");
                   if(empty($api_key)|| $api_key==""){
                    echo  $this->errorResponse('You have not Entered yandex API Key');
                    die();
                }
              $apiKey = $api_key;
                if(Helpers::yandexSLangList($targetLang)==false){
                    echo  $this->errorResponse('Yandex Translator Does not support this language');
                    die();
                }
                if(is_array( $stringArr)&& !empty($stringArr))
                {
                   $response=$this->yandex_api_call($stringArr,$targetLang,$sourceLang,$requestType,$apiKey);
                   if(is_array($response) && $response['code']==200)
                    {
                        // grab translation count data
                        $responseArr['code']=200;       
                        $responseArr['translatedString']= $response['text'];        
                        $responseArr['stats']= $this->saveStringsCount($request_chars,$totalChars,$apiType);
                    }else if(isset($response['code'])){
                        $responseArr['code']=$response['code'];  
                        $responseArr['error']=$response['message'];
                    }else{
                        $responseArr['error']=$response;
                        $responseArr['code']=500;
                    }
            }  
        }else{
           // grab API keys
           $api_key = Helpers::getAPIkey("ibm");
                   if(empty($api_key)|| $api_key==""){
                    echo  $this->errorResponse('You have not Entered IBM API Key');
                    die();
                }
              $apiKey = $api_key;
              $keys_arr= get_option('atlt_register');
                    if(isset($keys_arr['atlt_ibm-translate-url'])){
                        $args['base'] =$keys_arr['atlt_ibm-translate-url'].'/v3/translate?version=2018-05-01';   
                    }
                if(Helpers::ibmSLangList($targetLang)==false){
                    echo  $this->errorResponse('IBM Translator Does not support this language');
                    die();
                }
                if(is_array( $stringArr)&& !empty($stringArr))
                {
                   $args['key']= $apiKey;
                    $args['from']=$sourceLang;
                    $args['to']=$targetLang;
                    $args['text']=$stringArr;
                    $ibm_obj= new ibmTranslator(); 
                    $response=$ibm_obj->translate($args);
                  if(is_array($response) && $response['code']==200)
                    {
                        // grab translation count data
                        $responseArr['code']=200;       
                        $responseArr['translatedString']= $response['translation'];        
                        $responseArr['stats']= $this->saveStringsCount($request_chars,$totalChars,$apiType);
                    }else if(isset($response['code'])){
                        $responseArr['code']=$response['code'];  
                        $responseArr['error']=$response['error'];
                    }else{
                        $responseArr['error']=$response;
                        $responseArr['code']=500;
                    } 
              
            } 
        }   
    
            die(json_encode($responseArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
       }  
    }
 }


   /*
   |----------------------------------------------------------------------
   | check User Status
   |----------------------------------------------------------------------
   */
   public function checkStatus(){
    Helpers::checkPeriod();
    $key=Helpers::getLicenseKey();
    if(Helpers::validKey( $key) && Helpers::proInstalled()==false){
      add_action('admin_notices', array($this, 'atlt_pro_install_notice'));
    }
   }
   /*
   |----------------------------------------------------------------------
   | check if required "Loco Translate" plugin is active
   | also register the plugin text domain
   |----------------------------------------------------------------------
   */
   public function atlt_check_required_loco_plugin()
   {
      if (!function_exists('loco_plugin_self')) {
         add_action('admin_notices', array($this, 'atlt_plugin_required_admin_notice'));
      }

    if (function_exists('loco_plugin_self')) {
        add_action('admin_notices',array($this,'use_loco_autotranslate_notice'));
    }
      load_plugin_textdomain('loco-translate-addon', false, basename(dirname(__FILE__)) . '/languages/');
   }

    /*
   |----------------------------------------------------------------------
   | Install Loco Automatic Translate Addon Pro notice
   |----------------------------------------------------------------------
   */
  public function atlt_pro_install_notice()
  {

     if (current_user_can('activate_plugins')) {
        $key=Helpers::getLicenseKey();
        $url =esc_url( add_query_arg( 'license-key',$key , 'https://locoaddon.com/data/download-plugin.php' ) );
        $title = "Loco Automatic Translate Addon Pro";

        if( class_exists( 'LocoAutoTranslateAddonPro' ) ){
            // no further execution required
            return;
        }
        
        if( false == file_exists( WP_PLUGIN_DIR . '/loco-automatic-translate-addon-pro') ){
            echo '<div class="error loco-pro-missing" style="border:2px solid;border-color:#dc3232;"><p>' . 
            sprintf('You are using <strong>%s</strong> license. Please also install and activate <strong>%s</strong> plugin to enjoy all premium featues and automatic premium updates.</p>
            <p><a href="%s" target="_blank" title="%s" class="button button-primary"><strong>Download %s plugin</strong></a> and install it, you can also download it from <a href="https://locoaddon.com/my-account/downloads/" target="_blank">https://locoaddon.com/my-account/downloads/</a>', 
            esc_attr($title),esc_attr($title),esc_url($url),esc_attr($title),esc_attr($title)) . '.</p></div>';
        }else{
            echo '<div class="error loco-pro-missing" style="border:2px solid;border-color:#dc3232;"><p>' . 
            sprintf('You are using <strong>%s</strong> license. Please also activate <strong>%s</strong> plugin to enjoy all premium featues and automatic premium updates.</p>', 
            esc_attr($title),esc_attr($title)) . '</p></div>';
        }

     }
  }

  /*
   |----------------------------------------------------------------------
   | Notice to 'Admin' if "Loco Translate" is not active
   |----------------------------------------------------------------------
   */
  public function atlt_plugin_required_admin_notice()
  {
     if (current_user_can('activate_plugins')) {
        $url = 'plugin-install.php?tab=plugin-information&plugin=loco-translate&TB_iframe=true';
        $title = "Loco Translate";
        $plugin_info = get_plugin_data(__FILE__, true, true);
        echo '<div class="error"><p>' . 
        sprintf(__('In order to use <strong>%s</strong> plugin, please install and activate the version 2.3.3 of <a href="%s" class="thickbox" title="%s">%s</a>', 
        'loco-translate-addon'),
         $plugin_info['Name'], esc_url($url),
          esc_attr($title), esc_attr($title)) . '.</p></div>';

        deactivate_plugins(__FILE__);
     }
  }

   /*
   |----------------------------------------------------------------------
   | Verify API's working or not
   |----------------------------------------------------------------------
   */
   public function atlt_free_test_api_provider(){
    if ( ! wp_verify_nonce($_REQUEST['nonce'], 'atlt_nonce' ) ) {
        die(json_encode(array('code' =>500, 'message' => 'Request Time Out. Please refresh your browser window.')));
    } else {
       $text = $_REQUEST['text'];
       $targetLang=$_REQUEST['target'];
       $sourceLang=$_REQUEST['source'];
       $apikey=$_REQUEST['apikey'];
       $apiType=$_REQUEST['apiprovider'];

    if( $apiType=="yandex"){

       $strArr[]=$text;
       $requestType="plain";
            $response=$this->yandex_api_call(
             $strArr,$targetLang,$sourceLang,$requestType,$apikey);
             $responseArr['response']=$response;
            if(is_array($response) && $response['code']==200)
            {
                // grab translation count data
                $responseArr['code']=200;       
                $responseArr['translatedString']= $response['text'];        
            }else if(isset($response['code']) && isset($response['message'])){
                $responseArr['code']= $response['code'];  
                $responseArr['error']= $response['message'];
            }else{
                $responseArr['code']=500;  
                $responseArr['error']= $response;
            }
    }else{
                    $strArr[]=$text;
                    $args['from']=$sourceLang;
                    $args['to']=$targetLang;
                    $args['text']=$strArr;

                    $keys_arr= get_option('atlt_register');
                    if(isset($keys_arr['atlt_ibm-translate-url'])){
                        $args['base'] =$keys_arr['atlt_ibm-translate-url'].'/v3/translate?version=2018-05-01';   
                    }
                    $args['key'] = $apikey;
                    $ibm_obj= new ibmTranslator(); 
                    $response=$ibm_obj->translate($args);
                    if(is_array($response) && $response['code']==200)
                    {
                        // grab translation count data
                        $responseArr['code']=200;       
                        $responseArr['translatedString']= $response['translation'];        
                    }else if(isset($response['code']) && isset($response['error'])){
                        $responseArr['code']= $response['code'];  
                        $responseArr['error']= $response['error'];
                    }else{
                        $responseArr['code']=500;  
                        $responseArr['error']= $response;
                    }
                   
    }
        die(json_encode($responseArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
   }
}

 /*
   |----------------------------------------------------------------------
   | error response creator
   |----------------------------------------------------------------------
   */
  public function errorResponse($message){
    $error=[];
        if($message){
            $error['error']['code']=800;
            $error['error']['message']=$message;
        }
        return json_encode($error);
    }

   /*
   |----------------------------------------------------------------------
   | Save string usage 
   |----------------------------------------------------------------------
   */
    public function saveStringsCount($request_chars,$totalChars,$apiType)
    {

        if($apiType=="yandex"){
            $today_translated = Helpers::ytodayTranslated( $request_chars);
            $monthly_translated = Helpers::ymonthlyTranslated( $request_chars);
            /** Calculate the total time save on translation */
            $session_time_saved = Helpers::atlt_time_saved_on_translation( $totalChars);
            $total_time_saved = Helpers::atlt_time_saved_on_translation($totalChars);
            // create response array
            $stats=array(
                            'todays_translation'=>$today_translated,
                        'total_translation'=>$monthly_translated,
                        'time_saved'=> $session_time_saved,
                        'total_time_saved'=>$total_time_saved,
                        'totalChars'=>$totalChars
                    );
        }else{
            $today_translated = Helpers::iTodayTranslated( $request_chars);
            $monthly_translated = Helpers::iMonthlyTranslated( $request_chars);
            /** Calculate the total time save on translation */
            $session_time_saved = Helpers::atlt_time_saved_on_translation( $totalChars);
            $total_time_saved = Helpers::atlt_time_saved_on_translation($totalChars);
            // create response array
            $stats=array(
                        'todays_translation'=>$today_translated,
                        'total_translation'=>$monthly_translated,
                        'time_saved'=> $session_time_saved,
                        'total_time_saved'=>$total_time_saved,
                        'totalChars'=>$totalChars
                    );
        }
        return $stats;
    }

 /*
   |------------------------------------------------------
   |   Send Request to  yandex API
   |------------------------------------------------------
  */
  public function yandex_api_call($stringArr,$target_language,$source_language,$requestType,$apiKey){
    // create query string 
    $queryString='';
    $langParam = $source_language.'-'.$target_language;
  
    if(is_array($stringArr)){
        foreach($stringArr as $str){
            $queryString.='&text='.urlencode($str);
        }
    }
    // build query
    $buildReqURL='';
    $buildReqURL.='https://translate.yandex.net/api/v1.5/tr.json/translate';
    $buildReqURL.='?key=' . $apiKey . '&lang=' . $langParam.'&format='.$requestType;
    $buildReqURL.=$queryString;
    // get API response 
    $response = wp_remote_get($buildReqURL, array('timeout'=>'180'));

    if (is_wp_error($response)) {
        return $response->get_error_message();; // Bail early
    }
    $body = wp_remote_retrieve_body($response);
    // convert string into assoc array
    $data = json_decode( $body, true);  
    return $data; 
}

  /*
   |------------------------------------------------------------------------
   |  Enqueue required JS file
   |------------------------------------------------------------------------
   */
   function atlt_enqueue_scripts(){
    wp_deregister_script('loco-js-editor');
    wp_register_script( 'sweet-alert', ATLT_URL.'assets/sweetalert/sweetalert.min.js', array('loco-js-min-admin'),false, true);
    //sweet alert for settings panel
    wp_register_script( 'settings-sweet-alert', ATLT_URL.'assets/sweetalert/sweetalert.min.js',array('jquery'),false, true);
    wp_register_script( 'test-api', ATLT_URL.'assets/js/api-testing.js', array('jquery','settings-sweet-alert'));
    wp_register_script( 'atlt-rollback', ATLT_URL.'assets/js/atlt-rollback.js', array('jquery'));
        if(Helpers::userType()=="free"){
                wp_register_script( 'loco-js-editor', ATLT_URL.'assets/js/loco-js-editor.js', array('loco-js-min-admin'),ATLT_VERSION, true);
        }else{
            // if PRO version is installed then load assets
            if(Helpers::proInstalled() && version_compare(ATLT_PRO_VERSION,'1.1', '>=')){
                wp_register_script( 'loco-js-editor', ATLT_PRO_URL.'assets/js/loco-js-editor.js', array('loco-js-min-admin'),ATLT_PRO_VERSION, true);
                wp_register_script( 'loco-addon-custom', ATLT_PRO_URL.'assets/js/custom.js', array('loco-js-min-admin'),ATLT_PRO_VERSION, true);
                wp_register_style('loco-addon-custom-css', ATLT_PRO_URL.'assets/css/custom.css',null, 
                ATLT_PRO_VERSION,'all');
            }else{
                wp_register_script( 'loco-js-editor', ATLT_URL.'assets/js/loco-js-editor.js', array('loco-js-min-admin'),ATLT_VERSION, true);
            }
        }
        

    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'file-edit')
     {
         $data=array();
         wp_enqueue_script('sweet-alert');
         if(Helpers::userType()=="pro"){
            wp_enqueue_script('loco-addon-custom');
            wp_enqueue_style('loco-addon-custom-css');
         }
         wp_enqueue_script('loco-js-editor');
     
         $status=Helpers::atltVerification();
         $data['api_key']['yApiKey']=Helpers::getAPIkey("yandex");
         $data['info']['yAvailableChars']=Helpers::getAvailableChars("yandex");
         
         $data['api_key']['ibmApiKey']=Helpers::getAPIkey("ibm");
         $data['info']['iAvailableChars']=Helpers::getAvailableChars("ibm");
        
         $data['nonce']= wp_create_nonce('atlt_nonce');
         $data['endpoint']='free_autotranslate_handler';
          // @since version 1.7.1
         $settings=get_option('atlt_register');

         if(isset($settings['atlt_ibm-translate-url'])){
            $data['info']['ibm_url']=$settings['atlt_ibm-translate-url'];   
        }else{
            $data['info']['ibm_url']=false;
        }
         $index_per_request=isset($settings['atlt_index-per-request'])?$settings['atlt_index-per-request']:50;
         $data['api_key']['atlt_index-per-request']=$index_per_request;
         
         if($status['type']=="free"){
             $info=Helpers::atltVerification();
             $data['info']['type']= $info['type'];
             $data['info']['total']= $info['total'];
             if(isset($info['today'])){
             $data['info']['today']= $info['today'];
             }
             $data['info']['allowed']= $info['allowed'];
         }else{
             $data['api_key']['gApiKey']=Helpers::getAPIkey("google");
             $data['api_key']['mApiKey']=Helpers::getAPIkey("microsoft");
             $key=Helpers::getLicenseKey();
             if(Helpers::validKey( $key)){
                 $data['info']['type']="pro";
                 $data['info']['allowed']="yes";
                 $data['info']['licenseKey']=$key;
                 $data['info']['gAvailableChars']=Helpers::getAvailableChars("google");
                 $data['info']['mAvailableChars']=Helpers::getAvailableChars("microsoft");
                 if(Helpers::proInstalled()==false){
                    $data['info']['proInstalled']="no";
                  }else{
                    $data['info']['proInstalled']="yes";
                    $data['endpoint']='pro_autotranslate_handler';
                  }
                }
         }
         $extraData['preloader_path']=ATLT_URL.'/assets/images/preloader.gif';
         $extraData['gt_preview']=ATLT_URL.'/assets/images/powered-by-google.png';
         wp_localize_script('loco-js-editor', 'ATLT', $data);
         wp_localize_script('loco-js-editor', 'extradata', $extraData);
         
         if(Helpers::proInstalled() && version_compare(ATLT_PRO_VERSION,'1.1', '>=')){
                wp_localize_script('loco-addon-custom', 'ATLT', $data);
                wp_localize_script('loco-addon-custom', 'extradata', $extraData);
            }
    }    
    if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'loco-atlt')
    {
        wp_enqueue_script('settings-sweet-alert');
        wp_enqueue_script('test-api'); 
        wp_enqueue_script('atlt-rollback');
        wp_localize_script( 'atlt-rollback', 'atlt_rollback',array('key'=> wp_create_nonce('atlt_nounce_rollback_loco')) );
    }

}

   /*
   |------------------------------------------------------
   |    Plugin activation
   |------------------------------------------------------
    */
   public function atlt_activate(){
       $plugin_info = get_plugin_data(__FILE__, true, true);
       update_option('atlt_version', $plugin_info['Version'] );
       update_option("atlt-installDate",date('Y-m-d h:i:s') );
       update_option("atlt-already-rated","no");
       update_option("atlt-type","free");
   }
   /*
   |-------------------------------------------------------
   |    Plugin deactivation
   |-------------------------------------------------------
   */
   public function atlt_deactivate(){
   }
}
  
$atlt=new LocoAutoTranslate();
  

