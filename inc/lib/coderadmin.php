<?php

class coderAdmin
{

  public static $Auth = array(
    'member' => array('key' => 2, 'name' => '會員', 'icon' => 'icon-user'),
    'reward' => array('key' => 4, 'name' => '獎項', 'icon' => 'icon-money'),
    'admin' => array('key' => 1, 'name' => '登入帳號', 'icon' => 'icon-lock'),
  );


  public static function Auth($key)
  {
    if (isset(self::$Auth[$key])) {
      return self::$Auth[$key];
    } else {
      die('權限錯誤~');
    }
  }


  public static function vaild($key)
  {
    if (!self::isAuth($key)) {
      self::drawBody('授權失敗!', '您未擁有操作此項功能的權限,請聯絡系統管理員。');
    }
  }


  public static function isAuth($key)
  {
    $user = self::getUser();
    $item = self::Auth($key);

    $result = false;

    if ($user['auth'] & $item['key']) {
      $result = true;
    } elseif ($user['isadmin'] == 2 && ($item['key'] == 2 || $item['key'] == 4 || $item['key'] == 16)) {
      //如果身分為老師($user['isadmin'] == 2)，且功能模組為課程($item['key'] == 4)
      $result = true;
    }elseif($user['isadmin'] == 3 && ($item['key'] == 2 || $item['key'] == 4 || $item['key'] == 8 || $item['key'] == 16)){
      // 身分為櫃台且功能能會員、課程、留言紀錄
      $result = true;
    }
    return $result;
  }


  public static function getAuthAry()
  {
    $ary = array();

    foreach (self::$Auth as $_key => $item) {
      if ($_key == 'admin') {
        continue;
      }
      $ary[] = self::getReturnElement($item);
    }

    return $ary;
  }


  public static function getAuthListAryByInt($int)
  {
    $ary = array();
    foreach (self::$Auth as $item) {
      if ($int & $item['key']) {
        $ary[] = self::getReturnElement($item);
      }
    }
    return $ary;
  }


  private static function getReturnElement($item)
  {
    return array('key' => $item['key'], 'name' => $item['name']);
  }

  public static function change_admin_data($username)
  {
    global $db, $admin_path_admin;
    $sql = "SELECT username,name,id,mid,ispublic,pic,auth,isadmin,branch_auth FROM " . coderDBConf::$admin . " WHERE username=:username";
    $row = $db->query_first($sql, array(':username' => $username));
    if ($row) {
      $user = array(
        'id' => $row['id'],
        'username' => $row['username'],
        'name' => $row['name'],
        'pic' => $admin_path_admin . 's' . $row['pic'],
        'time' => datetime('A h:i'),
        //'auth' => ($row["isadmin"]) ? 1099511627775 : $row['auth'],
        'auth' => $row["isadmin"] == 1 ? 1099511627775 : $row['auth'],
        'branch_auth' => $row['branch_auth'],
        'isadmin' => $row['isadmin'],
      );
      self::setUser($user);
    }
  }

  public static function loginOut()
  {
    global $webmanageurl;
    unCookie('mid', $webmanageurl);
    unset($_SESSION['manage_loginuser']);
  }

  public static function login($username, $password, $remember_me = '')
  {
    global $db, $webmanageurl, $admin_path_admin;


    $password = sha1($password);
    $sql = "SELECT username,name,id,mid,ispublic,pic,auth,isadmin,branch_auth FROM " . coderDBConf::$admin . " WHERE username=:username and password=:password";
    $row = $db->query_first($sql, array(':username' => $username, ':password' => $password));
    if (!$row) {
      coderAdminLog::insert($username, 'admin', 'login', '登入失敗-帳密不正確');
      throw new Exception('帳號或密碼不正確!');
    } else if ($row['ispublic'] != 1) {
      coderAdminLog::insert($username, 'admin', 'login', '登入失敗-己被停權');
      throw new Exception('此帳號己被停權!');
    } else {
      $user = array(
        'id' => $row['id'],
        'username' => $row['username'],
        'name' => $row['name'],
        'pic' => $admin_path_admin . 's' . $row['pic'],
        'time' => datetime('A h:i'),
        //'auth' => ($row["isadmin"]) ? 1099511627775 : $row['auth'],
        'auth' => $row["isadmin"] == 1 ? 1099511627775 : $row['auth'],
        'branch_auth' => $row['branch_auth'],
        'isadmin' => $row["isadmin"],
      );

      $mid_sessionid = self::getmid();
      $db->execute("update " . coderDBConf::$admin . " set logintime=:logintime,ip=:ip,mid=:mid where username=:username ", array(':logintime' => request_cd(), ':ip' => request_ip(), ':username' => $username, ':mid' => $mid_sessionid));

      if ($remember_me === 1) {
        saveCookieHour('mid', $mid_sessionid, 24 * 7, $webmanageurl, true);
      } else {
        unCookie('mid', $webmanageurl);
      }

      self::setUser($user);

      coderAdminLog::insert($username, 'admin', 'login', '成功');
    }
  }

