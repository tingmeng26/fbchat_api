<?php

class coderDBConf
{
    /*↓↓必須↓↓*/
    public static $admin = 'admin';//後台管理者
    public static $admin_log = 'admin_log';//後台管理者操作紀錄

    public static $col_admin = array('id' => 'id', 'mid' => 'mid', 'username' => 'username', 'password' => 'password', 'name' => 'name', 'email' => 'email', 'introduction' => 'introduction', 'pic' => 'pic', 'auth' => 'auth', 'branch_auth' => 'branch_auth', 'isadmin' => 'isadmin', 'ispublic' => 'ispublic', 'forgetcode' => 'forgetcode', 'forgetcode_time' => 'forgetcode_time', 'ip' => 'ip', 'admin' => 'admin', 'logintime' => 'logintime', 'createtime' => 'createtime', 'updatetime' => 'updatetime');
    /*↑↑必須↑↑*/



    //主會員
    public static $main_members = 'main_members';
    public static $col_main_members = array('id'=>'id','account'=>'account','number'=>'number','group'=>'group','expire_time'=>'expire_time','access_token'=>'access_token','refresh_token'=>'refresh_token','access_token_expire_time'=>'access_token_expire_time','refresh_token_expire_time'=>'refresh_token_expire_time','is_public'=>'is_public','createe_at'=>'created_at','updated_at'=>'updated_at');

    //會員
    public static $members = 'members';
    public static $col_members = array('id' => 'member_id', 'username' => 'username', 'student_id' => 'm_student_id', 'password' => 'password', 'realname' => 'real_name', 'english_name' => 'english_name', 'email' => 'email', 'birth' => 'birth', 'addr' => 'address', 'cel' => 'cell_phone', 'verify' => 'verify_status', 'nickname' => 'nickname', 'fbid' => 'fb_id', 'sex' => 'sex', 'score' => 'score_total', 'person_id' => 'person_id', 'parents' => 'parents_name', 'retrain' => 'is_retrain', 'org' => 'organization', 'school' => 'school','grade'=>'grade', 'pic' => 'pic', 'introducer' => 'introducer', 'remarks' => 'remarks', 'status' => 'status', 'type' => 'type', 'level' => 'lv', 'advanced' => 'is_advanced', 'admin' => 'admin', 'createtime' => 'join_date', 'updatetime' => 'modify_date');

    //報名
    public static $member_register = 'ex_signup_list';
    public static $col_member_register = array('id' => 'esl_id', 'emid' => 'esl_em_id', 'sub_emid' => 'esl_sub_em_id', 'eccid' => 'esl_ec_id', 'price' => 'esl_price', 'retrain' => 'esl_is_retrain', 'statue' => 'esl_statue', 'ind' => 'esl_ind', 'admin' => 'esl_admin', 'ispublic' => 'esl_ispublic', 'createtime' => 'esl_createtime', 'updatetime' => 'esl_updatetime');

    //繳費
    public static $member_pay = 'ex_pays_list';
    public static $col_member_pay = array('id' => 'epl_id', 'eslid' => 'epl_esl_id', 'eccid' => 'epl_ecc_id', 'emid' => 'epl_em_id', 'paydate' => 'epl_pays_date', 'payrecord' => 'epl_pays_record', 'paid_up' => 'epl_paid_up', 'payment' => 'epl_payments', 'remarks' => 'epl_remarks', 'ind' => 'epl_ind', 'admin' => 'epl_admin', 'ispublic' => 'epl_ispublic', 'createtime' => 'epl_createtime', 'updatetime' => 'epl_updatetime');

    //成績
    public static $member_score = 'ex_report';
    public static $col_member_score = array('id' => 'er_id', 'eccid' => 'er_ec_id', 'ecdid' => 'er_ecd_id', 'emid' => 'er_em_id', 'type' => 'er_type', 'absorb' => 'er_absorb', 'memory' => 'er_memory', 'reading' => 'er_reading', 'understanding' => 'er_understanding', 'test_paper_no' => 'er_test_paper_no', 'ispublic' => 'er_ispublic', 'ind' => 'er_ind', 'admin' => 'er_admin', 'createtime' => 'er_createtime', 'updatetime' => 'er_updatetime');

