
/**
 * Script for PO file editor pages
 */
!function( window, $ ){
    var TotalCharacters=0;
    var HtmlStrings = 0;
    var requestChars=0;
  
  // ES6 Modules or TypeScript
    let event = document.createEvent('event');
    event.initEvent('atlt_run_translation');
    createSettingsPopup();
 $('#atlt-dialog .atlt-ok.button').on('click',function(){
        // hide dialog container by finding main parent DOM
        localStorage.removeItem('unSavedString');
        $("#atlt-dialog").parent('.ui-dialog').hide();
    });
    var loco = window.locoScope,
        conf = window.locoConf,
        syncParams = null,
        saveParams = null,
        // UI translation
        translator = loco.l10n,
        sprintf = loco.string.sprintf,
        // PO file data
        locale = conf.locale,
        messages = loco.po.init( locale ).wrap( conf.powrap ),
        template = ! locale,
        // form containing action buttons
        elForm = document.getElementById('loco-actions'),
        filePath = conf.popath,
        syncPath = conf.potpath,
        
        // file system connect when file is locked
        elFilesys = document.getElementById('loco-fs'),
        fsConnect = elFilesys && loco.fs.init( elFilesys ),
        
        // prevent all write operations if readonly mode
        readonly = conf.readonly,
        editable = ! readonly,
        // Editor components
        editor,
        saveButton,
        innerDiv = document.getElementById('loco-editor-inner');

     
    /** 
     * 
     */
    function doSyncAction( callback ){
        function onSuccess( result ){
             var info = [],
                 doc = messages,
                 exp = result.po,
                 src = result.pot,
                 pot = loco.po.init().load( exp ),
                 done = doc.merge( pot ),
                 nadd = done.add.length,
                 ndel = done.del.length,
                 t = translator;
             // reload even if unchanged, cos indexes could be off
             editor.load( doc );
             // Show summary 
             if( nadd || ndel ){
                 if( src ){
                    // Translators: Where %s is the name of the POT template file. Message appears after sync
                    info.push( sprintf( t._('Merged from %s'), src ) );
                 }   
                 else {        
                    // Translators: Message appears after sync operation     
                    info.push( t._('Merged from source code') );
                 }
                 // Translators: Summary of new strings after running in-editor Sync
                 nadd && info.push( sprintf( t._n('1 new string added','%s new strings added', nadd ), nadd ) );
                 // Translators: Summary of existing strings that no longer exist after running in-editor Sync
                 ndel && info.push( sprintf( t._n('1 obsolete string removed','%s obsolete strings removed', ndel ), ndel ) );
                 // editor thinks it's saved, but we want the UI to appear otherwise
                 $(innerDiv).trigger('poUnsaved',[]);
                 updateStatus();
                 // debug info in lieu of proper merge confirmation:
                 window.console && debugMerge( console, done );
             }
             else if( src ){
                 // Translators: Message appears after sync operation when nothing has changed. %s refers to a POT file.
                 info.push( sprintf( t._('Already up to date with %s'), src ) );
             }
             else {
                 // Translators: Message appears after sync operation when nothing has changed
                 info.push( t._('Already up to date with source code') );
             }
             loco.notices.success( info.join('. ') );
             $(innerDiv).trigger('poMerge',[result]);
             // done sync
             callback && callback();
        }
        loco.ajax.post( 'sync', syncParams, onSuccess, callback );
    }

    function debugMerge( console, result ){
         var i = -1, t = result.add.length;
         while( ++i < t ){
             console.log(' + '+result.add[i].source() );
         }
         i = -1, t = result.del.length;
         while( ++i < t ){
             console.log(' - '+result.del[i].source() );
         }
    }

    /**
     * Post full editor contents to "posave" endpoint
     */    
    function doSaveAction( callback ){
        function onSuccess( result ){
            callback && callback();
            editor.save( true );
            // Update saved time update
            $('#loco-po-modified').text( result.datetime||'[datetime error]' );
        }
        saveParams.locale = String( messages.locale() || '' );
        if( fsConnect ){
            fsConnect.applyCreds( saveParams );
        }
        // adding PO source last for easier debugging in network inspector
        saveParams.data = String( messages );
        loco.ajax.post( 'save', saveParams, onSuccess, callback );
    }
    function saveIfDirty(){
        editor.dirty && doSaveAction();
    }
    function onUnloadWarning(){
        // Translators: Warning appears when user tries to refresh or navigate away when editor work is unsaved
        return translator._("Your changes will be lost if you continue without saving");
    }
    
    function registerSaveButton( button ){
        saveButton = button;
        // enables and disable according to save/unsave events
        editor
            .on('poUnsaved', function(){
                enable();
                $(button).addClass( 'button-primary loco-flagged' );
            } )
            .on('poSave', function(){
                disable();
                $(button).removeClass( 'button-primary loco-flagged' );
            } )
        ;
        function disable(){ 
            button.disabled = true;
        }
        function enable(){
            button.disabled = false;
        }        
        function think(){
            disable();
            $(button).addClass('loco-loading');
        }
        function unthink(){
            enable();
            $(button).removeClass('loco-loading');
        }
        saveParams = $.extend( { path: filePath }, conf.project||{} );

        $(button).click( function(event){
            event.preventDefault();
            think();
            doSaveAction( unthink );
            setTimeout(function() {
                location.reload();
               },3500);
            return false;
        } );
        return true;
    };
    
  function registerSyncButton( button ){
        var project = conf.project;
        if( project ){
            function disable(){
                button.disabled = true;
            }
            function enable(){
                button.disabled = false;
            }
            function think(){
                disable();
                $(button).addClass('loco-loading');
            }
            function unthink(){
                enable();
                $(button).removeClass('loco-loading');
            }
            // Only permit sync when document is saved
            editor
                .on('poUnsaved', function(){
                    disable();
                } )
                .on('poSave', function(){
                    enable();
                } )
            ;
            // params for sync end point
            syncParams = {
                bundle: project.bundle,
                domain: project.domain,
                type: template ? 'pot' : 'po',
                sync: syncPath||''
            };
            // enable syncing on button click
            $(button)
                .click( function(event){
                    event.preventDefault();
                    think();
                    doSyncAction( unthink );
                    return false;
                } )
                //.attr('title', syncPath ? sprintf( translator._('Update from %s'), syncPath ) : translator._('Update from source code') )
            ;
            enable();
        }
        return true;
    }
    
function registerFuzzyButton( button ){
        var toggled = false, 
            enabled = false
        ;
        function redraw( message, state ){
            // fuzziness only makes sense when top-level string is translated
            var allowed = message && message.translated(0) || false;
            if( enabled !== allowed ){
                button.disabled = ! allowed;
                enabled = allowed;
            }
            // toggle on/off according to new fuzziness
            if( state !== toggled ){
                $(button)[ state ? 'addClass' : 'removeClass' ]('inverted');
                toggled = state;
            }
        }
        // state changes depending on whether an asset is selected and is fuzzy
        editor
            .on('poSelected', function( event, message ){
                redraw( message, message && message.fuzzy() || false );
            } )
            .on( 'poEmpty', function( event, blank, message, pluralIndex ){
                if( 0 === pluralIndex && blank === enabled ){
                    redraw( message, toggled );
                }
            } )
            .on( 'poFuzzy', function( event, message, newState ){
                redraw( message, newState );
            } )
        ;
        // click toggles current state
        $(button).click( function( event ){
            event.preventDefault();
            editor.fuzzy( ! editor.fuzzy() );
            return false;
        } );
        return true;
    };

 function registerRevertButton( button ){
        // No need for revert when document is saved
        editor
            .on('poUnsaved', function(){
                button.disabled = false;
            } )
            .on('poSave', function(){
                button.disabled = true;
            } )
        ;
        // handling unsaved state prompt with onbeforeunload, see below
        $(button).click( function( event ){
            event.preventDefault();
            location.reload();
            return false;
        } );
        return true;
    };

    function registerInvisiblesButton( button ){
        var $button = $(button);
        button.disabled = false;
        editor.on('poInvs', function( event, state ){
            $button[ state ? 'addClass' : 'removeClass' ]('inverted');
        });
        $button.click( function( event ){
            event.preventDefault();
            editor.setInvs( ! editor.getInvs() );
            return false;
        } );
        locoScope.tooltip.init($button);
        return true;
    }

    function registerCodeviewButton( button ){
         var $button = $(button);
         button.disabled = false;
         $button.click( function(event){
            event.preventDefault();
            var state = ! editor.getMono();
            editor.setMono( state );
            $button[ state ? 'addClass' : 'removeClass' ]('inverted');
            return false;
        } );
        locoScope.tooltip.init($button);
        return true;
    };

    function registerAddButton( button ){
        button.disabled = false;
        $(button).click( function( event ){
            event.preventDefault();
            // Need a placeholder guaranteed to be unique for new items
            var i = 1, baseid, msgid, regex = /(\d+)$/;
            msgid = baseid = 'New message';
            while( messages.get( msgid ) ){
                i = regex.exec(msgid) ? Math.max(i,RegExp.$1) : i;
                msgid = baseid+' '+( ++i );
            }
            editor.add( msgid );
            return false;
        } );
        return true;
    };

    function registerDelButton( button ){
        button.disabled = false;
        $(button).click( function(event){
            event.preventDefault();
            editor.del();
            return false;
        } );
        return true;
    };

    function registerDownloadButton( button, id ){
        button.disabled = false;
        $(button).click( function( event ){
            var form = button.form,
                path = filePath;
            // swap out path
            if( 'binary' === id ){
                path = path.replace(/\.po$/,'.mo');
            }
            form.path.value = path;
            form.source.value = messages.toString();
            // allow form to submit
            return true;
        } );
        return true;
    }

    
    // event handler that stops dead
    function noop( event ){
        event.preventDefault();
        return false;
    }

    /*/ dummy function for enabling buttons that do nothing (or do something inherently)
    function registerNoopButton( button ){
        return true;
    }*/
    
    /**
     * Update status message above editor.
     * This is dynamic version of PHP Loco_gettext_Metadata::getProgressSummary
     * TODO implement progress bar, not just text.
     */
    function updateStatus(){
        var t = translator,
            stats = editor.stats(),
            total = stats.t,
            fuzzy = stats.f,
            empty = stats.u,
            // Translators: Shows total string count at top of editor
            stext = sprintf( t._n('1 string','%s strings',total ), total.format(0) ),
            extra = [];
        if( locale ){
            // Translators: Shows percentage translated at top of editor
            stext = sprintf( t._('%s%% translated'), stats.p.replace('%','') ) +', '+ stext;
            // Translators: Shows number of fuzzy strings at top of editor
            fuzzy && extra.push( sprintf( t._('%s fuzzy'), fuzzy.format(0) ) );
            // Translators: Shows number of untranslated strings at top of editor
            empty && extra.push( sprintf( t._('%s untranslated'), empty.format(0) ) );
            if( extra.length ){
                stext += ' ('+extra.join(', ')+')';
            }
        }
        $('#loco-po-status').text( stext );
        if( typeof window.locoEditorStats == 'undefined'){
            window.locoEditorStats = {totalWords:stats.t, totalTranslated:stats.p} ;
        }else{
            window.locoEditorStats.totalWords = stats.t;
            window.locoEditorStats.totalTranslated = stats.p;
        }
        
    }
    
    /**
     * Enable text filtering
     */
    function initSearchFilter( elSearch ){
        editor.searchable( loco.fulltext.init() );
        // prep search text field
        elSearch.disabled = false;
        elSearch.value = '';
        function showValidFilter( numFound ){
            $(elSearch.parentNode)[ numFound || null == numFound ? 'removeClass' : 'addClass' ]('invalid');
        }
        var listener = loco.watchtext( elSearch, function( value ){
            var numFound = editor.filter( value, true  );
            showValidFilter( numFound );
        } );
        editor
            .on( 'poFilter', function( event, value, numFound ){
                listener.val( value||'' );
                showValidFilter( numFound );
            } )
            .on( 'poMerge', function( event, result ){
                var value = listener.val();
                value && editor.filter( value );
            } )
        ;
    }    
     
    // resize function fits editor to screen, accounting for headroom and touching bottom of screen.
    var resize = function(){
        function top( el, ancestor ){
            var y = el.offsetTop||0;
            while( ( el = el.offsetParent ) && el !== ancestor ){
                y += el.offsetTop||0;
            } 
            return y;    
        }
        var fixHeight,
            minHeight = parseInt($(innerDiv).css('min-height')||0)
        ;
        return function(){
            var padBottom = 20,
                topBanner = top( innerDiv, document.body ),
                winHeight = window.innerHeight,
                setHeight = Math.max( minHeight, winHeight - topBanner - padBottom )
            ;
            if( fixHeight !== setHeight ){
                innerDiv.style.height = String(setHeight)+'px';
                fixHeight = setHeight;
            }
        };
    }();    

    // ensure outer resize is handled before editor's internal resize
    resize();
    $(window).resize( resize );

    // initialize editor    
    innerDiv.innerHTML = '';
    editor = loco.po.ed
        .init( innerDiv )
        .localise( translator )
    ;
    loco.po.kbd
        .init( editor )
        .add( 'save', saveIfDirty )
        .enable('copy','clear','enter','next','prev','fuzzy','save','invis')
    ;

    // initialize toolbar button actions
    var buttons = {
        // help: registerNoopButton,
        save: editable && registerSaveButton,
        sync: editable && registerSyncButton,
        revert: registerRevertButton,
        // editor mode togglers
        invs: registerInvisiblesButton,
        code: registerCodeviewButton,
        // downloads / post-throughs
        source: registerDownloadButton,
        binary: template ? null : registerDownloadButton
    };
    // POT only
    if( template ){
        buttons.add = editable && registerAddButton;
        buttons.del = editable && registerDelButton;
    }
    // PO only
    else {
        buttons.fuzzy = registerFuzzyButton;
    };
    $('#loco-toolbar').find('button').each( function(i,el){
        var id = el.getAttribute('data-loco'), register = buttons[id];
        register && register(el,id) || $(el).hide();
    } );
    
    // disable submit on dummy form
    $(elForm).submit( noop );

    // enable text filtering
    initSearchFilter( document.getElementById('loco-search') );    

    // editor event behaviours
    editor
        .on('poUnsaved', function(){
            window.onbeforeunload = onUnloadWarning;
        } )
        .on('poSave', function(){
            updateStatus();
            window.onbeforeunload = null;
        } )
        .on( 'poUpdate', updateStatus );
   
    
    // load raw message data
    messages.load( conf.podata );
    
    // ready to render editor
    editor.load( messages );
    
    // locale should be cast to full object once set in editor
    if( locale = editor.targetLocale ){
        locale.isRTL() && $(innerDiv).addClass('trg-rtl');
    }
    // enable template mode when no target locale 
    else {
        editor.unlock();
    }


/*
|--------------------------------------------------------------------------
|   Auto Translator Custom Code
|--------------------------------------------------------------------------
*/
    //encode URL query string
    function createEncodedString(allStringText){
            const queryString=allStringText.map((item)=>{
                 return "&text="+ encodeURIComponent(item.source);
            }).join(",");
            
             return queryString;
        } 
    // validate key
    function validLicenseKey(licenseKey){
        if(licenseKey!=undefined && licenseKey.length>1){
          let validKey=  validate_pattern(licenseKey);
            if(validKey.length>1){
            return licenseKey;
            }
        }else{
            return false;
        }
      
    }

    // create Saved Time Message HTML
    function savedTimeInfo(statsObj){
        var info='';
        if(statsObj!=undefined && statsObj['time_saved']!==undefined){
                let timeSaved=statsObj['time_saved'];
                let totalChars=statsObj['totalChars'];
               var info =`<div class="saved_time_wrapper" style="margin:10px 0px">
               <span style="border: 3px solid #14b75d;display: inline-block;padding: 3px;">
               Wahooo! You have saved your 
               <strong>${timeSaved}</strong> 
               via auto translating  <strong>${totalChars}</strong>  
               characters  using <strong> <br />
               <a href="https://wordpress.org/support/plugin/automatic-translator-addon-for-loco-translate/reviews/#new-post" target="_new">
               Loco Automatic Translate Addon</a></strong>
               </span></div>`;
            }
    return info;
    }
    // validate pattern
    function validate_pattern(str){
        let m;
        const regex = /^([A-Z0-9]{8})-([A-Z0-9]{8})-([A-Z0-9]{8})-([A-Z0-9]{8})$/gm;
        let saveMatch=[];
        while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }
            // The result can be accessed through the `m`-variable.
            m.forEach((match, groupIndex) => {
            saveMatch.push(match);
            // console.log(`Found match, group ${groupIndex}: ${match}`);
            });
        }
        return saveMatch;
    }
    function getTargetLang(){
        return window.locoConf.locale.lang?window.locoConf.locale.lang:null;
    }
