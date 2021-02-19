<?php
class Database
{

  private static $instance;
  private static $redisInstance;

  var $server   = ""; //database server
  var $user     = ""; //database login name
  var $pass     = ""; //database login password
  var $database = ""; //database name
  var $pre      = ""; //table prefix


  #######################
  //internal info
  var $error = "";
  var $errno = 0;

  //number of rows affected by SQL query
  var $affected_rows = 0;

  var $link_id;
  var $query_id = 0;

  var $debug = true;
  var $debugsql = true;
  var $commandtext = '';
  #-#############################################
  # desc: constructor
  function __construct($server, $user, $pass, $database, $pre = '')
  {
    $this->server = $server;
    $this->user = $user;
    $this->pass = $pass;
    $this->database = $database;
    $this->pre = $pre;
    $this->commandtext = "";
  } #-#constructor()

  public static function DB()
  {
    if (!isset(self::$instance)) {
      self::$instance = self::initDB();
    }
    return self::$instance;
  }

  public static function Redis()
  {
    // var_dump(!isset(self::$redisInstance));exit;
    try {
      if (!isset(self::$redisInstance)) {
        self::$redisInstance = self::initRedis();
      }
      return self::$redisInstance;
    } catch (Exception $e) {
      return null;
    }
  }

  public static function initDB()
  {
    global $HS, $ID, $PW, $DB;
    $db = new Database($HS, $ID, $PW, $DB);
    $db->connect();
    return $db;
  }

  public static function initRedis()
  {
    global $redis_host, $redis_port;
    try {
      $redis = new Redis();
      $redis->connect($redis_host, $redis_port);
    } catch (Exception $e) {
      return null;
    }
    return $redis;
  }

  #-#############################################
  # desc: connect and select database using vars above
  # Param: $new_link can force connect() to open a new link, even if mysql_connect() was called before with the same parameters
  function connect($new_link = false)
  {
    $dsn = 'mysql:host=' . $this->server . ';dbname=' . $this->database;

    $this->link_id = new PDO($dsn, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="NO_ENGINE_SUBSTITUTION"'));
    $this->link_id->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!$this->link_id) { //open failed
      $this->oops("Could not connect to server: <b>$this->server</b>.");
    }

    $this->link_id->query("SET NAMES 'UTF8'");
    //$this->link_id->query('set character set utf8');

    // unset the data so it can't be dumped
    $this->server = '';
    $this->user = '';
    $this->pass = '';
    $this->database = '';
  } #-#connect()



  #-#############################################
  # desc: close the connection
  function close()
  {
    $this->link_id = null;
    //if (!@sqlsrv_close($this->link_id)) {
    //$this->oops("Connection close failed.");
    //}
  } #-#close()


  #-#############################################
  # Desc: escapes characters to be mysql ready
  # Param: string
  # returns: string
  function escape($string)
  {
    if (get_magic_quotes_runtime()) $string = stripslashes($string);
    return @mysql_real_escape_string($string, $this->link_id);
  } #-#escape()


  #-#############################################
  # Desc: executes SQL query to an open connection
  # Param: (MySQL query) to execute
  # returns: (query_id) for fetching results etc
  function query($sql)
  {
    // do query
    try {
      $this->commandtext = $sql;
      $this->query_id = $this->link_id->query($sql);
      if (!$this->query_id) {
        $this->oops("Query fail:");
        return 0;
      }
    } catch (Exception $e) {
      $this->oops("Query fail");
    }
    //$this->affected_rows = @sqlsrv_rows_affected($this->link_id);
    return $this->query_id;
  } #-#query()

  function isExisit($table, $field, $val, $where = '')
  {
    try {
      $this->commandtext = "select $field  from $table where $field='$val' $where";
      $row = $this->query_first("select $field  from $table where $field='$val' $where");

      return ($row) ? true : false;
    } catch (Exception $e) {
      $this->oops("Query fail");
    }
  }

  //pdo的exec
  function exec($sql)
  {
    $count = 0;
    try {
      $this->commandtext = $sql;
      $count = $this->link_id->exec($sql);
    } catch (Exception $e) {
      $this->oops("Query fail");
    }

    return $count;
  }