  public static function pwHash($str)
  {
    return sha1($str);
  }

  public static function setUser($ary)
  {

    if (!is_array($ary)) {
      throw new Exception("USER格式不正確,儲存錯誤!");
    } else {
      $_SESSION['manage_loginuser'] = serialize($ary);
    }
  }

  public static function getUser_cookie()
  {
    //勾選記住我抓cookie
    global $db, $webmanageurl, $admin_path_admin;
    $mid = getCookie('mid');
    if ($mid != '' && empty($_SESSION['manage_loginuser'])) {
      $sql = "SELECT username,name,id,mid,ispublic,pic,auth,isadmin,branch_auth FROM " . coderDBConf::$admin . " WHERE mid=:mid";
      $row = $db->query_first($sql, array(':mid' => $mid));
      if ($row && $row['ispublic'] == 1) {
        $user = array(
          'id' => $row['id'],
          'username' => $row['username'],
          'name' => $row['name'],
          'pic' => $admin_path_admin . 's' . $row['pic'],
          'time' => datetime('A h:i'),
          //'auth' => ($row["isadmin"]) ? 1099511627775 : $row['auth'],
          'auth' => $row["isadmin"] == 1 ? 1099511627775 : $row['auth'],
          'branch_auth' => $row['branch_auth'],
          'isadmin' => $row['isadmin'],
        );
        $mid_sessionid = self::getmid();
        $db->execute("update " . coderDBConf::$admin . " set logintime=:logintime,ip=:ip,mid=:mid where username=:username ", array(':logintime' => request_cd(), ':ip' => request_ip(), ':username' => $row['username'], 'mid' => $mid_sessionid));
        self::setUser($user);
        saveCookieHour('mid', $mid_sessionid, 24 * 7, $webmanageurl, true);
        return true;
      } else {
        unCookie('mid', $webmanageurl);
      }
    }
    return false;
  }

  public static function getUser()
  {
    if (!isset($_SESSION['manage_loginuser'])) {
      if (self::getUser_cookie()) {

        return self::getUser();
      } else {
        self::showLoginPage();
      }
    } else {
      $user = unserialize($_SESSION['manage_loginuser']);
      if (!is_array($user)) {
        self::showLoginPage();
      } else {
        return $user;
      }
    }
  }

  /**
   * 身份為櫃台 取得負責的班級
   * @return string 
   */
  public static function getMyClass(){
    $result = '';
    $user = self::getUser();
    if($user['isadmin'] == 3){
      global $db;
      $table = coderDBConf::$admin;
      $sql = "select classes from $table where id ={$user['id']}";
      $data = $db->query_first($sql);
      $result = $data['classes']??'999';
    }
    return $result;
  }


  public static function getmid()
  {
    global $db;
    $mid = substr(md5(uniqid()) . time() . rand(1, 99999) . session_id(), 0, 90);
    if ($db->isExisit(coderDBConf::$admin, 'mid', $mid)) {
      $mid = self::getmid();
    }
    return $mid;
  }


