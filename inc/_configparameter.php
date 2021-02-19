<?php
/*Upload path*/
//所有檔案上傳的暫存
$admin_path_temp = "../../upload/temp/";
//admin
$admin_path_admin = "../upload/admin/";
//會員
$path_member = $web_root . 'upload/member/';
$admin_path_member = '../../upload/member/';
//課程分類
$admin_path_course_type = '../../upload/course_type/';

$API_PATH_MEMBER = 'upload/member/';


/*Cache path*/

$cache_path = __DIR__ . 'upload/cache/';
$cache_path_web = $cache_path;
$cache_path_mob = __DIR__ . $cache_path;
$cache_path_do = __DIR__ . $cache_path;
$cache_path_admin = '../../upload/cache/';
//ckeditor
$path_ckeditor = $weburl . 'upload/editor/'; //ckeditor中路徑
$admin_path_ckeditor = "../../upload/editor/"; //上傳放置(以後台位置來看)
$db_path_ckeditor = 'upload/editor/'; //存入資料庫時改為


/*Image setup*/
//會員照片
$member_pic_w = 151;
$member_pic_h = 123;
//課程分類照片
$course_type_pic_w = 136;
$course_type_pic_h = 110;


/*Cache name*/
$web_cache = array('web_meta' => 'web_meta');


/*創建資料夾列表*/
$ary_mkdir = array('manage' => 'Web_Manage/', 'upload' => 'upload/');


/*資料用ARY*/
//基本常用
$incary_sex = array(1 => '男', 2 => '女');
$incary_yn = array(0 => '否', 1 => '是');
$incary_yn_layout = array('<span class="label">否</span>', '<span class="label label-success">是</span>');
$incary_labelstyle = array(0 => 'default', 1 => 'success', 2 => 'pink', 3 => 'gray', 4 => 'important', 5 => 'warning', 6 => 'yellow', 7 => 'lime', 8 => 'magenta', 9 => 'inverse');
$incary_labelstyle2 = array(1 => 'default', 2 => 'pink', 3 => 'info', 4 => 'warning', 5 => 'lime');
//會員
$incary_member_status = array(0 => '停權', 1 => '有效');
$incary_member_type = array(1 => '體驗會員', 2 => '正式會員', 3 => 'APP會員');
//報名
$incary_register_statue = array(1 => '未繳費', 2 => '處理中', 3 => '已繳費');
//課程
$incary_course_type = array(1 => '幼幼', 2 => '基礎', 3 => '初階', 4 => '進階');
$incary_course_status = array(0 => '未開始', 1 => '進行中', 2 => '已結束');
//成績
$incary_score_type = array(1 => '期初', 2 => '期中', 3 => '期末');
//繳費
$incary_payment = array(1 => '匯款', 2 => '現場');
//星期
$incary_day_type = array(
  1 => array('name' => '星期一', 'value' => 1),
  2 => array('name' => '星期二', 'value' => 2),
  3 => array('name' => '星期三', 'value' => 3),
  4 => array('name' => '星期四', 'value' => 4),
  5 => array('name' => '星期五', 'value' => 5),
  6 => array('name' => '星期六', 'value' => 6),
  0 => array('name' => '星期日', 'value' => 0),
);
//課堂評分的特殊類型
$incary_course_grade_type = array(
  1 => '檢定考',
  2 => '結業',
  3 => '晉級',
);
//數字轉換
$incary_number_ch = array(0 => '0', 1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '七', 8 => '八', 9 => '九', 10 => '十');
//檢定考等別(index務必按照順序新增)
$incary_grade1 = array(
  1 => '初',
  2 => '中',
  3 => '高',
  4 => '優',
  5 => '特優',
);
$incary_grade1_en = array(
  1 => 'PRIMARY',
  2 => 'INTERMEDIATE',
  3 => 'ADVANCED',
  4 => 'EXCELLENT',
  5 => 'PREMIUM',
);
//聯絡我們
$incary_qna_status = array(1 => '已回覆', 2 => '未回覆');

// 各項參數設定

$APP_CONFIG = [
  'mission' => 15,
  'warmup' => [
    'speed' => 1,
    'times' => 3,
    'target' => 0
  ],
  'speed' => [
    'speed' => 400,
    'type' => 1,
    'words' => 8,
    'target' => 0
  ],
  'eye' => [
    'speed' => 1,
    'rows' => 1,
    'words' => 2,
    'quantity' => 2,
    'target' => 0
  ]
];

// 文章語系

$ARTICLE_LANGUAGE = [
  1 => '繁中',
  2 => '英',
  3 => '日'
];

$GROUP = [
  1 => '第一組',
  2 => '第二組',
  3 => '第三組',
  4 => '第四組',
  5 => '第五組',
  6 => '第六組',
  7 => '第七組',
  8 => '第八組',
  9 => '第九組'
];


$FINISH = [
  1 => '已完成',
  0 => '未完成'
];

$RECEIVE = [
  1 => '已領取',
  0 => '尚未領取'
];

$SATISFACTION = [
  1 => '非常滿意', 2 => '滿意', 3 => '普通', 4 => '不滿意', 5 => '非常不滿意'
];

$AGREE = [
  0 => '不同意',
  1 => '同意'
];

$LOTTERY_TYPE = [
  '1' => '累積參展人數前10萬名',
  '2' => '累積參展人數前30萬名',
  '3' => '累積參展人數前60萬名',
  '4' => '累積參展人數前100萬名',
  '9' => '其他'

];


$SCHOOL_TYPE = [
  'e' => '國小',
  'j' => '國中',
  'o' => '其他'

];



$CITY = [
  [
    'code' => '01',
    'name' => '新北市'
  ],
  [
    'code' => '02',
    'name' => '宜蘭縣'
  ],
  [
    'code' => '03',
    'name' => '桃園市'
  ],
  [
    'code' => '04',
    'name' => '新竹縣'
  ],
  [
    'code' => '05',
    'name' => '苗栗縣'
  ],
  [
    'code' => '06',
    'name' => '臺中市'
  ],
  [
    'code' => '07',
    'name' => '彰化縣'
  ],
  [
    'code' => '08',
    'name' => '南投縣'
  ],
  [
    'code' => '09',
    'name' => '雲林縣'
  ],
  [
    'code' => '10',
    'name' => '嘉義縣'
  ],
  [
    'code' => '11',
    'name' => '臺南市'
  ],
  [
    'code' => '12',
    'name' => '高雄市'
  ],
  [
    'code' => '13',
    'name' => '屏東縣'
  ],
  [
    'code' => '14',
    'name' => '臺東縣'
  ],
  [
    'code' => '15',
    'name' => '花蓮縣'
  ],
  [
    'code' => '16',
    'name' => '澎湖縣'
  ],
  [
    'code' => '17',
    'name' => '基隆市'
  ],
  [
    'code' => '18',
    'name' => '新竹市'
  ],
  [
    'code' => '20',
    'name' => '嘉義市'
  ],
  [
    'code' => '30',
    'name' => '臺北市'
  ],
  [
    'code' => '71',
    'name' => '金門縣'
  ],
  [
    'code' => '72',
    'name' => '連江縣'
  ],
  [
    'code' => '94',
    'name' => '通路'
  ],


];
