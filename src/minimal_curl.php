<?php
/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */
$request = curl_init($url); // initiate curl object
      curl_setopt($request, CURLOPT_HEADER, 0); // set to 1 to get the header info in the response
      curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
      curl_setopt($request, CURLOPT_POSTFIELDS, $postData); // use HTTP POST to send form data

      //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
      if ( preg_match('/^https/', $url) ) {
         curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($request, CURLOPT_SSL_VERIFYHOST, FALSE);
      }

      // execute curl post and store results in $post_response
      if( ! $result = curl_exec($request) ){
      
      }