  public static function sayHello()
  {

    $talktype = rand(0, 1);
    //一般問候
    if ($talktype == 0) {
      $ary_talk = array('歡迎登入。', '感謝您使用本系統', 'Hello :)', ' 阿囉哈', '記得要微笑 : )', '每隔30分鐘記得喝水,出去活動一下。', '來杯咖啡嗎?', ' hihi!!');
    } else {
      //依時間問候
      $hour = datetime('H');
      if ($hour > 5 && $hour < 9) { //早上5點到9點登入
        $ary_talk = array('早安!', '早起的鳥兒有蟲吃!', '您知道嗎?清晨的空氣特別新鮮', '您今天真早', '您早', '來杯咖啡嗎?', '記得吃早餐!', '今天真是個美好的一天,不是嗎?', '一日之計在於晨');
      } else if ($hour > 9 && $hour < 11) {
        $ary_talk = array('您今天加油了嗎?', ' 每天告訴自己一次,我真的很不錯', '抱最大的希望，為最大的努力，做最壞的打算', '喝口水吧', '每天都是一年中最美好的日子');
      } else if ($hour > 10 && $hour < 14) {
        $ary_talk = array('吃過飯了嗎?', ' 記得多吃點蔬菜水果喔~ ', '來根香蕉吧!', '多吃香蕉有益健康');
      } else if ($hour > 13 && $hour < 17) {
        $ary_talk = array('來杯下午茶吧。', '每一件事都要用多方面的角度來看它', '美好的生命應該充滿期待、驚喜和感激。', '天才是百分之一的靈感加上百分之九十九的努力', '您累了嗎? 喝杯水吧休息一下吧。', '肚子餓的話,吃些點心吧。');
      } else if ($hour > 16 && $hour < 20) {
        $ary_talk = array('今天沒什麼事就早點下班吧', '記得吃晚餐', '晚餐不要吃太多,身體才健康', '想像力比知識更重要', '晚餐請不要吃太多');
      } else if ($hour > 19 && $hour < 23) {
        $ary_talk = array('您辛苦了', '別忙到太晚', '加油加油!', '如果你曾歌頌黎明，那麼也請你擁抱黑夜', '吃晚餐了嗎?', '沒什麼事就早點休息吧', '研究指出,加班會降低工作效率', '千萬別吃宵夜', '睡前別喝太多水,會水腄');
      } else if ($hour > 22 && $hour < 02) {
        $ary_talk = array('請去休息吧!', '研究指出,加班會降低工作效率', '您睡不著嗎?', '感謝每盞亮著的燈,沒留下你一個人', '經驗是由痛苦中粹取出來的', '天才是百分之一的靈感加上百分之九十九的努力');
      } else {
        $ary_talk = array('................', '唔....', '嗯.....', '現在是下班時間吧?', '天才是百分之一的靈感加上百分之九十九的努力', 'XD', '囧');
      }
      echo $hour;
    }
    return '您好 ' . $ary_talk[rand(0, count($ary_talk) - 1)];
  }


  public static function showLoginPage()
  {
    self::drawBody('登入逾時', '您尚未登入或超過登入期限<br>為了確保安全性<br>請按下方連結重新登入。');
  }

