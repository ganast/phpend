<?php

namespace com\ganast\jm\http\json {

    abstract class JSONHTTPHelpers {

        /*
        * TODO
        * 
        * @return JsonHttpResponse
        * @param $success: Boolean
        * @param $data: Object or Array
        */
       public static function returnJSONHTTPResponse($httpCode, $data) {

			reset_request($httpCode, 'application/json; charset=utf-8');

	        echo json_encode($data);
       
			exit();
       }
    }
}