$(document).ready(function(){
    if( template ){
        return ; 
     }
    const locoRawData=conf.podata;
    if(locoRawData!=undefined && locoRawData.length>0 ){
        // called auto traslate button
        addAutoTranslationBtn();
    }

    $(document).on("click", "#cool-auto-translate-btn", function() {
         $('#atlt-dialog').dialog({width:440,height:500});
    });
    // main translate handler
    $("#typehtmlWrapper").hide();
    $("input[name=api_type]").on( "click",function(){
        if($(this).val()=="google" || $(this).val()=="microsoft" || $(this).val()=="ibm"){
            $("#typehtmlWrapper").hide();
            $("#typeplain").attr("checked","checked");
        }else{
            $("#typehtmlWrapper").show();
        }
    });
    
    // integrate reset string traslation button
    $("#atlt_reset_all").on("click",function(){
        swal("What type of strings do you want to reset?",
            {
            dangerMode: true,
            icon: "warning",
            confirmButtonColor: '#8CD4F5',
          
            buttons: {
              plain: {
                text: "Plain Text Strings",
                value: "plain",
                class:"danger"
              },
              html: {
                text: "HTML Strings",
                value: "html",
              },
              all: {
                text: "All Strings",
                value: "all",
              },
              cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                className: "",
                closeModal: false,
              },
            },
          })
          .then((value) => {
            switch (value) {
              case "all":
                resetTranslations(value);
                swal("Done!", "You have successfully reset all strings translations. Just close this popup & SAVE!","success");;
                break;
              case "plain":
                resetTranslations(value);
                swal("Done!", "You have successfully reset all plain text strings translations. Just close this popup & SAVE!", "success");
                break;
              case "html":
                resetTranslations(value);
                swal("Done!", "You have successfully reset all strings with HTML translations. Just close this popup & SAVE!", "success");
                break;
              default:
                swal("Cancelled, Just close this popup!");
            }
          });
          
    });

    // reset string array 
    function resetTransArr(tranArr,type){
      var  resetStrs=[];
     return  resetStrs=tranArr.map(function(item){
            if(item.source!==undefined && item.target!==undefined ){
               if(type=="html"){
                   if((isHTML(item.source))){
                   item.target="";
                }else if(isAllowedChars(item.source) && isPlacehodersChars(item.source)===false){
                    item.target="";
                }
                }
               if(type=="plain"){
                if( isPlacehodersChars(item.source)===true){
                    item.target="";
                }
                else if(isHTML(item.source) || isAllowedChars(item.source)){
                  }else{
                    item.target="";
                  }
                }
                if(type=="all"){
                item.target="";
                }     
            return item;
        }
        });
    }

// integrate reset translation button
    function resetTranslations(type){
        var resetArr=[];
        const saveBtn=$('[data-loco="save"]');
        if(conf.podata!==undefined){
            if(type=="plain"){
              resetArr=resetTransArr(conf.podata,"plain");
            }else if(type=="html"){
                resetArr=resetTransArr(conf.podata,"html");
            }else{
                resetArr=resetTransArr(conf.podata,"all");
            }
             messages = loco.po.init( locale ).wrap( conf.powrap );
             messages.load(resetArr);
             // editor event behaviours
            editor
            .on('poUnsaved', function(){
                window.onbeforeunload = onUnloadWarning;
            } )
            .on('poSave', function(){
                updateStatus();
                window.onbeforeunload = null;
            } )
            .on( 'poUpdate', updateStatus );
        
            // ready to render editor
            editor.load(messages);
            saveBtn.addClass( 'button-primary loco-flagged' ).removeAttr("disabled");
            updateStatus();
    }
}
    // handle form settings
    $("#atlt-settings-form").submit(function( event ) {  
        event.preventDefault(); 
        const user_type=ATLT["info"].type;
        let strType ="plain";
        let apiType = $("input[name='api_type']:checked").val();
        let mainBtn=$("#cool-auto-translate-btn");
        var thisBtn=$("#cool-auto-translate-start");
        let sourceApiKey ='';
        var todayLimit= mainBtn.data('today-limit');
        var totalLimit= mainBtn.data('total-limit');
        let  targetLang='';
       
        if(user_type=="free" && apiType=="google"){
            alert("Google Translation Only Available in the PRO version");
            return false;
        }
        if(user_type=="free" && apiType=="microsoft"){
            alert("Microsoft Translator Only Available in the PRO version");
            return false;
        }
        if((user_type==undefined || user_type=="pro") && ATLT["info"]["licenseKey"]==undefined){
            alert("Please enter Your License Key");
            return false;
        }

        if(conf['locale']["lang"]==!undefined){
            targetLang=conf['locale']["lang"];
        }else{
            targetLang=getTargetLang();
        }
        if(apiType=="google"){
            if(targetLang=="zh"){
            targetLang= targetLang+"-"+conf['locale']["region"];
            }
            sourceApiKey = ATLT["api_key"]["gApiKey"];
        }else if(apiType=="microsoft"){
            if(targetLang=="zh"){
                    if(conf['locale']["region"]=="CN"){
                    targetLang= targetLang+"-Hans";
                    }else if(conf['locale']["region"]=="TW"){
                    targetLang= targetLang+"-Hant";
                     }else{
                    targetLang= targetLang+"-"+conf['locale']["region"]; 
                     }
             }
            sourceApiKey = ATLT["api_key"]["mApiKey"];
        }else if(apiType=="ibm"){
            sourceApiKey = ATLT["api_key"]["ibmApiKey"];
        }else{
            sourceApiKey = ATLT["api_key"]["yApiKey"];
        }
        // filter transable strings
        if(locoRawData!=undefined && locoRawData.length>0 && sourceApiKey!='' ){
            let plainStrArr=[];
            let htmlStrArr=[];
            let orgStrArr=[];
            orgStrArr=locoRawData;
            var countChars=0;
            if(strType=="plain"){
                plainStrArr= filterRawObject(locoRawData,"plain");
                if (plainStrArr !== null) {
                    plainStrArr.map(function(index){
                        countChars +=index.source.length; 
                    });
                 }
            }else{
                htmlStrArr= filterRawObject(locoRawData,"html");
                if (htmlStrArr !== null) {
                    plainStrArr.map(function(index){
                        countChars +=index.length; 
                    });
                 }
            }
            
            if (htmlStrArr !== null || plainStrArr !== null ) {
            if(countChars>parseInt(todayLimit)){
               alert('Your translation string are larger then available free limit.In order to extend limit Buy Pro license key');
            }else{
              
             if(strType=="plain"){
                if(plainStrArr.length==0){
                    $("#atlt-dialog").parent('.ui-dialog').hide();
                    mainBtn.attr('disabled','disabled');
                    alert("You have no untranslated plain strings");
                    window.location.reload();
                    return;
                }
                dataObj = {  
                textToTranslateArr:plainStrArr,
                strType:"plain",
                };
            }
            // create data object for later use
            dataObj.orgStrArr=orgStrArr;
            dataObj.thisBtn=thisBtn;
            dataObj.apiType=apiType;
            dataObj.targetLang=targetLang;
            if(apiType=="google" || apiType=="microsoft"){
                dataObj.endpoint='pro_autotranslate_handler';
            }else{
                dataObj.endpoint='free_autotranslate_handler';
            }
         

            // save data object globaly for later use
           window.locoEditorStats.dataObj = dataObj;
           jQuery(document).trigger('atlt_run_translation');
           thisBtn.val('Translating...');
           mainBtn.text("Translating..");
           $("#atlt_preloader").show();
            // load raw message data
           
       }  //
            
        } // else close
        } 
     
    });
  
});
// create translation events
jQuery(document).on('atlt_run_translation',function(){
    let textToTranslate = window.locoEditorStats.dataObj.textToTranslateArr
    let totalTranslated = window.locoEditorStats.totalTranslated
  
    const nonce=ATLT["nonce"];
    const saveBtn=$('[data-loco="save"]');
    const orignalstringArr=window.locoEditorStats.dataObj.orgStrArr;
    const targetLang=  window.locoEditorStats.dataObj.targetLang;
    let indexRequest = 50;
    if( ATLT.api_key['atlt_index-per-request'] != "" && typeof ATLT.api_key['atlt_index-per-request'] != "undefined" ){
        indexRequest = ATLT.api_key['atlt_index-per-request'];
    }
  
    //save pending array in window object for later use
    if( typeof textToTranslate == "object" && textToTranslate.length >= 1 ){
        // update object for later us
        let translationO = {
            textToTranslateArr:textToTranslate.slice(indexRequest), //save pending index for later us
            thisBtn:window.locoEditorStats.dataObj.thisBtn,
            strType:window.locoEditorStats.dataObj.strType,
            orgStrArr:window.locoEditorStats.dataObj.orgStrArr,
            apiType:window.locoEditorStats.dataObj.apiType,
            targetLang:targetLang,
            endpoint:window.locoEditorStats.dataObj.endpoint
            };
        window.locoEditorStats.dataObj = translationO;
      
      // send partial data request
        let data =  {
            sourceLang:'en',
            targetLang:targetLang,
            textToTranslateArr:textToTranslate.slice(0,indexRequest),
            orginalArr:orignalstringArr,
            thisBtn:window.locoEditorStats.dataObj.thisBtn,
            strType:window.locoEditorStats.dataObj.strType,
            apiType:window.locoEditorStats.dataObj.apiType,
            saveBtn:saveBtn,
            endpoint:window.locoEditorStats.dataObj.endpoint,
            nonce:nonce
            };
            // slice data 
            textToTranslate.slice(0,indexRequest).map(function(value,index){
                TotalCharacters += (value.source).length;
                requestChars+=(value.source).length;
            })
            atlt_translate(data);
      }
})