  public static function drawMenu($submenu)
  {
    $user = self::getUser();
    $auth = $user['auth'];
    $query_str = $_SERVER['QUERY_STRING'];
    $query_str = $query_str != '' ? '?' . $query_str : '';
    $pagename = realpath(request_basename()) . $query_str;

    $admin_auth = $user['isadmin']; //身分(0非特殊身分 1最高權限管理者 2老師)

    if ($admin_auth == 2) {
      //身分為老師者，看到的submenu不一樣
      // 20201023 因老師權限調整  也要可以看到 app 故增加 app 的選單
      $array = ['course','app'];
      $escape = ['授權','系統文章','學員文章','功能參數設定','閱讀測驗','快速檢測'];
      $str = '';
      foreach($array as $row){
        $item = self::$Auth[$row];
        $str.= '
                   <a href="javascript:void(0)" class="dropdown-toggle">
                       <i class="' . $item['icon'] . '"></i>
                       <span>' . $item['name'] . '</span>
                       <b class="arrow icon-angle-right"></b>
                   </a>';
        if (array_key_exists($row, $submenu) || array_key_exists($row, $submenu)) {
          $str .= '<ul class="submenu">';
          foreach ($submenu[$row] as $subkey => $subitem) {
            if($subkey == '課程分類管理' || in_array($subkey,$escape)){
              continue;
            }
            $index = strpos($subitem, '?');
            if ($index > 0) {
              $_subitem = realpath(substr($subitem, 0, $index)) . substr($subitem, $index);
            } else {
              $_subitem = realpath($subitem);
            }
  
            $classname = '';
            if (realpath(request_basename()) == $_subitem || $pagename == $_subitem) {
              $classname = ' class="active" ';
              $str .= '<li ' . $classname . '><a href="' . $subitem . '" >' . $subkey . '</a></li>';
            } else {
              $str .= '<li ><a href="' . $subitem . '" >' . $subkey . '</a></li>';
            }
          }
          $str .= '</ul>';
        }
      }
      echo '<li ' . $classname . '>' . $str . '</li>';
    } elseif ($admin_auth == 3) {
      $array = ['member', 'course', 'contact','app'];
      foreach ($array as $row) {
        $key = $row;
        $item = self::$Auth[$key];
        $str = '
                   <a href="javascript:void(0)" class="dropdown-toggle">
                       <i class="' . $item['icon'] . '"></i>
                       <span>' . $item['name'] . '</span>
                       <b class="arrow icon-angle-right"></b>
                   </a>';

        if (array_key_exists($key, $submenu)) {
          $str .= '<ul class="submenu">';
          foreach ($submenu[$key] as $subkey => $subitem) {
            $index = strpos($subitem, '?');
            if ($index > 0) {
              $_subitem = realpath(substr($subitem, 0, $index)) . substr($subitem, $index);
            } else {
              $_subitem = realpath($subitem);
            }

            $classname = '';
            if (realpath(request_basename()) == $_subitem || $pagename == $_subitem) {
              $classname = ' class="active" ';
              $str .= '<li ' . $classname . '><a href="' . $subitem . '" >' . $subkey . '</a></li>';
            } else {
              $str .= '<li ><a href="' . $subitem . '" >' . $subkey . '</a></li>';
            }
          }
          $str .= '</ul>';
        }
        echo '<li ' . $classname . '>' . $str . '</li>';
      }
    } else {
      foreach (self::$Auth as $key => $item) {
        if ((!($auth & $item['key']) || (isset($item['show']) && $item['show'] == false))) {
          continue;
        }
        $classname = '';
        $str = '
                  <a href="javascript:void(0)" class="dropdown-toggle">
                      <i class="' . $item['icon'] . '"></i>
                      <span>' . $item['name'] . '</span>
                      <b class="arrow icon-angle-right"></b>
                  </a>';

        if (array_key_exists($key, $submenu)) {
          $str .= '<ul class="submenu">';
          foreach ($submenu[$key] as $subkey => $subitem) {
            $index = strpos($subitem, '?');
            if ($index > 0) {
              $_subitem = realpath(substr($subitem, 0, $index)) . substr($subitem, $index);
            } else {
              $_subitem = realpath($subitem);
            }

            if (realpath(request_basename()) == $_subitem || $pagename == $_subitem) {
              $classname = ' class="active" ';
              $str .= '<li ' . $classname . '><a href="' . $subitem . '" >' . $subkey . '</a></li>';
            } else {
              $str .= '<li ><a href="' . $subitem . '" >' . $subkey . '</a></li>';
            }
          }
          $str .= '</ul>';
        }
        echo '<li ' . $classname . '>' . $str . '</li>';
      }
    }
  }

  public static function array_diff_assoc_auth($array1, $array2)
  {
    $ary2 = array();
    foreach ($array2 as $val) {
      $ary2[$val['key']][] = $val['name'];
    }
    foreach ($array1 as $key => $value) {
      if (!isset($ary2[$value['key']]) || !in_array($value['name'], $ary2[$value['key']])) {
        $difference[$key] = $value;
      }
    }
    return !isset($difference) ? 0 : $difference;
  }

  public static function drawBody($title, $content)
  {

    die(' < !DOCTYPE html >
		<html >
			<head >
				<meta charset = "utf-8" >
				<meta http - equiv = "X-UA-Compatible" content = "IE=edge,chrome=1" >
				<meta name = "description" content = "" >
				<meta name = "viewport" content = "width=device-width, initial-scale=1.0" >
				<link rel = "stylesheet" href = "../assets/bootstrap/css/bootstrap.min.css" >
				<link rel = "stylesheet" href = "../assets/font-awesome/css/font-awesome.min.css" >
				<!--flaty css styles-->
				<link rel = "stylesheet" href = "../css/flaty.css" >
				<link rel = "stylesheet" href = "../css/flaty-responsive.css" >
			</head >
			<body class="error-page" >
			<div class="error-wrapper" >
					<div >
					</div >
					<h5 ><img src = "../images/logo.png" width = "60%" ><span > OOPS</span ></h5 ><p ><h5 > ' . $title . '</h5 ></p ><p >
            ' . $content . '
					<hr >
					<p class="clearfix" >
						<a href = "javascript:void(0)" onclick = "if(document.referrer!=null)history.go(-1)" class="pull-left" > ← 回到前一頁 </a >
						<a href = "../login.php" class="pull-right" > 回到登入頁</a >
					</p >
			 <!--basic scripts-->
				<script src = "../assets/jquery/jquery-2.0.3.min.js" ></script >
				<script src = "../assets/bootstrap/js/bootstrap.min.js" ></script >
			</body >
		</html >
            ');
  }
}