    //課程分類
    public static $course_type = 'ex_course_type';
    public static $col_course_type = array('id' => 'ect_id', 'type' => 'ect_type', 'title' => 'ect_title', 'subtitle' => 'ect_subtitle', 'content' => 'ect_content', 'price' => 'ect_price', 'retrain_price' => 'ect_retrain_price', 'is_exper' => 'ect_is_exper', 'is_retrain' => 'ect_is_retrain', 'pic' => 'ect_pic', 'ispublic' => 'ect_ispublic', 'ind' => 'ect_ind', 'admin' => 'ect_admin', 'createtime' => 'ect_createtime', 'updatetime' => 'ect_updatetime');

    //課程細項
    public static $course_class = 'ex_course_class';
    public static $col_course_class = array('id' => 'ecc_id', 'classid' => 'ecc_class_id', 'ectid' => 'ecc_ect_id','t_id'=>'ecc_teacher_id', 'title' => 'ecc_title', 'subtitle' => 'ecc_subtitle', 'content' => 'ecc_content', 'capacity' => 'ec_capacity', 'price' => 'ecc_price', 'retrain_price' => 'ecc_retrain_price', 'is_retrain' => 'ecc_is_retrain', 'post_from' => 'ecc_post_from', 'post_to' => 'ecc_post_to', 'course_from' => 'ecc_course_from', 'course_to' => 'ecc_course_to', 'open_time' => 'ecc_open_time', 'ind' => 'ecc_ind', 'admin' => 'ecc_admin', 'ispublic' => 'ecc_ispublic', 'createtime' => 'ecc_createtime', 'updatetime' => 'ecc_updatetime');

    //課程課表
    public static $course_detail = 'ex_course_detail';
    public static $col_course_detail = array('id' => 'ecd_id', 'eccid' => 'ecd_ecc_id', 'date' => 'ecd_date', 'time_from' => 'ecd_time_from', 'time_to' => 'ecd_time_to', 'ind' => 'ecd_ind', 'admin' => 'ecd_admin', 'createtime' => 'ecd_createtime', 'updatetime' => 'ecd_updatetime');

    //班級
    public static $classes = 'classes';
    public static $col_classes = array('id' => 'Board_No', 'title' => 'Title', 'subtitle' => 'Title2', 'note' => 'note', 'content' => 'Content', 'type' => 'Type');

    //評等
    public static $course_grade = 'ex_course_grade';
    public static $col_course_grade = array('id' => 'ecg_id', 'eccid' => 'ecg_ecc_id', 'emid' => 'ecg_member_id', 'type' => 'ecg_type', 'pass' => 'ecg_pass', 'admin' => 'ecg_admin', 'createtime' => 'ecg_createtime', 'updatetime' => 'ecg_updatetime');

    //檢定考
    public static $course_exam = 'ex_course_exam';
    public static $col_course_exam = array('id' => 'ece_id', 'eccid' => 'ece_ecc_id', 'emid' => 'ece_member_id', 'type' => 'ece_type', 'reading' => 'ece_reading', 'understanding' => 'ece_understanding', 'grade1' => 'ece_grade1', 'grade2' => 'ece_grade2', 'passed' => 'ece_passed','test_paper_no'=>'ece_test_paper_no', 'admin' => 'ece_admin', 'createtime' => 'ece_createtime', 'updatetime' => 'ece_updatetime');

    //課程諮詢
    public static $course_qna = 'ex_course_class_msg';
    public static $col_course_qna = array('id' => 'id', 'name' => 'name', 'eccid' => 'ecc_id', 'ectid' => 'ect_id', 'content' => 'content', 'email' => 'email', 'phone' => 'tel', 'status' => 'state', 'reply_subject' => 'reply_subject', 'reply_content' => 'reply_msg', 'admin' => 'ecq_admin', 'createtime' => 'post_date', 'updatetime' => 'reply_date');

    //聯絡我們
    public static $contact = 'contact';
    public static $col_contact = array('id' => 'c_id', 'name' => 'c_name', 'content' => 'c_content', 'email' => 'c_email', 'phone' => 'c_phone', 'status' => 'c_status', 'reply_subject' => 'c_reply_subject', 'reply_content' => 'c_reply_content', 'admin' => 'c_admin', 'createtime' => 'c_createtime', 'updatetime' => 'c_updatetime');


