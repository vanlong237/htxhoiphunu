<?php
namespace LocoAutoTranslateAddon;
class ibmTranslator {

    public $queue = [];
    public $response;
    public $responses = [];

    public function translate($args = [], $opts = []) {
        $args['key'] = isset($args['key']) ? $args['key'] : null;
        $args['from'] = isset($args['from']) ? $args['from'] : null;
        $args['to'] = isset($args['to']) ? $args['to'] : null;
        $args['text'] = isset($args['text']) ? $args['text'] : null;
        if (!$args['base']) {
            return false;
        }
        if (!$args['key']) {
            return false;
        }
        if (!$args['from']) {
            return false;
        }
        if (!$args['to']) {
            return false;
        }
        if (!$args['text']) {
            return false;
		}
		$url = $args['base'];
     
        $headers = [
            'Content-type: application/json',
		];
        $data['text']= $args['text'];
        $data['source']= $args['from'];
        $data['target']= $args['to'];
        $params = json_encode($data);
      
		$options = $opts;
        $queue = isset($args['queue']) ? $args['queue'] : false;
        $response = $this->post($url, $headers, $params, $options, $queue,$args['key']);
        if (!$queue) {
            $this->response = $response;
        }
        if ($queue) {
            return;
        }
		$json = json_decode($response['body'], true);
        $response=[];
        if(isset($json['code'])&& isset($json['error'])){
               $response['code']=$json['code'];
               $response['error']=$json['error'];
        }else{
              $response['code']=200;
         
            if (empty($json['translations'][0]['translation'])) {
                 return false;
             }
            if(is_array($json['translations'])){
                foreach($json['translations'] as $key=>$value){
                 $translated[$key]=$value['translation'];
                }
                $response['translation']=$translated;
            }
         }
          return $response;
    }

    public function post($url, $headers = [], $params = [], $options = [], $queue = false,$api_key) {
        $opts = [];
        $opts[CURLINFO_HEADER_OUT] = true;
        $opts[CURLOPT_CONNECTTIMEOUT] = 5;
        $opts[CURLOPT_ENCODING] = '';
        $opts[CURLOPT_FOLLOWLOCATION] = false;
        $opts[CURLOPT_HEADER] = true;
        $opts[CURLOPT_HTTPHEADER] = $headers;
		$opts[CURLOPT_POST] = true;
		//$opts[CURLOPT_USERPWD] = \json_encode(array('api_key'=>$api_key));
        $opts[CURLOPT_POSTFIELDS] = is_array($params) || is_object($params) ? http_build_query($params) : $params;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_SSL_VERIFYHOST] = false;
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        $opts[CURLOPT_TIMEOUT] = 10;
        $opts[CURLOPT_URL] = $url;
        foreach ($opts as $key => $value) {
            if (!array_key_exists($key, $options)) {
                $options[$key] = $value;
            }
        }
        if ($queue) {
            $this->queue[] = ['options' => $options];
            return;
        }
        $follow = false;
        if ($options[CURLOPT_FOLLOWLOCATION]) {
            $follow = true;
            $options[CURLOPT_FOLLOWLOCATION] = false;
        }
        $errors = 2;
        $redirects = isset($options[CURLOPT_MAXREDIRS]) ? $options[CURLOPT_MAXREDIRS] : 5;
        while (true) {
            $ch = curl_init();
			curl_setopt_array($ch, $options);
			curl_setopt($ch, CURLOPT_USERPWD, 'apikey' . ':' .$api_key );
            $body = curl_exec($ch);
            $info = curl_getinfo($ch);
            $head = substr($body, 0, $info['header_size']);
            $body = substr($body, $info['header_size']);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            $response = [
                'info' => $info,
                'head' => $head,
                'body' => $body,
                'error' => $error,
                'errno' => $errno,
            ];
            if ($error || $errno) {
                if ($errors > 0) {
                    $errors--;
                    continue;
                }
            } elseif ($info['redirect_url'] && $follow) {
                if ($redirects > 0) {
                    $redirects--;
                    $options[CURLOPT_URL] = $info['redirect_url'];
                    continue;
                }
            }
            break;
        }
        return $response;
    }

}