  function queryCount($sql)
  {
    try {
      $this->commandtext = 'select count(*) from (' . $sql . ') as count';
      $this->query_id = $this->link_id->query('select count(*) from (' . $sql . ') as count');

      if ($this->query_id) {
        $count = $this->query_id->fetchColumn();
        $this->free_result($this->query_id);
        return $count;
      } else {
        $this->oops("Query fail:");
        return 0;
      }
    } catch (Exception $e) {
      $this->oops("Query fail");
    }
  } #-#query()
  #-#############################################
  # desc: fetches and returns results one line at a time
  # param: query_id for mysql run. if none specified, last used
  # return: (array) fetched record(s)
  function fetch_array()
  {
    if (isset($this->query_id)) {
      $record = $this->query_id->fetch(PDO::FETCH_ASSOC);
    } else {
      $this->oops("Invalid query_id: <b>$this->query_id</b>. Records could not be fetched.");
    }
    return $record;
  } #-#fetch_array()



  function fetch_all_array($sql, $ary = array())
  {
    if (count($ary) > 0) {
      return $this->preparefetch_all_array($sql, $ary);
    } else {
      $query_id = $this->query($sql);
      $out = @$this->query_id->fetchAll(PDO::FETCH_ASSOC);
      $this->free_result($query_id);
      return $out;
    }
  }


  #-#############################################
  # desc: frees the resultset
  # param: query_id for mysql run. if none specified, last used
  function free_result($query_id = -1)
  {

    if ($this->query_id && !@$this->query_id->closeCursor()) {
      $this->oops("Result ID: <b>$this->query_id</b> could not be freed.");
    }
  } #-#free_result()


  #-#############################################
  # desc: does a query, fetches the first row only, frees resultset
  # param: (MySQL query) the query to run on server
  # returns: array of fetched results
  function query_first($query_string, $ary = array())
  {

    if (count($ary) > 0) {
      return $this->query_prepare_first($query_string, $ary);
    } else {

      $query_id = $this->query($query_string);
      $out = $this->fetch_array($query_id);
      $this->free_result($query_id);
      return $out;
    }
  } #-#query_first()

  function preparefetch_all_array($query_string, $ary = array())
  {
    try {
      $this->commandtext = $query_string;
      $stmt = $this->link_id->prepare($query_string);
      $stmt->execute($ary);
      $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->free_result($stmt);
      return $out;
    } catch (Exception $e) {
      $this->oops('preparefetch_all_array Failed:');
    }
  }

  function query_prepare_first($query_string, $ary = array())
  {

    $this->commandtext = $query_string;
    $stmt = $this->link_id->prepare($query_string);
    $stmt->execute($ary);
    $out = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->free_result($stmt);

    if ($stmt->errorCode() && $stmt->errorCode() != 00000) {
      $this->oops("execute error : ", $stmt);
    } else {
      return $out;
    }
  }

  function execute($sql, $ary = array())
  {

    $this->commandtext = $sql;
    $stmt = $this->link_id->prepare($sql);
    $stmt->execute($ary);
    if ($stmt->errorCode() && $stmt->errorCode() != 00000) {
      $this->oops("execute error : ", $stmt);
    } else {
      return $stmt->rowCount();
    }
  }

  #-#############################################
  # desc: 更新Query
  # param: table (no prefix), assoc array with data (doesn't need escaped), where condition
  # returns: (query_id) for fetching results etc
  function query_update($table, $data, $where = '1', $wheredata = array())
  {
    $q = "UPDATE `" . $this->pre . $table . "` SET ";
    $ary = array();
    foreach ($data as $key => $val) {
      if (strtolower($val) == 'null') $q .= "`$key` = NULL, ";
      elseif (strtolower($val) == 'now()') $q .= "`$key` = NOW(), ";
      else {
        $q .= "`$key`=:" . $key . ", ";
        $ary[':' . $key] = $val;
      }
    }


    $q = rtrim($q, ', ') . ' WHERE ' . $where . ';';
    if (count($wheredata) > 0) {
      $ary = array_merge($ary, $wheredata);
    }

    return $this->execute($q, $ary);
  } #-#query_update()