// Translate
function atlt_translate(data) {
    
    atlt_ajax_translation_request(data, "POST").success(function(
      resp,status,xhr) {
 if(xhr.status==200 && resp!=null){

        const json_resp = JSON.parse(resp);
        let responseObj;
        let apiProvider=window.locoEditorStats.dataObj.apiType;
 
        if(json_resp['error'] && json_resp['error']['code']==800)
        {
            let errorMsz= json_resp['error']['message'];
            $('#atlt-dialog .atlt-final-message').html("<p style='color:red;margin:5px 2px;font-weight:bold;'>"+errorMsz+"</p>");
            $('#atlt-dialog .atlt-ok.button').show();
            $("#atlt_preloader").hide();
            $("#cool-auto-translate-btn").text('Error').attr('disabled','disabled');
            setTimeout(function() {
                location.reload();
                },4000);   
            return false;    
        }

        if(json_resp.translatedString!=null && json_resp.translatedString.length
             && json_resp['code']==200){
         responseObj=json_resp.translatedString;
        }else{
            let errorCode=json_resp['code'];
            let errorMsz=json_resp['error'];
            $('#atlt-dialog .atlt-final-message').html("<p style='color:red;margin:5px 2px;font-weight:bold;'>"+errorMsz+"</p>");
            $('#atlt-dialog .atlt-ok.button').show();
            $("#atlt_preloader").hide();
            $("#cool-auto-translate-btn").text('Error').attr('disabled','disabled');
            setTimeout(function() {
                location.reload();
                },4000); 
                return false;  

        }    

        let totalTranslated = window.locoEditorStats.totalTranslated;
       
        let unSavedStr=[];
        if(responseObj!==undefined && responseObj.length){
            for(i=0;i< responseObj.length;i++){
                    var text = responseObj[i];
                    if( data.textToTranslateArr[i] === undefined ){
                        break;
                    }
                 data.textToTranslateArr[i].target = text ;
            }
        }

        let translatedStrArr=data.textToTranslateArr;

        let Emptytargets = [];
        for(var x=0; x<translatedStrArr.length;++x){
            if(translatedStrArr[x].target !='' ){
                Emptytargets[x]=translatedStrArr[x].source;
            }
        }   
        let items;
        if (localStorage.getItem('unSavedString')) {
        items = JSON.parse(localStorage.getItem('unSavedString'))
        } else {
        items = []
        }

        var unSavedStrArr = items.concat(Emptytargets);
        localStorage.setItem('unSavedString', JSON.stringify(unSavedStrArr));
        messages = loco.po.init( locale ).wrap( conf.powrap );
            // ready to render editor
            messages.load(conf.podata);
            // editor event behaviours
            editor
            .on('poUnsaved', function(){
                window.onbeforeunload = onUnloadWarning;
            } )
            .on('poSave', function(){
                updateStatus();
                window.onbeforeunload = null;
            } )
            .on( 'poUpdate', updateStatus );
        
            // ready to render editor
            editor.load(messages);
            data.saveBtn.addClass( 'button-primary loco-flagged' ).removeAttr("disabled");
            updateStatus();
              // run through DOM and mark *(STAR) for newly translated
             markUnsavedString();
            
            requestChars=0;
            // update progress bar
            $('#atlt-dialog .translated-label').text('Translated');
              $('#atlt-dialog .translated-text').text(window.locoEditorStats.totalTranslated);
            
            $('#atlt-dialog .atlt-progress-bar-value').width(window.locoEditorStats.totalTranslated);
        
            let saved_time_html= savedTimeInfo(json_resp['stats']);
            let finalHTML="<strong style='font-size:18px;display:inline-block;margin:5px auto;'>Translation Complete!</strong><br/>(Close this popup & Click <strong>Save</strong>).";

            switch( window.locoEditorStats.totalTranslated ){
                case "0%":
                    $('#atlt-dialog .translated-label').text('Translating...');
                    $('#atlt-dialog .translated-text').text('');
                break;
                case "100%":
                    data.thisBtn.hide();
                    $("#atlt_preloader").hide();
                    data.thisBtn.attr('disabled','disabled');
                    $("#cool-auto-translate-btn").text('Translated - SAVE NOW').attr('disabled','disabled');
                    // change cursor to 'default' state
                     $('#atlt-dialog .atlt-final-message').html(finalHTML+saved_time_html);
                    $('#atlt-dialog .atlt-ok.button').show();
                    return;
                break;
            }
        
             // run through DOM and mark *(STAR) for newly translated
             for(var x=0;x<=Emptytargets.length;x++){
                var source = Emptytargets[x];
                jQuery("#po-list-tbody div[for='po-list-col-source'] div").filter(function(index){
                    return jQuery(this).text() == source 
                }).addClass('po-unsaved');
            }

        

            if( (window.locoEditorStats.dataObj.textToTranslateArr).length == 0){
                data.thisBtn.val('Translated').attr("disabled","true");
                $("#atlt_preloader").hide();
               // data.thisBtn.attr('disabled','disabled').hide('slow');
               $("#cool-auto-translate-btn").text('Translated - SAVE NOW').attr('disabled','disabled');
                // change cursor to 'default' state
                $('#atlt-dialog .atlt-final-message').html(finalHTML+saved_time_html);
                $('#atlt-dialog .atlt-ok.button').show();
                return;
            }

            jQuery(document).trigger('atlt_run_translation');
        
       }   else{
            data.thisBtn.hide('slow');
            $("#atlt_preloader").hide();
            $("#cool-auto-translate-btn").text('Translation').attr('disabled','disabled');
            $('#atlt-dialog .atlt-ok.button').show();
            alert('Unable to make request to the server at the moment. Try again later.');
        }
    }).fail(function(jqXHR){
        console.log(jqXHR);
        if(jqXHR.status==500 || jqXHR.status==0){
            // internal server error or internet connection broke  
            data.thisBtn.hide('slow');
            $("#atlt_preloader").hide();
            $("#cool-auto-translate-btn").text('Translation').attr('disabled','disabled');
            $('#atlt-dialog .atlt-ok.button').show();
            alert('Unable to make request to the server at the moment. Try again later.');
        }
    });
}
  // filter all saved strings
