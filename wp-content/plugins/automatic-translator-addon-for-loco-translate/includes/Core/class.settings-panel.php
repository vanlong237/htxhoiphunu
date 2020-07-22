<?php
/*
|------------------------------------------------------------------------
|   Settings panel
|------------------------------------------------------------------------
*/
namespace LocoAutoTranslateAddon\Core;
use LocoAutoTranslateAddon\Helpers\Helpers;

if( !class_exists( 'Settings_Panel' ) ){
    class Settings_Panel {
        
        public $settings_api;
        public $PREFIX;
        public function __construct(){
                $this->settings_api = new Settings_API;
                $this->PREFIX = 'atlt_';    
                add_action('admin_init', array($this, 'admin_init' ) );
                add_action('admin_menu', array( $this, 'admin_menu' ),100 );
                add_action('admin_notices', array( $this, 'missing_api_key') );
        }


        /*
        |------------------------------------------
        |    Initialize settings section
        |------------------------------------------
        */
        public function admin_init(){
            $this->settings_api->set_sections( $this->get_settings_sections() );            
            $this->settings_api->set_fields( $this->get_settings_fields() );            
            $this->settings_api->admin_init();
          
        }

        /*
        |--------------------------------------------------------------------
        |   Create multiple section in settings page using array in $sections
        |--------------------------------------------------------------------
        */
        public function get_settings_sections()
        {
                $sections = array(
                    array(
                        'id' => $this->PREFIX.'register',
                        'title' => __('Loco Automatic Translate Addon Settings', 'cmb2'),
                    )
                );
                return $sections;
        }

        
        public function create_google_stats_tbl(){
            $month = get_option('g_translation_month');
            $total_translation = get_option('g_month_translated_chars', 0);
            $a_per_mon=0;
            $total_aval=0;
            $a_per_mon=500000;
            $total_aval=$a_per_mon-$total_translation;
            $info_tbl='<table>
            <tr>
                <th>Total Characters</th>
                <th>'.number_format($a_per_mon).' / Month</th>
                <th>Special Note</th>
            </tr>
            <tr>
                <td><strong>Used Characters</strong></td>
                <td>'.number_format($total_translation).'<br/><span>(Used This Month)</span></td>
                <td rowspan="2" style="max-width:200px;">Plugin do not provide any free translation limit. All free characters translation limit provided by translate API provider - <strong>Google</strong></td>
            </tr>
            <tr> 
                <td><strong>Available Characters</strong></td>
                <td>'.number_format($total_aval).'<br/><span>(Available This Month)</span></td>
                </tr>
            </table>
            <br/>
            <span style="color:#0e840e;">*Check translation characters usage limit statistics inside API provider dashboard if you are using same API key on multiple sites.</span>';
            return $info_tbl;
        }

        public function create_ibm_stats_tbl(){
            $month = get_option('i_translation_month');
            $total_translation = get_option('i_month_translated_chars', 0);
            $a_per_mon=0;
            $total_aval=0;
            $a_per_mon=1000000;
            $total_aval=$a_per_mon-$total_translation;
            $info_tbl='<table>
            <tr>
                <th>Total Characters</th>
                <th>'.number_format($a_per_mon).' / Month</th>
                <th>Special Note</th>
            </tr>
            <tr>
                <td><strong>Used Characters</strong></td>
                <td>'.number_format($total_translation).'<br/><span>(Used This Month)</span></td>
                <td rowspan="2" style="max-width:200px;">Plugin do not provide any free translation limit. 
                All free characters translation limit provided by translate API provider - <strong>IBM Watson</strong></td>
            </tr>
            <tr> 
                <td><strong>Available Characters</strong></td>
                <td>'.number_format($total_aval).'<br/><span>(Available This Month)</span></td>
                </tr>
            </table>
            <br/>
            <span style="color:#0e840e;">*Check translation characters usage limit statistics inside API provider dashboard if you are using same API key on multiple sites.</span>';
            return $info_tbl;
        }
        

        public function create_microsoft_stats_tbl(){
            $month = get_option('m_translation_month');
            $total_translation = get_option('m_month_translated_chars', 0);
            $a_per_mon=0;
            $total_aval=0;
            $a_per_mon=2000000;
            $total_aval=$a_per_mon-$total_translation;
            $info_tbl='<table>
            <tr>
                <th>Total Characters</th>
                <th>'.number_format($a_per_mon).' / Month</th>
                <th>Special Note</th>
            </tr>
            <tr>
                <td><strong>Used Characters</strong></td>
                <td>'.number_format($total_translation).'<br/><span>(Used This Month)</span></td>
                <td rowspan="2" style="max-width:200px;">Plugin do not provide any free translation limit. All free characters translation limit provided by translate API provider - <strong>Microsoft</strong></td>
            </tr>
            <tr> 
                <td><strong>Available Characters</strong></td>
                <td>'.number_format($total_aval).'<br/><span>(Available This Month)</span></td>
                </tr>
            </table>
            <br/>
            <span style="color:#0e840e;">*Check translation characters usage limit statistics inside API provider dashboard if you are using same API key on multiple sites.</span>';
            return $info_tbl;
        }

        public function APIIntegrationTesting($name){
            $output='';
            $desc='';
            $sourceLang='en';
            $targetLang='fr';
            $rawString='Hello';
            $apiKey='';
           $ajax_url= admin_url( 'admin-ajax.php' );
           $nonce= wp_create_nonce('atlt_nonce');
        if($name=="microsoft")
        {
             $apiKey= Helpers::getAPIkey("microsoft");
             $lbl='Microsoft Translation';
             $desc='<a target="_blank" href="https://locoaddon.com/how-to-generate-microsoft-translator-api-key/">Check Microsoft Translator API key generation guide.</a>';
        }else if($name=="ibm")
            {
                $keys_arr= get_option('atlt_register');
                $lbl='IBM Translation';
                $desc='<a target="_blank" href="https://locoaddon.com/how-to-generate-ibm-watson-translator-api-key/">How to generate IBM Watson Translator free API key?</a>';
                
                if(!empty($keys_arr['atlt_ibm-translate-api-key'])&& 
                !empty($keys_arr['atlt_ibm-translate-url'])){
                    $apiKey=$keys_arr['atlt_ibm-translate-api-key'];
                    $lbl='IBM Translate';
                    }

          }  else if($name=="google")
            {
                $apiKey= Helpers::getAPIkey("google");
                $lbl='Google Translation';
                $desc= '<a target="_blank" href="https://locoaddon.com/howto-generate-google-translate-api-key/">Check Google Translator API key generation guide.</a>';
            }else{
             $apiKey= Helpers::getAPIkey("yandex");
             $lbl='Yandex Translation';
             $desc='<span style="color:#e00b0b;">*Yandex Translate API v1 has been deprecated from June 01, 2020. Yandex has stopped providing free translate API v1 keys.<br/>If you have already generated Yandex Translate v1 API key only then you can use Yandex for automatic  translations.</span>';
            }
            if($apiKey){
           $output .='<div><a class="atlt_test_api button button-primary button-small"
            data-source="'.$sourceLang.'"
            data-nonce="'.$nonce.'"
            data-target="'.$targetLang.'"
            data-text="'.$rawString.'"
            data-apikey="'.$apiKey.'"
            data-api-provider="'.$name.'"
            data-ajaxurl="'.$ajax_url.'"
            href="#"><span class="dashicons dashicons-translation"></span> Test '.$lbl.' API  </a>   <img style="display:none;width:24px;height:24px;" class="atlt-preloader" src="'.ATLT_URL.'/assets/images/preloader.gif"></div>';
            }else{
                $output.=$desc;
            }
       
            return $output;
      
    }
        /*
        |--------------------------------------------------------------------
        |   return all settings fields to be initialized in settings page
        |--------------------------------------------------------------------
        */
        public function get_settings_fields()
        {
            $month = get_option('atlt_translation_month');
            $today = ('atlt_translation_day');
            $total_translation = get_option('atlt_month_translated_chars', 0);
            $todays_total_translation = get_option('atlt_perday_translated_chars', 0);
            $a_per_day=0;
            $a_per_mon=0;
            $today_aval=0;
            $total_aval=0;
          
            $key=Helpers::getLicenseKey();

            $LS_html='';
            if(Helpers::userType()=="free"){
                $LS_html='<table>
                <tr>
                    <th><strong>FREE User</strong></th>
                    <th><a href="?page=loco-atlt-register">Enter License Key<br/><span>(Click Here!)</span></a></th>
                    <th><a target="_blank" href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing">Buy Pro License Key<br/><span>(Increase Translation Limit!)</span></a></th>
                </tr>
                </table>';
                 $a_per_day=300000;
                 $a_per_mon=1000000;
                 $today_aval= $a_per_day-$todays_total_translation;
                 $total_aval=$a_per_mon-$total_translation;
                
            }else{
                $key=Helpers::getLicenseKey();
                if(Helpers::validKey( $key)){
                 $LS_html='<table>
                 <tr>
                     <th><strong>PREMIUM User</strong></th>
                     <th><a href="?page=loco-atlt-register">Check License Validity Status<br/><span>(Click Here!)</span></a></th>
                 </tr>
                 </table>';
                 $a_per_day=1000000;
                 $a_per_mon=10000000;
                 $today_aval= $a_per_day-$todays_total_translation;
                 $total_aval=$a_per_mon-$total_translation;
                }      
            }
            $info_tbl='<table>
            <tr>
                <th>Total Characters</th>
                <th>'.number_format($a_per_day).' / Day</th>
                <th>'.number_format($a_per_mon).' / Month</th>
            </tr>
            <tr>
                <td><strong>Used Characters</strong></td>
                <td>'.number_format($todays_total_translation).'<br/><span>(Used Today)</span></td>
                <td>'.number_format($total_translation).'<br/><span>(Used This Month)</span></td>
            </tr>
            <tr> 
                <td><strong>Available Characters</strong></td>
                <td>'.number_format($today_aval).'<br/><span>(Available Today)</span></td>
                <td>'.number_format($total_aval).'<br/><span>(Available This Month)</span></td>
                </tr>
            </table>
            <br/>
            <span style="color:#e00b0b;">*Yandex Translate API v1 has been deprecated from June 01, 2020. Yandex has stopped providing free translate API v1 keys.<br>If you have already generated Yandex Translate v1 API key only then you can use Yandex for automtatic translations.</span>';

            if( function_exists( 'loco_plugin_version' ) && version_compare( loco_plugin_version() , '2.3.3' , '>')  ):
                $settingArr[]= array(
                    'name'  => $this->PREFIX.'rollback-request',
                    'id'    => $this->PREFIX.'rollback-request',
                    'class' => $this->PREFIX.'rollbakBtn',
                    'label'  => 'Rollback Loco Translate To Version 2.3.3:',
                    'desc' => '<div id="Btloco_rollback" class="button primary">Rollback Here</div>
                    <p class="notices" style="color:#d61919;"><strong>Automatic Translate Addon For Loco Translate</strong> is only compatible with Loco Translate official plugin version <strong>2.3.3</strong> or lower.<br/>Currently you are using Loco Translate official plugin version 2.4.0 or higher.<br/>Please rollback to use all features of this addon.<br/><br/>You can also rollback via <a href="https://wordpress.org/plugins/wp-rollback/">WP Rollback plugin</a> easily if you face any issue in above rollback process.</p>
                    ',
                    'type'  => 'html',
                );
            endif;

            $pro_per_day=1000000;
            $pro_per_mon=10000000;
            $pro_info='<table>
                <tr>
                    <th style="vertical-align:middle;">Google Translate API</th>
                    <td style="min-width:200px;">'.number_format(500000).' / Month<br/>(Free Char Limit)</td>
                    <td rowspan="2" style="max-width:200px;"><p>Free characters translation limit only provided by Translate API providers, e.g. - Google, Microsoft, IBM etc.<br/>Plugin doesn\'t provide any free characters limit for automatic translations.</p></td>
                </tr>
                <tr>
                    <th style="vertical-align:middle;">Microsoft Translator API</th>
                    <td style="min-width:200px;">'.number_format(2000000).' / Month<br/>(Free Char Limit)</td>
                </tr>
                </table>';
          
            $settingArr[]=array(
                'name'  => $this->PREFIX.'api-key',
                'id'    => $this->PREFIX.'api-key',
                'class' => $this->PREFIX.'settings-field',
                'label'  => 'Enter Yandex Translate<br/>API v1 Key:',
                'desc'  =>$this->APIIntegrationTesting("yandex"),
                'type'  => 'text',
                'placeholder'=>__('Yandex API Key','cmb2'),
                'default' => ''
            );

            $settingArr[]=array(
                'name'  => $this->PREFIX.'ibm-translate-api-key',
                'id'    => $this->PREFIX.'ibm-translate-api-key',
                'class' => $this->PREFIX.'settings-field',
                'label'  => 'Enter IBM Translate<br/>API Key:',
                'type'  => 'text',
                'placeholder'=>__('IBM Translate API Key','cmb2'),
                'default' => ''
            );
            $settingArr[]=array(
                'name'  => $this->PREFIX.'ibm-translate-url',
                'id'    => $this->PREFIX.'ibm-translate-url',
                'class' => $this->PREFIX.'settings-field',
                'label'  => 'Enter IBM Translate URL:',
                'desc'  =>$this->APIIntegrationTesting("ibm"),
                'type'  => 'text',
                'placeholder'=>__('IBM Watson Translate URL','cmb2'),
                'default' => ''
            );
       if(Helpers::userType()=="pro" && Helpers::proInstalled()==false) {
        $key=Helpers::getLicenseKey();
        $url =esc_url( add_query_arg( 'license-key',$key , 'https://locoaddon.com/data/download-plugin.php' ) );

        $settingArr[]=array(
            'name'  => $this->PREFIX.'install-pro',
            'id'    => $this->PREFIX.'install-pro',
            'class' => $this->PREFIX.'settings-field',
            'label'  => 'Please Activate Or Download and Install PRO version',
            'desc'  =>'<a href="'.$url.'" target="_blank">Loco Automatic Translate Addon Pro</a>',
            'type'  => 'html',
         
            'default' => ''
        );
       }else if(Helpers::proInstalled()==true && Helpers::getLicenseKey()==false) {
        $settings_page_link=esc_url( get_admin_url(null, 'admin.php?page=loco-atlt-register') );
        $notice=__('Please add <strong>Loco Automatic Translate Addon Pro license key</strong> in the settings panel.', 'loco-translate-addon');
        $settingArr[]=
        array(
            'name'  => $this->PREFIX.'google-api-key-demo-2',
            'id'    => $this->PREFIX.'google-api-key-demo-2',
            'class' => $this->PREFIX.'settings-field',
            'label' => 'Enter Google Translate<br>API Key:',
            'desc'  =>'<div class=""><p>'.$notice.'</p>
            <p><a class="button button-secondary" href="'.$settings_page_link.'">'.__('Add License Key & Activate Premium Features').'</a>
        </p></div>',
            'type'  => 'html'
        );
        $settingArr[]=
        array(
            'name'  => $this->PREFIX.'microsoft-api-key-demo-2',
            'id'    => $this->PREFIX.'microsoft-api-key-demo-2',
            'class' => $this->PREFIX.'settings-field',
            'label' => 'Enter Microsoft Translator<br>API Key:',
            'desc'  =>'<div class=""><p>'.$notice.'</p>
            <p><a class="button button-secondary" href="'.$settings_page_link.'">'.__('Add License Key & Activate Premium Features').'</a>
        </p></div>',
            'type'  => 'html'
        );
        
       }else if(Helpers::userType()=="pro") {
        $key=Helpers::getLicenseKey();
        if(Helpers::validKey( $key)){
            $settingArr[]=array(
                'name'  => $this->PREFIX.'google-api-key',
                'id'    => $this->PREFIX.'google-api-key',
                'class' => $this->PREFIX.'settings-field',
                'label'  => 'Enter Google Translate<br>API Key:',
                'desc'  =>$this->APIIntegrationTesting("google"),
                'type'  => 'text',
                'placeholder'=>__('Google Translate API Key','cmb2'),
                'default' => ''
            );
            $settingArr[]=array(
                'name'  => $this->PREFIX.'microsoft-api-key',
                'id'    => $this->PREFIX.'microsoft-api-key',
                'class' => $this->PREFIX.'settings-field',
                'label'  => 'Enter Microsoft Translator<br>Subscription Key:',
                'desc'  => $this->APIIntegrationTesting("microsoft"),
                'type'  => 'text',
                'placeholder'=>__('Microsoft Translator Subscription Key','cmb2'),
                'default' => ''
            );
        }
        }else{
                $settingArr[]=
                array(
                    'name'  => $this->PREFIX.'google-api-key-demo',
                    'id'    => $this->PREFIX.'google-api-key-demo',
                    'class' => $this->PREFIX.'settings-field',
                    'label' => 'Enter Google Translate<br>API Key:',
                    'desc'  =>'<a href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing" target="_blank"><img  style="width:auto" src="'.ATLT_URL.'/assets/images/google-api.png" alt="Add Google Translate API Key"></a>',
                    'type'  => 'html'
                );
                $settingArr[]=
                array(
                    'name'  => $this->PREFIX.'microsoft-api-key-demo',
                    'id'    => $this->PREFIX.'microsoft-api-key-demo',
                    'class' => $this->PREFIX.'settings-field',
                    'label' => 'Enter Microsoft Translator<br>API Key:',
                    'desc'  =>'<a href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing" target="_blank"><img  style="width:auto" src="'.ATLT_URL.'/assets/images/microsoft-api.png" alt="Add Microsoft Translator API Key"></a>',
                    'type'  => 'html'
                );
                $settingArr[]=
                array(
                    'name'  => $this->PREFIX.'google-page-translate-button-demo',
                    'id'    => $this->PREFIX.'google-page-translate-button-demo',
                    'class' => $this->PREFIX.'settings-field',
                    'label' => 'Google Translate Button<br>No API Key Required:<br/><img src='.ATLT_URL.'/assets/images/powered-by-google.png>',
                    'desc'  =>'<a href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing" target="_blank"><img  style="width:auto" src="'.ATLT_URL.'/assets/images/google-translate-button.png" alt="Add Microsoft Translator API Key"><br/>✅ Available Inside Pro Version 1.1<br/><span style="font-size:11px;font-weight:bold;">(Release date:- 27 June, 2020)</span></a>',
                    'type'  => 'html'
                );
        }
            $settingArr[]= array(
                'name'  => $this->PREFIX.'index-per-request',
                'id'    => $this->PREFIX.'index-per-request',
                'class' => $this->PREFIX.'settings-field',
                'label'  => 'Index Per Request:',
                'desc'  => 'Number of strings index to send to Translate API in every request.<br/>Decrease it if you have long strings inside your loco translate table fields.'.$this->welcome_tab(),
                'type'  => 'number',
                'placeholder'=>__('50','cmb2'),
                'default' => '50'
            );
           
           // if(Helpers::userType()=="pro") {
               
           // }
          
           $settingArr[]=
           array(
               'name'  => $this->PREFIX.'supported-lang-list',
               'id'    => $this->PREFIX.'supported-lang-list',
               'class' => $this->PREFIX.'settings-field',
               'label' => 'Supported Languages List',
               'desc'  =>'<a target="_blank" href="https://locoaddon.com/supported-languages/">Click here to view All Supported Languages</a>',
               'type'  => 'html'
              );
            $settingArr[]=
            array(
                'name'  => $this->PREFIX.'traslation-limit',
                'id'    => $this->PREFIX.'traslation-limit',
                'class' => $this->PREFIX.'settings-field',
                'label' => 'Yandex Translate<br>Free Translation Limit:',
                'desc'  => $info_tbl,
                'type'  => 'html'
            );

            $settingArr[]= array(
                'name'  => $this->PREFIX.'ibm-traslation-limit',
                'id'    => $this->PREFIX.'ibm-traslation-limit',
                'class' => $this->PREFIX.'settings-field',
                'label' => 'IBM Translate<br>Free Translation Limit:',
                'desc'  =>$this->create_ibm_stats_tbl(),
                'type'  => 'html'
                 ); 
            if(Helpers::userType()=="free") {
                $settingArr[]=  array(
                    'name'  => $this->PREFIX.'upgrade-to-pro',
                    'id'    => $this->PREFIX.'upgrade-to-pro',
                    'class' => $this->PREFIX.'settings-field',
                    'label' => 'Extra Translate APIs Support<br/>Using Pro License:',
                    'desc'  => $pro_info,       
                    'type'  => 'html'
                );
            }else{
               
                if(Helpers::proInstalled()==true) {
                  
                $settingArr[]= array(
                'name'  => $this->PREFIX.'google-traslation-limit',
                'id'    => $this->PREFIX.'google-traslation-limit',
                'class' => $this->PREFIX.'settings-field',
                'label' => 'Google Translate<br>Free Translation Limit:',
                'desc'  =>$this->create_google_stats_tbl(),
                'type'  => 'html'
                 ); 
                 $settingArr[]= array(
                    'name'  => $this->PREFIX.'microsoft-traslation-limit',
                    'id'    => $this->PREFIX.'microsoft-traslation-limit',
                    'class' => $this->PREFIX.'settings-field',
                    'label' => 'Microsoft Translator<br>Free Translation Limit:',
                    'desc'  =>$this->create_microsoft_stats_tbl(),
                    'type'  => 'html'
                     ); 
                 }
            }
            $settingArr[]= array(
                'name'  => $this->PREFIX.'license-status',
                'id'    => $this->PREFIX.'license-status',
                'class' => $this->PREFIX.'settings-field',
                'label' => 'Current License Status:',
                'desc'  => $LS_html,
                'type'  => 'html'
            );
            
            $settingArr[]=  array(
                'name'  => $this->PREFIX.'screenshort',
                'id'    => $this->PREFIX.'screenshort',
                'label' => 'Usage Instructions:',
                'desc'  => $this->screenshort(),
                'type'  => 'html'
            );
            $settings_fields = array(
                $this->PREFIX.'register' =>  $settingArr
            );
            return $settings_fields;
        }

        public function welcome_tab(){
            //$this->ce_get_option($this->PREFIX.'-api-key');
            return get_submit_button('Save Settings');

        }

        public function rate_now(){
            $like_it_text='Rate Now! ★★★★★';
            $p_link=esc_url('https://wordpress.org/support/plugin/automatic-translator-addon-for-loco-translate/reviews/#new-post');
            $ajax_url=admin_url( 'admin-ajax.php' );
            $html ='<p>Thanks for using Loco Automatic Translate Addon - WordPress plugin. We hope it has saved your valuable time and efforts! <br/>Please give us a quick rating, it works as a boost for us to keep working on more <a href="https://coolplugins.net/">Cool Plugins!</a></p>
            <a href="'.$p_link.'" class="like_it_btn button button-primary" target="_new" title="'.$like_it_text.'">'.$like_it_text.'</a>
            ';            
            return $html;
        }

        public function screenshort(){
            
            $src = ATLT_URL .'assets/images/screenshot-1.gif';
            $html = '<img src="'.$src.'" style="width:auto;max-width:100%;">';

            return $html;
        }
        /*
        |---------------------------------------------------
        |   Add settings page to wordpress menu
        |---------------------------------------------------
        */
        public function admin_menu()
        {
                add_submenu_page( 'loco','Loco Auto Translator', 'Auto Translator Addon - Settings', 'manage_options', 'loco-atlt', array($this, 'atlt_settings_page'));
        }

        public function atlt_settings_page(){
            
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms('Save',false);

        }

        /*
        |---------------------------------------------------------
        |   Gather settings field-values like get_options()
        |---------------------------------------------------------
        */
        public function ce_get_option($option, $default = '')
        {

            $section = $this->PREFIX.'register';
            $options = get_option($section);

            if (isset($options[$option])) {
                return $options[$option];
            }

            return $default;
        }

        /*
        |-----------------------------------------------------------
        |   Show message in case of no api-key is saved
        |-----------------------------------------------------------
        */
        public function missing_api_key(){

            $api_key = $this->ce_get_option( $this->PREFIX.'api-key');

            $ibm_api_key = $this->ce_get_option( $this->PREFIX.'ibm-translate-api-key');
            if( isset( $api_key ) && !empty( $api_key ) ){
                return;
            }
            if( isset( $ibm_api_key ) && !empty( $ibm_api_key ) ){
                return;
            }
            if(Helpers::userType()=="pro"){
                return;
            }
            // Show API message only in translation editor page
            if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'file-edit' ){
                $plugin_info = get_plugin_data( ATLT_FILE , true, true );
                $message = sprintf('You must provide an %s to use the functionality of <strong>%s</strong>','<a href="'.admin_url('admin.php?page=loco-atlt').'">API key</a>',$plugin_info['Name']);
                $translation = __($message,'loco-translate-addon');
                $HTML = '<div class="notice notice-warning inline is-dismissible"><p>'.$translation.'</p></div>';
                echo $HTML;
            }else if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'loco-atlt' ){
                $message = sprintf('Get a free API KEY from %s and save it below to enable the Auto Translation feature.','<a href="https://cloud.ibm.com/registration" target="_blank">IBM Watson Language Translator</a>');
                $translation = __($message,'loco-translate-addon');
                $HTML = '<div class="notice notice-warning inline is-dismissible"><p>'.$translation.'</p></div>';
                echo $HTML;
            }
        }
        
    }
    
}