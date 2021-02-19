<?php
$ary_submenu = array(
  'admin' => array(
    '帳號管理' => $manage_path . 'admin/index.php',
    '操作歷程記錄' => $manage_path . 'adminlog/index.php',
  ),
  /* ## coder [submenu] --> ## */
  'member' => array(
    '單位管理' => '../unit/index.php',
    '會員管理' => '../member/index.php',
  ),
  'reward' => array(
    '獎項管理' => '../reward/index.php',
    '獎項中獎者管理' => '../winner/index.php',
    '彩蛋獎項管理' => '../extra_reward/index.php',
    '彩蛋獎中獎者管理' => '../extra_winner/index.php'
  )

  /* ## coder [submenu] <-- ## */
);