function filterSavedStrings(rawArray){
    return filterdArr=rawArray.filter((item,index)=>{
        if(item.target!="" &&(item.source!==undefined && item.source!="")){
           return true;
        }
    });     
}  

// filter string based upon type
function filterRawObject(rawArray,filterType){
    filterdArr=[];
   return filterdArr=rawArray.filter((item,index)=>{
        if((item.source!=="" && item.source!==undefined) && (item.target===undefined || item.target=="")){
            if( ValidURL(item.source)){
                return false;
            }
        if(filterType=="html"){
                if(isHTML(item.source)){
                    return true;
                }else if(isAllowedChars(item.source) && isPlacehodersChars(item.source)==false){
                    return true;
                }else{
                return false;
                } 
        }else{
            if(isHTML(item.source)){
                return false;
             } 
             else if(isPlacehodersChars(item.source)){
                return true;
             }
             else if(isSpecialChars(item.source)){
                   return false;
             }else if( item.source.includes('#') ) {
                return false;
             }else{
                return true;
             }      
        }    
        }        
      });
}


  // auto traslator button in editor
  function addAutoTranslationBtn(){
    if($("#loco-toolbar").find("#cool-auto-translate-btn").length>0){
        $("#loco-toolbar").find("#cool-auto-translate-btn").remove();
    }
    const locoActions= $("#loco-toolbar").find("#loco-actions");
    const otherBtn='<button class="button has-icon icon-warn" id="atlt_reset_all">Reset Translations</button></fieldset>';
    const allTranslated='<fieldset><button id="cool-auto-translate-btn" class="button has-icon icon-translate" disabled>Translated</button></fieldset>';
    let savedStrings=filterSavedStrings(conf.podata);

   let plainStrings= filterRawObject(conf.podata,"plain");
   let htmlStrings = filterRawObject(conf.podata,"html");
   const userType=ATLT["info"].type;
    if((Array.isArray(plainStrings) && plainStrings.length) ||
       (Array.isArray(htmlStrings)&& htmlStrings.length)
    ){
    const inActiveBtn='<fieldset><button title="Add API key to enable this feature." id="cool-auto-translate-btn" disabled class="button has-icon icon-translate">Auto Translate</button> <a style="font-size:9px;display:block;margin-left:8px;" target="_blank" href="https://www.ibm.com/in-en/cloud/watson-language-translator/pricing">Get Free API Key</a></fieldset>';
    const disabledBtn='<fieldset><button title="Buy PRO." id="cool-auto-translate-btn" disabled class="button has-icon icon-translate">Auto Translate</button><div style="max-width:320px; display:inline-block;margin-top: 4px;"><span style="font-size:12px;display:inline-block;margin-left:8px;">You have exceeded free translation limit. In order to extend the limit - <a target="_blank" style="font-size:14px;display:inline-block;margin-left:8px;" target="_blank" href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing">Buy Premium License</a></span></div></fieldset>';
    const apiKey=ATLT["api_key"]["yApiKey"];
    const proActiveBtn='<fieldset><button id="cool-auto-translate-btn" class="button has-icon icon-translate">Auto Translate</button></fieldset>';
    const allowed=ATLT["info"].allowed;
    const today=ATLT["info"].today;
    const total=ATLT["info"].total;
    const aTodayChars=300000;
    const aTodayChar=1000000;
  
    // not added API key
    if( ATLT == undefined || ATLT["api_key"] =='' ){
        if( userType=='free'){
            locoActions.append(inActiveBtn);
        return; 
        }else{
            locoActions.append(proActiveBtn);
        }
    }else if(
        ATLT["api_key"]["yApiKey"]=="" && ATLT["api_key"]["ibmApiKey"]==""){
            if( userType=='free'){
                locoActions.append(inActiveBtn);
            return; 
            }else{
                locoActions.append(proActiveBtn);
            }
    }
    else if( allowed=="no" && userType=='free'){
        // free not allowed
        locoActions.append(disabledBtn);
        return; 
    }else if(today!==undefined && parseInt(today)>aTodayChars 
    && userType=='free'){
      // today free limit exceeded
        locoActions.append(disabledBtn);
        return; 
    }else if(total!==undefined && parseInt(total)>aTodayChar
     && userType=='free'){
        // monthly limit exceeded
        locoActions.append(disabledBtn);
        return; 
    }else if( window.locoEditorStats.totalTranslated != "100%" 
    && window.locoEditorStats.totalWords > 0 ){
        //Pro user and added key then show button
        if(userType=='pro' && ATLT["info"]["licenseKey"]!=undefined && validLicenseKey(ATLT["info"]["licenseKey"])){
            locoActions.append(proActiveBtn);
        }else{
            //if user is free and allowed the show button
            if(today==undefined){
                var todayChars=aTodayChars;
            }else{
            var todayChars=aTodayChars-parseInt(today);
            }
            var totalChars=aTodayChar-parseInt(total);
            // append button for free
            var freeBtn='<fieldset><button data-today-limit="'+todayChars+'" data-total-limit="'+totalChars+'"  id="cool-auto-translate-btn" class="button has-icon icon-translate">Auto Translate</button></fieldset>';
            locoActions.append(freeBtn);
        }
    } else if( window.locoEditorStats.totalWords == 0){
        return;
    } 
} else{
    locoActions.append(allTranslated);
}

if((Array.isArray(savedStrings) && savedStrings.length)){
    if(userType=='pro' && ATLT["info"]["licenseKey"]!=undefined && validLicenseKey(ATLT["info"]["licenseKey"])){
        if(ATLT["info"]["proInstalled"]=="yes"){
            locoActions.append(otherBtn);
        }
       
    } 
}

}
// mark unsaved  after ajax translation process 
function markUnsavedString(){
    const unSavedString = JSON.parse(localStorage.getItem('unSavedString'));
   
    for(var x=0;x<=unSavedString.length;x++){
     var source = unSavedString[x];
     jQuery("#po-list-tbody div[for='po-list-col-source'] div").filter(function(index){
          return jQuery(this).text() == source 
       }).addClass('po-unsaved');
     }
   }

