<?php

require 'face/facebook.php';
require 'face.php';

    
// check user logged in .
Face::checkuser();

//return facebook login url
Face::loginurl();

//return login url with permisson request
Face::loginurl("email,read_friendlists,read_stream");
//or with array
Face::loginurl(["email","read_friendlists","read_stream"]);



// set permission request 

Face::setperms("email,read_friendlists,read_stream");
//or set with array
Face::setperms(["email","read_friendlists","read_stream"]);

//return user data
Face::getuserdata();


//return permission list 
Face::getperms();


//return if permission is given by user
Face::checkperms("email");

//return facebook logout url
Face::logouturl();

//post to user feed
Face::post(['message'=>"Playing facebook sdk...."]);

//return user's friend list
Face::friends();

//return user's likes
Face::likes();

//checks if user logged in or not

Face::isloggedin();

//return the result of fql
Face::fql("SELECT user_id, object_id, post_id FROM like WHERE user_id=me()");



if(!Face::checkuser()){
    
   echo "<a href='".Face::loginurl()."'>login with facebook</a>"; 
}else{    
  
    echo "<a href=.".Face::logouturl()."'>logout</a>"; 
}




?>