    // APP
    // main member
    public static $appMainMember = 'app_main_members';
    public static $colAppMainMember = array('id'=>'m_id','accessToken'=>'m_access_token','refreshToken'=>'m_refresh_token','accessTokenExpireTime'=>'m_access_token_expire_time','refreshTokenExpireTime'=>'m_refresh_token_expire_time','lastLoginTime'=>'m_last_login_time','updatetime'=>'m_updatetime','createtime'=>'m_createtime');

    // members
    public static $appMember = 'app_members';
    public static $colAppMember = array('id'=>'m_id','accessToken'=>'m_access_token','refreshToken'=>'m_refresh_token','accessTokenExpireTime'=>'m_access_token_expire_time','refreshTokenExpireTime'=>'m_refresh_token_expire_time','load_class'=>'m_load_class','lastLoginTime'=>'m_last_login_time','updatetime'=>'m_updatetime','createtime'=>'m_createtime');

    // authorization
    public static $appAuth = 'app_auth';
    public static $colAppAuth = array('id'=>'id','m_id'=>'m_id','c_id'=>'c_id','date'=>'date','resource'=>'resource','at'=>'at','updatetime'=>'updatetime','createtime'=>'createtime');

    // setting
    public static $appSetting = 'app_setting';
    public static $colAppSetting = array('id'=>'id','m_id'=>'m_id','c_id'=>'c_id','type'=>'type','date'=>'date','setting'=>'setting','updatetime'=>'updatetime','createtime'=>'createtime');

    // article 
    public static $appArticle = 'app_article';
    public static $colAppArticle = array('id'=>'a_id','m_id'=>'a_m_id','type'=>'a_type','use'=>'a_use','words'=>'a_words','language'=>'a_lang','title'=>'a_title','content'=>'a_content','ind'=>'a_ind','last_update'=>'a_last_update','last_update_at'=>'a_last_update_at','ispublic'=>'a_ispublic','isdefault'=>'a_isdefault','updatetime'=>'a_updatetime','createtime'=>'a_createtime');

    // article detail
    public static $appArticleDetail = 'app_article_detail';
    public static $colAppArticleDetail = array('id'=>'ad_id','a_id'=>'ad_a_id','type'=>'ad_type','question'=>'ad_question','answer'=>'ad_answer','options'=>'ad_options','ind'=>'ad_ind','last_update'=>'ad_last_update','last_update_at'=>'ad_last_update_at','updatetime'=>'ad_updatetime','createtime'=>'ad_createtime');

    // article default
    public static $appArticleDefault = 'app_article_default';
    public static $colAppArticleDefault = array('id'=>'id','m_id'=>'m_id','a_id'=>'a_id','updatetime'=>'updatetime','createtime'=>'createtime');

    // speed test
    public static $appSpeedTest = 'app_speed_test';
    public static $colAppSpeedTest = array('id'=>'id','m_id'=>'m_id','c_id'=>'c_id','date'=>'date','words'=>'words','updatetime'=>'updatetime','createtime'=>'createtime');
    
    // daily mission
    public static $appDailyMission = 'app_daily_mission';
    public static $colAppDailyMission = array('id'=>'d_id','m_id'=>'d_m_id','c_id'=>'d_c_id','mission'=>'d_mission','finish'=>'d_finish','date'=>'d_date','target'=>'d_target','detail'=>'d_detail','updatetime'=>'d_updatetime','createtime'=>'d_createtime');

    // exam
    public static $appExam = 'app_exam';
    public static $colAppExam = array('id'=>'e_id','m_id'=>'m_id','c_id'=>'c_id','date'=>'date','a_id'=>'a_id','words'=>'words','score'=>'score','answer'=>'answer','updatetime'=>'updatetime','createtime'=>'createtime');

    // class setting
    public static $appClassSetting = 'app_class_setting';
    public static $colAppClassSetting = array('id'=>'id','c_id'=>'c_id','setting'=>'setting','updatetime'=>'updatetime','createtime'=>'createtime');
}
