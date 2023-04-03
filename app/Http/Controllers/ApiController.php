<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        return view('api_view');
    }

    public function fetchData(Request $request)
    {
        $url = $request->input('url');
        $client = new \GuzzleHttp\Client();
        $errorMessage = '';
        $user = '';
        $missingFields = [];
        
        try{
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody(), true);
            if(isset($data['users']))
            {
                $user = $data['users'][0];
            } else {
                $user = $data;
            }
            

            $requiredFields = ['firstName', 'lastName', 'age', 'gender', 'phone'];
            
            foreach($requiredFields as $field)
            {
                if(!array_key_exists($field, $user)){
                    $missingFields[] = $field;
                }
            }

            //transform data
            if(empty($missingFields))
            {
                //Ensure email is lowercase
                $user['email'] = strtolower($user['email']);

                //ensure age is number
                $user['age'] = intval($user['age']);

                //Ensure the first character of the first and last name is Uppercase
                $user['firstName'] = ucwords($user['firstName']);
                $user['lastName'] = ucwords($user['lastName']);
            }
        } catch(\GuzzleHttp\Exception\RequestException $e){
            $errorMessage = $e->getMessage();
        }
        
        

        return view('api_view', compact('user', 'missingFields', 'errorMessage'));
    }
}