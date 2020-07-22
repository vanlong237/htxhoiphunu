jQuery(document).ready(function ($) {
	$('.atlt_test_api').on('click', function (event) {
		event.preventDefault();
		var $this = $(this);
		$this.parent().find(".atlt-preloader").show();
		var ajaxURL=$this.data('ajaxurl');
		var source=$this.data('source');
		var target=$this.data('target');
		var text=$this.data('text');
		var apikey=$this.data('apikey');
		var apiprovider=$this.data('api-provider');
		if(apiprovider=="google" || apiprovider=="microsoft"){
			var endpoint='pro_test_api_provider';
		}else{
			var endpoint='free_test_api_provider';
			
		}
		var nonce=$this.data('nonce');
		var defaultString='"Hello" Translation in French:-';
		var data = {
			'action':endpoint,
			'nonce':nonce,
			'source':source,
			'target':target,
			'text':text,
			'apikey':apikey,
			'apiprovider':apiprovider,
		};
		var request = $.ajax({
			url:ajaxURL,
			method: "POST",
			data:data,
		  });
		  request.done(function (response, textStatus, jqXHR ){
		//	console.log(response);
		
			$this.parent().find(".atlt-preloader").hide();
			let output='';
		if(response && jqXHR.status==200){
				let json_resp = JSON.parse(response);
				if(json_resp.translatedString!=null && json_resp.translatedString.length
					 && json_resp['code']==200){
					traString=json_resp.translatedString;
					output=defaultString+'"'+traString+'"';
				swal({
						icon: "success",
						text:output
					  });
				}else{
				let errorCode=json_resp['code'];
				let message=json_resp['error'];
					swal({
						icon: "error",
						text:errorCode+  " " +message
					  });
				}
		}else{
			swal({
				icon: "error",
				text:response
			});
		}
		
	});
request.fail(function( jqXHR, textStatus ) {
			swal({
				icon: "warning",
				text:"Request failed: " + textStatus
			  });
			$this.parent().find(".atlt-preloader").hide();
		  });
	});
});