// detect String contain URL
function ValidURL(str) {
      var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    if(!pattern.test(str)) {
      return false;
    } else {
      return true;
    }
  }
  // detect Valid HTML in string
function isHTML(str){
    var rgex=/<(?=.*? .*?\/ ?>|br|hr|input|!--|wbr)[a-z]+.*?>|<([a-z]+).*?<\/\1>/i;
    if(str!==undefined){
        return  rgex.test(str); 
    }else {
        return false;
    }
}
//  check special chars in string
function isSpecialChars(str){
    var rgex=/[@#^$%&*{}|<>]/g;
    if(str!==undefined){
        return  rgex.test(str); 
    }else {
        return false;
    }
}
// allowed special chars in HTML string
function isAllowedChars(str){
    var rgex=/[!@#$%^&*(),?":|<>]/g;
    if(str!==undefined){
        return  rgex.test(str); 
    }else {
        return false;
    }
}

// allowed special chars in plain text
function isPlacehodersChars(str){
    var rgex=/%s|%d/g;
    if(str!==undefined){
        return  rgex.test(str); 
    }else {
        return false;
    }
}
// check string contain curly brackets
function isContainChars(str){
    var rgex=/[{}[]/g;
    if(str!==undefined){
        return  rgex.test(str); 
    }else {
        return false;
    }
}
// format numbers
function atltFormatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
  }

// create popup model for translation settings
function createSettingsPopup(){
    let preloaderImg=extradata['preloader_path'];
    let gtPreviewImg=extradata['gt_preview'];
    const userInfo=ATLT["info"].type;
    const yAC=ATLT["info"].yAvailableChars;
    const iAC=ATLT["info"].iAvailableChars;
    const licenseKey=ATLT["info"]["licenseKey"];
    const proInstalled=ATLT["info"]["proInstalled"];
    let yfieldStatus="";
    let ifieldStatus="";
    let gfieldStatus="";
    let mfieldStatus="";
    let hfieldStatus="";
    let htmlSupported="";
    let contCls="";
    let icontCls="";
    let proLbl="";
    let gContCls='';
    let gHtml='';
    let mContCls='';
    let mHtml='';
    let submitBtn='';
    let yHtml='';
    let iHtml='';
    let yChecked='"';
    let iChecked='checked="true"';
    if(userInfo=="free"){
         let gAC=0;
         gfieldStatus="disabled";
         mfieldStatus="disabled";
         hfieldStatus="disabled";
         contCls="html-disabled";
         gContCls='g-disabled';
         mContCls='m-disabled';
         proLbl='<span class="atlt-pro-feature"><a href="https://locoaddon.com/addon/loco-automatic-translate-premium-license-key/#pricing" target="_blank" style="color:red;font-weight:bold;font-size:0.9em;" title="Only For Pro Users">PRO Only</a></span>';
        
         gHtml=proLbl+' ';
         mHtml=proLbl+' ';
    }else if(proInstalled=="no"){
        gfieldStatus="disabled";
        mfieldStatus="disabled";
        hfieldStatus="disabled";
        contCls="html-disabled";
        gContCls='g-disabled';
        mContCls='m-disabled';
        proLbl='<span style="color:red;font-weight:bold;font-size:0.9em;" class="atlt-pro-feature">Please Install PRO version</span>';
       
        gHtml=proLbl+' ';
        mHtml=proLbl+' ';
    }else{
        
    if(ATLT["info"]["licenseKey"]!=undefined && validLicenseKey(ATLT["info"]["licenseKey"]))
         {
           if(ATLT["api_key"]["gApiKey"]!="" ){
                let gAC=ATLT["info"].gAvailableChars;
                if(gAC!==undefined && gAC>10000){
                    gHtml='<span class="available-chars" style="font-weight:bold;font-size:0.9em;"> ('+ atltFormatNumber(gAC)+' Free Char. Available This Month)</span>';
                }else if(gAC<10000){
                    gHtml='<span class="used-chars" style="font-weight:bold;font-size:0.9em;">(You have consumed all free characters.)</span>';
                }
            }else{
                gfieldStatus="disabled";
                gContCls='g-disabled';
                gHtml='<span class="error" style="color:red;font-size:0.85em;">(Please enter Google Translate API key)</span>';
            }
        if(ATLT["api_key"]["mApiKey"]!=undefined &&
            ATLT["api_key"]["mApiKey"]!="" ){
                mAC=ATLT["info"].mAvailableChars;
                if(mAC!==undefined && mAC>10000){
                    mHtml='<span class="available-chars" style="font-weight:bold;font-size:0.9em;"> ('+ atltFormatNumber(mAC)+' Free Char. Available This Month)</span>';
                }else if(mAC<10000){
                    mHtml='<span class="used-chars" style="font-weight:bold;font-size:0.9em;">(You have consumed all free characters.)</span>';
                }
            }else{
                mfieldStatus="disabled";
                mContCls='m-disabled';
                mHtml='<span class="error" style="color:red;font-size:0.85em;">(Please enter Microsoft Translator API key)</span>';
            }
      }    
    }
    if(ATLT["api_key"]["yApiKey"]!="")
    {
        if(yAC!==undefined){
            yHtml='<span class="available-chars" style="font-weight:bold;font-size:0.9em;"> ('+atltFormatNumber(yAC)+' Free Char. Available Monthly)</span>';
           }
    }else{
        yfieldStatus="disabled";
        yContCls='g-disabled';
        yChecked='';
        yHtml='<span class="error" style="color:red;font-size:0.85em;">(Please enter Yandex Translate API v1 key)</span>';
    }
if(ATLT["api_key"]["ibmApiKey"]=="" ||  ATLT["info"]["ibm_url"]==""){
    ifieldStatus="disabled";
    iContCls='g-disabled';
    iChecked='';
    iHtml='<span class="error" style="color:red;font-size:0.85em;">(Please enter IBM Translate API key/URL)</span>';
}else{
    if(iAC!==undefined){
        iHtml='<span class="available-chars" style="font-weight:bold;font-size:0.9em;"> ('+atltFormatNumber(iAC)+' Free Char. Available Monthly)</span>';
       }
}
    if(userInfo=="free"){
        plainStringLbl='Translate Plain Text Strings(No HTML/Special character supported)';
    }else{
    plainStringLbl='Translate Plain Text Strings';
    }

    if( ATLT["api_key"]["yApiKey"]!="" || ATLT["api_key"]["gApiKey"]!="" || ATLT["api_key"]["mApiKey"]!="" || ATLT["api_key"]["ibmApiKey"]!="")
    {
     submitBtn='<input type="submit" class="button has-icon icon-translate" value="Start Translation"  id="cool-auto-translate-start">';
    }else{
        submitBtn='<button  class="atlt-ok button button-primary">OK</button>';

    }
    let settingsHTML=`<div class="atlt-settings">
    <strong class="atlt-heading" style="margin-bottom:10px;display:inline-block;">Translate Using Google Page Translate Button<br/>(No API Key Required + Unlimited Translations!)</strong>
    <div class="inputGroup">
    <button id="atlt_gtranslate_btn" disabled="true" class="notranslate button button-primary">Google Translate</button>
    <br/><img src="${gtPreviewImg}" alt="google translate widget preview"><br/>
    ${proLbl}
    ✅ Available Inside Pro Version 1.1<br/><span style="font-size:11px;font-weight:bold;">(Release date:- 27 June, 2020)</span>
    </div>
    <hr/>
    <div class="atlt-settings">
    <form id="atlt-settings-form" method="post" action="#">
    <strong class="atlt-heading" style="margin:15px 0 10px;display:inline-block;">Translate Using A Translate API</strong>
    <div class="inputGroup">
    <input class="inputEle" type="radio" id="ibm_api" 
    ${iChecked}    ${ifieldStatus} name="api_type" value="ibm">
    <label for="ibm_api">IBM Translate ${iHtml}</label>
    </div>
    <div class="inputGroup">
    <input class="inputEle" type="radio" id="yandex_api" 
    ${yChecked}    ${yfieldStatus} name="api_type" value="yandex">
    <label for="yandex_api">Yandex Translate ${yHtml}</label>
    </div>
  
    <div class="inputGroup ${gContCls}">
    <input class="inputEle" type="radio" id="google_api" 
     name="api_type" value="google"  ${gfieldStatus}>
    <label for="google_api">Google Translate ${gHtml}</label>
    </div>
    <div class="inputGroup ${mContCls}">
    <input class="inputEle" type="radio" id="microsoft_api" 
     name="api_type" value="microsoft"  ${mfieldStatus}>
    <label for="microsoft_api">Microsoft Translator ${mHtml}</label>
    <br/>
    <small style="display:inline-block;margin-left:24px;margin-top:8px;font-weight:bold;">(<a href="https://locoaddon.com/supported-languages/" target="_blank">View all supported languages list</a>)</small>
    </div>
    <br/>
    <fieldset>
    ${submitBtn}
    <img style="display:none;margin-left:10px;margin-top:-3px;" id="atlt_preloader" src="${preloaderImg}">
    </fieldset>
    </form>
    </div>`;
    // custom popup message box
    let popup_html = `<div id="atlt-dialog-container">
    <div style="display:none;" id="atlt-dialog" title="Automatic Translation Progress">
    ${settingsHTML}
    <p><span class="translated-label">Translated</span>
     <span class="translated-text">0%</span></p>
    <div class="atlt-progress-bar-track">
    <div class="atlt-progress-bar-value">
    </div></div>
    <div class="atlt-final-message"></div>
    <button style="display:none;" class="atlt-ok button button-primary">OK</button>
    </div></div>`;
    $("body").append( popup_html );
}

// send ajax request 
function atlt_ajax_translation_request(data,type){
    let filteredArr=[];
    filteredArr=data.textToTranslateArr.map((item)=>{
        if( typeof item.source!= 'undefined'){
            return   item.source;
        } });
    const  jsonData=JSON.stringify(filteredArr);

    return jQuery.ajax({
        url: ajaxurl,
        type:'POST',
        data: {'action':data.endpoint,
                'sourceLan':data.sourceLang,
                'targetLan':data.targetLang,
                'totalCharacters': TotalCharacters,
                'requestChars':requestChars,
                'nonce':data.nonce,
                'strType':data.strType,
                'apiType':data.apiType,
               'data':jsonData
            },
            done:function(res){
            //    console.log(res)
            }
          
       
    });
}

// ok, editor ready
    updateStatus();
 
    // clean up
  //delete window.locoConf;
  //conf = buttons = null;

}( window, jQuery );