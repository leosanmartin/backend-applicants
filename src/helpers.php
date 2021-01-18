<?php

if (!function_exists('env')) {
    function env(string $varname, $default = null)
    {
        return $_ENV[$varname] ?? $default;
    }
}


function getDataApi($url=null)
{    
		$data = [];

	    if(isset($url) && !empty($url)){
	        $version = curl_version();
	        $options = array(
	                        CURLOPT_URL => $url,
	                        CURLOPT_RETURNTRANSFER => 1,
	                        CURLOPT_FOLLOWLOCATION => 1,
	                        CURLOPT_USERAGENT => 'curl/'.$version['version'],
	                        CURLOPT_USERPWD => env('GITHUB_AUTHENTICATION')
	        );

	        $curl = curl_init();
	        curl_setopt_array($curl,$options);
	        $data = json_decode( curl_exec($curl));
	        curl_close($curl);
	    }    


        return $data;
}


function csvToJson($filePath,$filter = null) 
{
 
    $csv= file_get_contents($filePath);
    $rows = explode("\n", trim($csv));
    $data = array_slice($rows, 1);
    $keys = array_fill(0, count($data), $rows[0]);
    $json = array_map(function ($row, $key) {
                            return array_combine(str_getcsv($key), str_getcsv($row));
                        
            		}, $data, $keys);

    if(isset($filter)){
        $filtered = array();
        foreach($json as $d){
                 if($d['id'] === $filter){
                    array_push($filtered,$d);
                 }  
        }
        $json = $filtered;
    }
    

    $json = json_encode($json);

    return $json;
}






