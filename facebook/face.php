<?php

require_once 'face/facebook.php';

class Face{
    
    /**
     *
     * @var facebook sdk object
     */
    private $facebook; 
    
    /**
     * @var int
     * @var user facebook id 
     */
    private $userid;
    
    /**
     *
     * @var array
     * user info array 
     */
    private $me;
    
    /**
     *facebook user permissin list
     * @var string 
     */
    private static $perms;
    
    /**
     *face class objcet
     * @var object 
     */
    private static $instance;
    
    /**
     *facebook connection details
     * @var array 
     */
    private static $params=[
            'appId'  => 'yourapiid',
            'secret' => 'yoursecretkey'
        ];
    
   
   private function __construct($perms=null) {
        if(!is_null($perms))
            self::setperms ($perms);
        $this->facebook=new Facebook(self::$params);
        $this->setuser();
      
       
       
        
    }
    
    private function __clone() {}
        
    
    
    /**
     * 
     * @return face object
     */
    
    public static function connect($perms=null)
    {
        if(!isset(self::$instance))
        {
            self::$instance=new self($perms);
        }
        return self::$instance;
    }
    
   /**
    * set facebook userid
    */
    
    private  function setuser()
    {
        $this->userid=$this->facebook->getUser();
    }
    
   /**
    * check facebook userid exists
    * @return boolean
    */
    public static function checkuser()
    {
        return (self::connect()->userid)?true:false;
    }
    
    
    /**
     * set facebook user profile information
     */
    public function setprofile()
    {
        if (self::checkuser()) {
          try {            
                $this->me= $this->facebook->api("/me"); 
                

          } catch (Exception $e) {
             
              $this->me=null;
              
          }
        }
    }
    
    /**
     * if user info exists return info else false retrun 
     * @return mix
     */
    public static function getuserdata()
    {
        if(is_null(self::connect()->me))
            self::connect()->setprofile();
        return (self::connect()->me)? self::connect()->me:false;
    }
    
    
    /**
     * 
     * @param mix $params
     * @return facebook login url
     */
    public static function loginurl($params=null)
    {
        if(!is_null($params))
            self::setperms ($params);
        return  self::connect()->facebook->getLoginUrl([
                           "scope" => (self::$perms)? self::$perms:""
                       ]);
    }
    
    /**
     * 
     * @return facebook logout url
     */
    public static function logouturl()
    {
        return self::connect()->facebook->getLogoutUrl();
    }
    
   /**
    * check if user logged in
    * @return boolean
    */
    
    public static function isloggedin()
    {
        return (self::connect()->me)?true:false;
    }
    
    /**
     * set permissions
     * @param mix $params
     */
    public static function setperms($params)
    {
        if(is_array($params))
           $params=  implode (",", $params);
        self::$perms=$params;
    }
    
    
    /**
     * 
     * @param string $field
     * @return boolean
     */
    public static function checkperms($field)
    {
        if(($result=self::getperms()))
            return in_array($field, $result);
        return false;
    }
    
    
    /**
     * run fql query
     * @param string $query
     * @return mix
     */
    public static function fql($query)
    {
         $result = [
            'method' => 'fql.query',
            'query' => $query
        ];
         
         return self::result($result);
    }
    
    /**
     * 
     * @param string $field
     * @return mix
     */
    private static function result($field)
    {
         if(self::connect()->me){
            return self::connect()->facebook->api($field);
            
        }
        return false;
    }
    
    /**
     * return user permissions
     * @return mix
     */
    public static function getperms()
    {
        $data=self::result('/me/permissions');
        return ($data['data'][0])? array_keys($data['data'][0]):false;
        
        
    }
    
    /**
     * return user friends list if right permission is given
     * @return mix
     */
    public static function friends()
    {
        
        return (self::checkperms("read_friendlists") && ($result=self::result("me/friends")))?$result:false;
   
    }
    
    /**
     * return user likes if right permission is given
     * @return boolean
     */
    public static function likes()
    {
        if(!self::checkperms("read_stream"))
            return false;
        $query="SELECT user_id, object_id, post_id FROM like WHERE user_id=me()";
        return self::fql($query);
    }
    
    /**
     * Post to the user feed if right permission is given
     * @param array $params
     * @return boolean
     */
    public static function post($params)
    {
        if(!is_array($params) || !self::checkperms("publish_actions"))return false;
        $result=self::connect()->facebook->api('/me/feed', 'POST', $params);
        return (isset($result['id']))?$result['id']:false;
    }
    
}
 
 

?>