  #-#############################################
  # desc: 將陣列內容新增到table
  # param: 陣列KEY值是欄位名稱,陣列元素是內容
  # returns: 回傳最後新增的自動編號的id
  function query_insert($table, $data)
  {
    $q = "INSERT INTO `" . $this->pre . $table . "` ";
    $v = '';
    $n = '';
    $ary = array();
    foreach ($data as $key => $val) {
      $n .= "`$key`, ";
      if (strtolower($val) == 'null') $v .= "NULL, ";
      elseif (strtolower($val) == 'now()') $v .= "NOW(), ";
      else {
        $v .= ":" . $key . ", ";
        $ary[':' . $key] = $val;
      };
    }

    $q .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";

    $stmt = $this->link_id->prepare($q);
    $stmt->execute($ary);
    if ($stmt->errorCode() && $stmt->errorCode() != 00000) {
      $this->oops("insert error : ", $stmt);
    } else {
      return $this->link_id->lastInsertId();
    }
  } #-#query_insert()


  function query_insert_update($table, $data, $dataupdate, $muli = false)
  { //增加ON DUPLICATE KEY UPDATE(表格名,資料ary(需含pk或uniqune),若重複則更新的資料ary)
    $q = "INSERT INTO `" . $this->pre . $table . "` ";
    $v = '';
    $n = '';
    $ary = array();
    if (!$muli) {
      foreach ($data as $key => $val) {
        $n .= "`$key`, ";
        if (strtolower($val) == 'null') $v .= "NULL, ";
        elseif (strtolower($val) == 'now()') $v .= "NOW(), ";
        else {
          $v .= ":" . $key . ", ";
          $ary[':' . $key] = $val;
        };
      }
      $q .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ")";
    } else {
      $v_ary = array();
      foreach ($data as $datakey => $row) {
        $v = '';
        foreach ($row as $key => $val) {
          if ($datakey === 0) $n .= "`$key`, ";
          if (strtolower($val) == 'null') $v .= "NULL, ";
          elseif (strtolower($val) == 'now()') $v .= "NOW(), ";
          else {
            $v .= ":" . $key . $datakey . ", ";
            $ary[':' . $key . $datakey] = $val;
          };
        }
        $v_ary[] = "(" . rtrim($v, ', ') . ")";
      }
      $q .= "(" . rtrim($n, ', ') . ") VALUES " . implode(',', $v_ary);
    }


    $q .= " ON DUPLICATE KEY UPDATE ";
    foreach ($dataupdate as $key => $val) {
      $v = '';
      $n = '';
      $n = " `$key`, ";
      if (strtolower($val) == 'null') $v = "NULL, ";
      elseif (strtolower($val) == 'now()') $v = "NOW(), ";
      else {
        $v = ":" . $key . "_update" . ", ";
        $ary[':' . $key . "_update"] = $val;
      };

      $q .=  rtrim($n, ', ') . "=" . rtrim($v, ', ') . ",";
    }
    $q = substr($q, 0, -1);
    $q .=  ";";
    $stmt = $this->link_id->prepare($q);
    $stmt->execute($ary);
    if ($stmt->errorCode() && $stmt->errorCode() != 00000) {
      $this->oops("insert error : ", $stmt);
    } else {
      return $this->link_id->lastInsertId();
    }
  } #-#query_insert()


  #-#############################################
  # desc: throw an error message
  # param: [optional] any custom error to display
  function oops($msg = '', $_error = null)
  {
    if ($this->debug == true) {
      if ($_error == null) {
        $_error = $this->link_id;
      }
      $error = $_error->errorInfo();
      $this->error = $error[0] . $error[2];
      $this->errno = $error[1];

      throw new Exception('DB:' . $msg . '<br>ERROR' . $this->error . ($this->debugsql == true ? 'Query:' . $this->commandtext : ''));
    }
  } #-#oops()

  function closeAutoCommit()
  {
    $this->query("SET AUTOCOMMIT=0");
  }
  function begin()
  {
    $this->query("SET AUTOCOMMIT=0");
    $this->query("BEGIN");
  }

  function commit()
  {
    $this->query("COMMIT");
    $this->query("SET AUTOCOMMIT=1");
  }

  function rollback()
  {
    $this->query("ROLLBACK");
    $this->query("SET AUTOCOMMIT=1");
  }
}//CLASS Database
###################################################################################################
