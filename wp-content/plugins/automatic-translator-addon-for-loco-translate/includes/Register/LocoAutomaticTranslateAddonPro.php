<?php
namespace LocoAutoTranslateAddon\Register;
use LocoAutoTranslateAddon\Helpers\Atlt_rollback;

require_once "LocoAutomaticTranslateAddonProBase.php";
	class LocoAutomaticTranslateAddonPro {
        public $plugin_file=__FILE__;
        public $responseObj;
        public $licenseMessage;
        public $showMessage=false;
        public $slug="loco-atlt-register";
        function __construct() {
    	    add_action( 'admin_print_styles', [ $this, 'SetAdminStyle' ] );
    	    $licenseKey=get_option("LocoAutomaticTranslateAddonPro_lic_Key","");
    	    $liceEmail=get_option( "LocoAutomaticTranslateAddonPro_lic_email","");
            LocoAutomaticTranslateAddonProBase::addOnDelete(function(){
               delete_option("LocoAutomaticTranslateAddonPro_lic_Key");
            });
    	    if(LocoAutomaticTranslateAddonProBase::CheckWPPlugin($licenseKey,$liceEmail,$this->licenseMessage,$this->responseObj,__FILE__)){
    		    add_action( 'admin_menu', [$this,'ActiveAdminMenu'],101);
    		    add_action( 'admin_post_LocoAutomaticTranslateAddonPro_el_deactivate_license', [ $this, 'action_deactivate_license' ] );
    		    //$this->licenselMessage=$this->mess;
                update_option("atlt-type","pro");
                add_action('wp_ajax_loco_install_pro', array($this, 'loco_install_pro'));           

    	    }else{
    	        if(!empty($licenseKey) && !empty($this->licenseMessage)){
    	           $this->showMessage=true;
                }
                update_option("atlt-type","free");
    		    update_option("LocoAutomaticTranslateAddonPro_lic_Key","") || add_option("LocoAutomaticTranslateAddonPro_lic_Key","");
    		    add_action( 'admin_post_LocoAutomaticTranslateAddonPro_el_activate_license', [ $this, 'action_activate_license' ] );
    		    add_action( 'admin_menu', [$this,'InactiveMenu'],101);
    	    }
        }
    	function SetAdminStyle() {
    		wp_register_style( "LocoAutomaticTranslateAddonProLic", plugins_url("style.css",$this->plugin_file),10);
    		wp_enqueue_style( "LocoAutomaticTranslateAddonProLic" );
        }
        
        function loco_install_pro(){
            $pass = (isset( $_POST['pass'] ) && !empty( $_POST['pass'] )) ? $_POST['pass'] : null;
            if( $pass === null || false == wp_verify_nonce( $pass, 'loco-pro-activation-nonce' )){
                die( array('response'=>'500','message'=>'Nonce Verification falied') );
            }
            $hash = isset($_POST['hash']) ? $_POST['hash'] : null;
            if( !empty( $hash ) ){
                require_once ATLT_PATH . 'includes/Helpers/Atlt_rollback.php';
                $request = new Atlt_rollback();
                $response = $request->install(  'https://locoaddon.com/data/download-plugin.php?license-key=' . $hash );
            }
            if( file_exists( WP_PLUGIN_DIR . '/loco-automatic-translate-addon-pro')==true ){
                activate_plugin( 'loco-automatic-translate-addon-pro/loco-automatic-translate-addon-pro.php' );
                die( json_encode( array('response'=>'200','message'=>'Loco Automatic Translate Addon Pro - Installed Successfully!')) );
            }else{
                die( json_encode( array('response'=>'500','message'=>'Loco Automatic Translate Addon Pro - Installation failed! Try downloading manually.')) );
            }
        }
        function ActiveAdminMenu(){
                add_submenu_page( 'loco',
                'Loco Automatic Translate Addon Pro', 
                'Auto Translator Addon - Premium License',
                 'manage_options', 
                    $this->slug,
                 array($this, 'Activated'));

                 if( class_exists( 'LocoAutoTranslateAddonPro' ) ){
                     // no further execution required
                     return;
                 }
            if( false == file_exists( WP_PLUGIN_DIR . '/loco-automatic-translate-addon-pro')  &&
                 isset( $_GET['page'] ) && $_GET['page'] == "loco-atlt-register" ){

                add_action('admin_footer', function(){
                    $hash_key = get_option( 'LocoAutomaticTranslateAddonPro_lic_Key' ,false);
                    $pass = wp_create_nonce('loco-pro-activation-nonce');
                    $url = admin_url( 'admin-ajax.php' );
                    $script = '
                    <script>
                    jQuery(document).ready(function($){
                        $(".error.loco-pro-missing").hide();
                        $.ajax({
                            type:"POST",
                            url:"'.$url.'",
                            data:{action:"loco_install_pro",hash:"'.$hash_key.'",pass:"'.$pass.'"},
                            complete:function(res){
                                console.log(res);
                                if( res.status == 200 ){
                                    console.log( res.message )
                                    $("#activating-loco-pro-response").text("Pro features activated successfully!");
                                    window.setTimeout(function(){
                                        $("#activating-loco-pro-response").fadeOut(400);   
                                    },1500);
                                }else{
                                    console.log( res.message )
                                }
                            }
                        })
                    });
                    </script>
                    <div id="activating-loco-pro-response">
                    <span> Please wait while we are activating pro features for you</span>
                    </div>
                    ';
                    echo $script;
                } );
            }
        }
        function InactiveMenu() {
            add_submenu_page( 'loco',
            'Loco Automatic Translate Addon Pro', 
            'Auto Translator Addon - Premium License',
             'activate_plugins', 
             $this->slug,
             array($this, 'LicenseForm'));
    	  /*  add_menu_page( "LocoAutomaticTranslateAddonPro", "Loco Automatic Translate Addon Pro", 'activate_plugins', $this->slug,  [$this,"LicenseForm"], " dashicons-star-filled " ); */

        }
        function action_activate_license(){
        		check_admin_referer( 'el-license' );
        		$licenseKey=!empty($_POST['el_license_key'])?$_POST['el_license_key']:"";
        		$licenseEmail=!empty($_POST['el_license_email'])?$_POST['el_license_email']:"";
        		update_option("LocoAutomaticTranslateAddonPro_lic_Key",$licenseKey) || add_option("LocoAutomaticTranslateAddonPro_lic_Key",$licenseKey);
        		update_option("LocoAutomaticTranslateAddonPro_lic_email",$licenseEmail) || add_option("LocoAutomaticTranslateAddonPro_lic_email",$licenseEmail);
        		wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug));
        	}
        function action_deactivate_license() {
    	    check_admin_referer( 'el-license' );
    	    if(LocoAutomaticTranslateAddonProBase::RemoveLicenseKey(__FILE__,$message)){
    		    update_option("LocoAutomaticTranslateAddonPro_lic_Key","") || add_option("LocoAutomaticTranslateAddonPro_lic_Key","");
    	    }
    	    wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug));
        }
        function Activated(){
            ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="LocoAutomaticTranslateAddonPro_el_deactivate_license"/>
                <div class="el-license-container">
                    <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("Loco Automatic Translate Addon - Premium License Status",$this->slug);?> </h3>
                    <hr>
                    <ul class="el-license-info">
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Status",$this->slug);?></span>

    			            <?php if ( $this->responseObj->is_valid ) : ?>
                                <span class="el-license-valid"><?php _e("Valid",$this->slug);?></span>
    			            <?php else : ?>
                                <span class="el-license-valid"><?php _e("Invalid",$this->slug);?></span>
    			            <?php endif; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Type",$this->slug);?></span>
    			            <?php echo $this->responseObj->license_title; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Expiry Date",$this->slug);?></span>
    			            <?php echo $this->responseObj->expire_date; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("Support Expiry Date",$this->slug);?></span>
    			            <?php echo $this->responseObj->support_end; ?>
                        </div>
                    </li>
                        <li>
                            <div>
                                <span class="el-license-info-title"><?php _e("Your License Key",$this->slug);?></span>
                                <span class="el-license-key"><?php echo esc_attr( substr($this->responseObj->license_key,0,9)."XXXXXXXX-XXXXXXXX".substr($this->responseObj->license_key,-9) ); ?></span>
                            </div>
                        </li>
                    </ul>
                    <div class="el-license-active-btn">
    				    <?php wp_nonce_field( 'el-license' ); ?>
    				    <?php submit_button('Deactivate License'); ?>
                    </div>
                </div>
            </form>
    	<?php
        }

        function LicenseForm() {
    	    ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
    	    <input type="hidden" name="action" value="LocoAutomaticTranslateAddonPro_el_activate_license"/>
    	    <div class="el-license-container">
    		    <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("Loco Automatic Translate Addon - Premium License",$this->slug);?></h3>
    		    <hr>
                <?php
                if(!empty($this->showMessage) && !empty($this->licenseMessage)){
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo _e($this->licenseMessage,$this->slug); ?></p>
                    </div>
                    <?php
                }
                ?>
                <style>
                table.loco-addon-license tr th, table.loco-addon-license tr td {
                    border: 1px solid #bbb;
                    padding: 12px;
                    text-align: center;
                }
                table.loco-addon-license tr td strong img {
                    height: 28px;
                    width: auto;
                    vertical-align: middle;
                }
                </style>
                <!--Enter License Key Here START-->
                <div class="el-license-field">
    			    <label for="el_license_key"><?php _e("Enter License code",$this->slug);?></label>
    			    <input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
    		    </div>
                <div class="el-license-field">
                    <label for="el_license_key"><?php _e("Email Address",$this->slug);?></label>
                    <?php
                        $purchaseEmail   = get_option( "LocoAutomaticTranslateAddonPro_lic_email", get_bloginfo( 'admin_email' ));
                    ?>
                    <input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo sanitize_email($purchaseEmail); ?>" placeholder="" required="required">
                    <div><small><?php _e("✅ I agree to share my purchase code and email for plugin verification and to receive future updates notifications!",$this->slug);?></small></div>
                </div>
    		    <div class="el-license-active-btn">
    			    <?php wp_nonce_field( 'el-license' ); ?>
    			    <?php submit_button('Activate'); ?>
    		    </div>
                <!--Enter License Key Here END-->
                <h3>Compare Free vs Pro (<a href='https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing' target='_blank'>Buy Premium License Key</a>)</h3>
                <table class="loco-addon-license">
                <tr>
                <th>Features</th>
                <th>Free License</th>
                <th>Premium License</th>
                </tr>
                <tr style="background:#fffb7a;font-weight: bold;">
                <td>**NEW**<br/>Google Translate Button Support<br/><span>(No API Key Required)<br/>+<br/>(Unlimited Translations)</span><br/><img style="border: 1px solid;" src="<?php echo ATLT_URL.'/assets/images/powered-by-google.png' ?>"/></td>
                <td>❌ Not Available</td>
                <td>✅ Available Inside Pro Version 1.1<br/><span style="font-size:11px;font-weight:bold;">(Release date:- 27 June, 2020)</span></td>
                </tr>
                <tr>
                <td><strong><img src="<?php echo ATLT_URL.'/assets/images/ibm-translate.png' ?>"/> IBM Watson Language Translator API</strong></td>
                <td>✅ Available</td>
                <td>✅ Available</td>
                </tr>
                <tr>
                <td>IBM API - free translation limit</td>
                <td>1,000,000 char / month<br/><span style="font-size:11px;font-weight:bold;">(Provided by API Provider)</span></td>
                <td>1,000,000 char / month<br/><span style="font-size:11px;font-weight:bold;">(Provided by API Provider)</span></td>
                </tr>
                <tr>
                <td><strong><img src="<?php echo ATLT_URL.'/assets/images/google-translate.png' ?>"/> Google Translate API Support</strong></td>
                <td>❌ Not Available</td>
                <td>✅ Available</td>
                </tr>
                <tr>
                <td>Google API - free translation limit</td>
                <td>❌ Not Available</td>
                <td>500,000 char / month<br/><span style="font-size:11px;font-weight:bold;">(Provided by API Provider)</span></td>
                </tr>
                <td><strong><img src="<?php echo ATLT_URL.'/assets/images/microsoft-translate.png' ?>"/> Microsoft Translator API Support</strong></td>
                <td>❌ Not Available</td>
                <td>✅ Available</td>
                </tr>
                <tr>
                <td>Microsoft API - free translation limit</td>
                <td>❌ Not Available</td>
                <td>2,000,000 char / month<br/><span style="font-size:11px;font-weight:bold;">(Provided by API Provider)</span></td>
                </tr>
                <tr>
                <td><strong>Reset Translations</strong></td>
                <td>❌ Not Available</td>
                <td>✅ Available</td>
                </tr>
                <td><strong>Support</strong></td>
               <td>WordPress Free Forum Support!<br/><strong>(Support Time: 7 – 10 days)</strong></td>
               <td>Quick Support Via Email<br/><strong>contact@coolplugins.net</strong></td>
                </tr>
                </table>
                <div>
                <strong style="color:#e00b0b;">*Important Points</strong>
                <ol>
                <li>Free characters translation limit only provided by Translate API providers, e.g. - Google, Microsoft, IBM etc.<br/>You need to grab translate API keys from their respective API platforms. Plugin does not provide any<br/>free characters limit for automatic translations.</li>
                <li>If any translation API provider stop providing free translations or translation API anytime in future.<br/>In that case plugin will not support that translation API provider.</li>
                <li>Currently automatic translate providers do not support HTML and special characters translations.<br/>So plugin will not translate any string that contains HTML or special characters.</li>
                </ol>
                </div>
                <br/>
                <a class="button button-primary" href='https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing' target='_blank'>Buy Premium License Key</a>
    	    </div>
        </form>
    	    <?php
        }
    }

    new LocoAutomaticTranslateAddonPro();