<?php

$db = new DB_Functions();

class DB_Connect {

  
  private $mysqli;
  
  // constructor
  function __construct() {
  
  }
  
  // destructor
  function __destruct() {
    // $this->close();
  }
  
  // Connecting to database
  public function connect() {
    // connecting to mysql
    
    $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    return $this->mysqli;
  }
  
  // Closing database connection
  public function close() {
    $this->mysqli->close();
  }

  // query
  public function query($q, $index = "")
  {
    if ($this->mysqli->real_query($q))
      {
        if ($result = $this->mysqli->use_result())
	  {
	    while ($row = $result->fetch_assoc())
	      {
		if ($index == "")
		  $res[] = $row;
		else
		  $res[$row[$index]] = $row;
		unset($row);
	      }
	    $result->close();
	    if (isset($res))
	      return ($res);
	  }
	return (TRUE);
      }
    return (FALSE);
  }

  public function real_escape_string($s)
  {
    return ($this->mysqli->real_escape_string($s));
  }

  public function insert_id()
  {
    return ($this->mysqli->insert_id);
  }

} 

class DB_Functions {
 
  private $db;
 
  //put your code here
  // constructor
  function __construct() {

    // connecting to database
    $this->db = new DB_Connect();
    $link = $this->db->connect();
    if ($link->connect_errno != 0)
      die("Cannot connect DB: ".$link->connect_error);
  }
 
  // destructor
  function __destruct() {
         
  }

  /**
   * set moderation
   */
  public function setModeration($id, $mod) {
    $result = $this->db->query("UPDATE ig SET moderation = '{$mod}' WHERE id = '{$id}'");
  }


  /**
   * Getting all pictures
   */
  public function alreadyExistsPicture($id) {
    $result = $this->db->query("SELECT id FROM ig WHERE id = '{$id}'", "id");
    return ($result[$id]['id'] == $id);
  }

  /**
   * Getting all pictures
   */
  public function getPictures($tag, $w = "", $l = "") {
    $q = "SELECT * FROM ig";
    if ($w != "")
      $q .= " WHERE (tag = '{$tag}' AND {$w})";
    if ($l != "")
      $q .= " {$l}";
    $result = $this->db->query($q, "id");
    return $result;
  }

  /**
   * Storing new device
   */
  public function storePicture($id, $tag, $code, $thumb, $full)
  {
    // remove previous record with same id
    $q = "DELETE FROM ig WHERE id = '{$id}'";
    $this->db->query($q, MYSQLI_STORE_RESULT);
    // insert picture into database
    $q = "INSERT INTO ig (id, tag, code, thumb, full, moderation) ".
      "VALUES('{$id}', '{$tag}', '{$code}', '{$thumb}', '{$full}', 0)";
    //echo "{$q}\n";
    return ($this->db->query($q));
  }

  public function insert_id()
  {
    return ($this->db->insert_id());
  }
}

?>