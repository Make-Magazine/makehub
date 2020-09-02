<?php
//include '../../../../wp-load.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

//First do the authentication
$url = "https://makermedia.auth0.com/oauth/token";
$post_data = "{\"grant_type\":\"client_credentials\",\"client_id\": \"Ya3K0wmP182DRTexd1NdoeLolgXOlqO1\",\"client_secret\": \"eu9e8LC7fvrKb9ou5JglKdIv67QDvhkiMg32vm0q433SMXD5PW3elCV7OuiSFs6n\",\"audience\": \"https://makermedia.auth0.com/api/v2/\"}";
$authRes = curlCall($url, $post_data);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <h2 style='text-align: center'>Auth0 Users</h2>
    </body>
</html>
<?php
if (isset($authRes->access_token)) {
    echo 'Authenticated<br/>';
    $token = $authRes->access_token;
    //echo 'returned token is ' . $token;
    $jobID = (isset($_GET['jobID']) ? $_GET['jobID'] : '');
    if ($jobID == '') {
        //get connections   
        echo '<hr>';
        echo 'Get Connections result<br/>';
        $post_data = '';
        $url = "https://makermedia.auth0.com/api/v2/connections";
        $authRes = curlCall($url, $post_data, $token);
        foreach ($authRes as $result) {
            if ($result->name === "Username-Password-Authentication") {
                //if($result->name==="DB-Make-Community"){
                $connection = $result->id;
            }
        }
        echo '$connection is ' . $connection . '<br/>';

        // submit a job to get all auth0 users
        $url = "https://makermedia.auth0.com/api/v2/jobs/users-imports";
        echo 'URL is '.$url.'<br/>';
  
        $post_data = array('connection_id'=> $connection, "users"=>"http://community.make.co/wp-content/themes/make-co/devScripts/auth0update.json");
        $post_data = json_encode($post_data);
        /*$post_data = '{'.
                        '"connection_id": "'.$connection.'", '.
                        '"users":'.
                '"http://community.make.co/wp-content/themes/make-co/devScripts/auth0update.json"'
                /*
        
        '[{
        "email": "alicia.williams130@gmail.com",
        "email_verified": false,        
        "username": "alicia-williams130",
        "given_name": "Alicia",
        "family_name": "Williams", 
        "name": "Alicia Williams",
        "password_hash": "$2y$10$2dGT92U3.8Oe/1q/cF3wsO5EAyEBJzxMK314CnvGSTW83Y8f1YRzi",     
        "user_metadata.first_name": "Alicia",
        "user_metadata.last_name": "Williams",
        "user_metadata.Month": "1",
        "user_metadata.Day": "11",
        "user_metadata.Year": "1976"    
        }]'*/
        // ."}";
        echo '$post_data is '.$post_data . '<br/><br/>';
        $type = 'multipart/form-data';        
        
        $authRes = curlCall($url, $post_data, $token, $type);
        if (isset($authRes->id)) {
            $jobID = $authRes->id;
            echo '<hr>';
            echo 'Submitted Job ID ' . $jobID . '<br/>';
            echo 'To Pull the results click <a href="https://makerfaire.com/wp-content/themes/makerfaire/devScripts/auth0UserExport.php?jobID=' . $jobID . '">here' . '</a>';
        } else {
            //echo '<hr>';
            //error
            //var_dump($authRes);
        }
    } else {
        //to get resultsjobID
        //Check the status of the job         
        $url = "https://makermedia.auth0.com/api/v2/jobs/" . $jobID;
        $post_data = "";
        $authRes = curlCall($url, $post_data, $token);
        if ($authRes->status === 'completed') {
            echo 'Get your results <a href="' . $authRes->location . '">Here</a>';
        } else {
            echo 'Job Status = ' . $authRes->status . '. Please refresh';
        }
    }

    //var_dump($authRes);
    die();
    echo '<hr>';
    echo 'Auth0 returned users<br/>';
    if (isset($authRes->error)) {
        echo'Error on CURL call: ';
        if (isset($authRes->statusCode))
            echo 'Status Code = ' . $authRes->statusCode . '<br/>';
        if (isset($authRes->error))
            echo ' ' . $authRes->error . ' ';
        if (isset($authRes->message))
            echo ' - ' . $authRes->message . ' ';
        if (isset($authRes->errorCode))
            echo '(' . $authRes->errorCode . ')';
    } else {
        var_dump($authRes);
        die();
        $outCSV = array();
        $keyArr = array('date');

        $my_file = 'auth0Users-' . date('m-d-Y_hia') . '.csv';
        echo 'Creating ' . $my_file . '<br/>';
        $output = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

        foreach ($authRes as $k => $authLog) {
            $result = [];
            $authLog = json_decode(json_encode($authLog), true);  //translate multi dimensional onject to array
            //flatten multi dimensional array
            array_walk_recursive($authLog, function($item, $key) use (&$result) {
                $result[$key] = $item;
            });
            $arr = array_keys($result);
            foreach ($arr as $keys) {
                if (!in_array($keys, $keyArr)) {
                    $keyArr[] = $keys;
                }
            }
            $outCSV[] = $result;
        }

        //var_dump($keyArr);
        fputcsv($output, $keyArr);  //write keydata to CSV

        foreach ($outCSV as $csv) {
            foreach ($keyArr as $key) {
                if (isset($csv[$key])) {
                    $outData[$key] = $csv[$key];
                } else {
                    $outData[$key] = '';
                }
            }

            fputcsv($output, $outData);  //write to CSV
        }
        fclose($output);
    }   
}

function curlCall($service_url, $curl_post_data, $token = '', $type='application/json') {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $service_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_POST => true,
    ));
    //be verbose and tell me the error
    curl_setopt($curl, CURLOPT_VERBOSE, true);

    $verbose = fopen('php://temp', 'w+');
    curl_setopt($curl, CURLOPT_STDERR, $verbose);
    
    if ($curl_post_data !== '') {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        if ($token !== '') {
            
            //curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer " . $token, "content-type: ".$type, $type = 'boundary=----WebKitFormBoundaryzuW5nPZQFQCwQtg4'));
            if($type=="multipart/form-data"){
                $boundary = uniqid();
                $gen_boundry = '-------------' . $boundary;
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer " . $token, 
                                                         "content-type: multipart/form-data; boundary=".$gen_boundry));
            }else{
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer " . $token, "content-type: ".$token));
            }
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: ".$type));
        }
    } else {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer " . $token, "content-type: ".$type));
    }


    $curl_response = curl_exec($curl);
    if ($curl_response === FALSE) {
        printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
        htmlspecialchars(curl_error($handle)));
    }
    $authRes = json_decode($curl_response);
    if(isset($authRes->statusCode) && $authRes->statusCode!=200){
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);

        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
    }
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    }

    
    if (isset($authRes->error)) {
        echo'<hr>Error on CURL call:<br/>';
        if (isset($authRes->statusCode))
            echo 'Status Code = ' . $authRes->statusCode . '<br/>';
        if (isset($authRes->error))
            echo 'Error = ' . $authRes->error . '<br/>';
        if (isset($authRes->message))
            echo 'Message = ' . $authRes->message . '<br/>';
        if (isset($authRes->errorCode))
            echo 'Error Code = (' . $authRes->errorCode . ')'. '<br/>';
    }
    return $authRes;
}
