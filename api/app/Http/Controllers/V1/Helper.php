<?php

namespace App\Http\Controllers\V1;

use App\Events\ExampleEvent;
use App\Events\SaveLog;
use App\Events\SaveLogEvent;
use App\func;
use \App\Http\Controllers\Controller;
use App\Model\Log;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class Helper extends Controller
{


  /**
   * 一般文字訊息
   */
  public static function textTemplate($text)
  {
    return [
      'text' => $text
    ];
  }

  /**
   * 圖片輪播訊息
   */
  public static function multipleGenericTemplate($data)
  {
    return [
      'attachment' => [
        "type" => "template",
        "payload" => [
          "template_type" => "generic",
          "elements" => $data,
        ]
      ]
    ];
  }

  /**
   * 一般按鍵訊息   
   */
  public static function buttonTemplate($btn_content, $text = "請選擇")
  {
    $r = [
      'attachment' => [
        "type" => "template",
        "payload" => [
          "template_type" => "button",
          "text" => $text,
          "buttons" => $btn_content
        ]
      ]
    ];

    return  $r;
  }

  /**
   * 處理 post 使用者留言
   */
  public static function processText($text)
  {
    switch ($text) {
      case '開始':

        break;

      default:

        break;
    }
  }
  /**
   * 處理 post postback互動留言
   */
  public static function processPayload($payload)
  {
  }

  /**
   * 傳入使用者留言 回傳對應題目或文字
   * @param string message
   * @return array message
   */
  public static function getMessage($message): array
  {
    $response = [];
    switch ($message) {
      case '開始':
        $data = [];
        $text = "Hi! 我是留學國師 aka 雅師。" .
          "或許你正在徬徨無助，或許你只需" .
          "要一點幫助。沒關係！我會盡力協" .
          "助你。接下來，我會問你幾個非常" .
          "簡單的問題，預測最適合你留學的" .
          "國家。準備好我們就開始囉！";
        $button = [
          [
            "type" => "postback",
            "title" => "I’m ready!",
            "payload" => "AGREEMENT"
          ],
        ];
        $data['message'] = self::buttonTemplate($button, $text);;
        $response[] = $data;
        break;
      case 'AGREEMENT':
        $data = [];
        $data['message'] = self::textTemplate('Wait! 在開始之前~');
        $response[] = $data;
        $data = [];
        $data['message'] = self::textTemplate("此遊戲內容與結果僅純屬娛樂，並不代表英國文化協會立場\n\n"
          . "[注意!] 若您未滿18歲，請先告知您的家長將參與此活動，並請由您的家長填寫以下資訊，以參與抽獎活動。\n\n"
          . "英國文化協會將依據活動須知使用您的Email來參與此活動的抽獎，同時，英國文化協會有意使用您提供的資料Email和Social Media，寄送您可能有興趣的活動及服務相關訊息給您。\n\n"
          . "若不再繼續訂閱時，也可以隨時取消。我們將依據您的意願處理您的個人資料。");
        $response[] = $data;
        $data = [];
        $text = "資訊保護\n\n"
          . "英國文化協會遵循英國及世界各國之資料保護法案，以符合個資保護之國際標準。您可以要求我們提供您的個資檔案紀錄，也有權向我們要求更正您的資料。若您對我們的個資使用方式有所疑慮，您有權向隱私監管機構申訴。有關個資保護說明，請瀏覽英國文化協會官網 www.britishcouncil.org/privacy ，或聯繫英國文化協會。我們將按照英國文化協會資料保存政策，將您的個資檔案保管7年。";
        $button = [
          [
            "type" => "postback",
            "title" => "Agree!",
            "payload" => "Q1"
          ],
        ];
        $data['message'] = self::buttonTemplate($button, $text);
        $response[] = $data;
        break;
      case 'Q1':
        $data = [];
        $data['message'] = self::textTemplate('Firstly，我將給你4首歌的部分歌詞。試試看，哪一句歌詞，讓你立刻哼起旋律？');
        $response[] = $data;
        $data = [];
        $data['message'] = self::textTemplate('往右滑看更多選項👉');
        $response[] = $data;
        $data = [];
        $array = self::getQuestion('Q1');
        $data['message'] = self::multipleGenericTemplate($array);
        $response[] = $data;
        break;

      case substr($message, -2) == 'Q2':
        // 存第一題答案
        // saveAnswer($key, $data, 'a1');
        // 第二題題目
        $data = [];
        $data['message'] = self::textTemplate('閉上眼，跟我一起說：I’m starving.');
        $r[] = $data;
        $data = [];
        $data['message'] = self::textTemplate('然後張開眼，眼前這4個食物，你最想吃哪一個？');
        $r[] = $data;
        $data = [];
        $data['message'] = self::textTemplate('往右滑看更多選項👉');
        $r[] = $data;
        $data = [];
        $array = self::getQuestion('Q2');
        $data['message'] = self::multipleGenericTemplate($array);
        $r[] = $data;
        break;
    }
    return $response;
  }

  public static function sendMessage($message)
  {

    $token = env('TOKEN');
    $result = self::postCurl("https://graph.facebook.com/v10.0/me/messages?access_token={$token}", json_encode($message));
    event(new SaveLog('send message: ' . $result));
    return $result;
  }

  /**
   * 傳入問題代號 回傳題目
   * @param string q1
   */
  public static function getQuestion($number): array
  {
    $array = [];
    $sourceUrl = env('APP_URL', 'https://325a54bd419f.ngrok.io/fbchat_api/') . 'source/';
    $question  = [
      'Q1' => [
        0 => [
          'txt' => "My youth, my youth is yours🎵Trippin' on skies, sippin' waterfalls",
          'btn' => [
            [
              "type" => "postback",
              "title" => "迷幻美麗少年 Troye Sivan",
              "payload" => "ANSQ1A1.Q2"
            ],
          ],
          'pic' => $sourceUrl . 'question1/0.jpg'
        ],
        1 => [
          'txt' => "I found a love for me🎵Darling, just dive right in",
          'btn' => [
            [
              "type" => "postback",
              "title" => "紅髮艾德 Ed Sheeran",
              "payload" => "ANSQ1A2.Q2"
            ],
          ],
          'pic' => $sourceUrl . 'question1/1.jpg'
        ],
        2 => [
          'txt' => "Yeah, you got that yummy, yum🎵That yummy, yum",
          'btn' => [
            [
              "type" => "postback",
              "title" => "小賈斯汀 Justin Bieber",
              "payload" => "ANSQ1A3.Q2"
            ],
          ],
          'pic' => $sourceUrl . 'question1/2.jpg'
        ],
        3 => [
          'txt' => "So you‘re a tough guy🎵Like it really rough guy",
          'btn' => [
            [
              "type" => "postback",
              "title" => "怪物新人 Billie Eilish",
              "payload" => "ANSQ1A4.Q2"
            ],
          ],
          'pic' => $sourceUrl . 'question1/3.jpg'
        ],
      ],
      'Q2' => [
        0 => [
          'txt' => "奶香四溢的酥脆派與濃郁鮮味",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Stargazy Pie",
              "payload" => "ANSQ2A1.Q3"
            ],
          ],
          'pic' => $sourceUrl . 'question2/0.jpg'
        ],
        1 => [
          'txt' => "酥炸薯條佐鮮美肉汁與罪惡起司",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Poutine",
              "payload" => "ANSQ2A2.Q3"
            ],
          ],
          'pic' => $sourceUrl . 'question2/1.jpg'
        ],
        2 => [
          'txt' => "拍照系美食彩虹貝果",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Rainbow Bagel",
              "payload" => "ANSQ2A3.Q3"
            ],
          ],
          'pic' => $sourceUrl . 'question2/2.jpg'
        ],
        3 => [
          'txt' => "南半球國民美食 令人難忘的滋味",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Vegemite",
              "payload" => "ANSQ2A4.Q3"
            ],
          ],
          'pic' => $sourceUrl . 'question2/3.jpg'
        ],
      ],
      'Q3' => [
        0 => [
          'txt' => "Welsh Corgi",
          'btn' => [
            [
              "type" => "postback",
              "title" => "柯基 ❤️",
              "payload" => "ANSQ3A1.Q4"
            ],
          ],
          'pic' => $sourceUrl . 'question3/0.jpg'
        ],
        1 => [
          'txt' => "Boston Terrier",
          'btn' => [
            [
              "type" => "postback",
              "title" => "波士頓㹴 ❤️",
              "payload" => "ANSQ3A2.Q4"
            ],
          ],
          'pic' => $sourceUrl . 'question3/1.jpg'
        ],
        2 => [
          'txt' => "Labrador Retriever",
          'btn' => [
            [
              "type" => "postback",
              "title" => "拉布拉多犬 ❤️",
              "payload" => "ANSQ3A3.Q4"
            ],
          ],
          'pic' => $sourceUrl . 'question3/2.jpg'
        ],
        3 => [
          'txt' => "Australian Terrier",
          'btn' => [
            [
              "type" => "postback",
              "title" => "澳洲㹴 ❤️",
              "payload" => "ANSQ3A4.Q4"
            ],
          ],
          'pic' => $sourceUrl . 'question3/3.jpg'
        ],
      ],
      'Q4' => [
        0 => [
          'txt' => "世界遺產 班夫國家公園",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Banff National Park",
              "payload" => "ANSQ4A1.Q5"
            ],
          ],
          'pic' => $sourceUrl . 'question4/0.jpg'
        ],
        1 => [
          'txt' => "世界奇景 羚羊峽谷",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Antelope Canyon",
              "payload" => "ANSQ4A2.Q5"
            ],
          ],
          'pic' => $sourceUrl . 'question4/1.jpg'
        ],
        2 => [
          'txt' => "世界文化遺產 巨石陣",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Stonehenge",
              "payload" => "ANSQ4A3.Q5"
            ],
          ],
          'pic' => $sourceUrl . 'question4/2.jpg'
        ],
        3 => [
          'txt' => "世界最美公路 大洋路",
          'btn' => [
            [
              "type" => "postback",
              "title" => "The Great Ocean Road",
              "payload" => "ANSQ4A4.Q5"
            ],
          ],
          'pic' => $sourceUrl . 'question4/3.jpg'
        ],
      ],
      'Q5' => [
        0 => [
          'txt' => "放鬆愜意的海邊民宿",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Beach House",
              "payload" => "ANSQ5A1.END"
            ],
          ],
          'pic' => $sourceUrl . 'question5/0.jpg'
        ],
        1 => [
          'txt' => "充滿皇家感的城堡花園",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Castle Garden",
              "payload" => "ANSQ5A2.END"
            ],
          ],
          'pic' => $sourceUrl . 'question5/1.jpg'
        ],
        2 => [
          'txt' => "如童話故事的森林小屋",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Forest Cottage",
              "payload" => "ANSQ5A3.END"
            ],
          ],
          'pic' => $sourceUrl . 'question5/2.jpg'
        ],
        3 => [
          'txt' => "市中心的城市現代公寓",
          'btn' => [
            [
              "type" => "postback",
              "title" => "Urban Apartment",
              "payload" => "ANSQ5A4.END"
            ],
          ],
          'pic' => $sourceUrl . 'question5/3.jpg'
        ],
      ]
    ];
    foreach ($question[$number] as $row) {
      array_push($array, [
        'title' => $row['txt'],
        'image_url' => $row['pic'],
        'buttons' => $row['btn']
      ]);
    }
    return $array;
  }


  public static function postCurl($url, $data, $headers = null, $debug = false, $CA = false, $CApem = "", $timeout = 30)
  {
    //網址,資料,header,返回錯誤訊息,https時驗證CA憑證,CA檔名,超時(秒)
    global $path_cacert;
    $result = "";
    $cacert = $path_cacert . $CApem;
    //CA根证书
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    if ($SSL && $CA && $CApem == "") {
      return "請指定CA檔名";
    }
    if ($headers == null) {
      $headers = [
        'Content-Type: application/json',
      ];
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    //允許執行的最長秒數
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
    //連接前等待時間(0為無限)
    //$headers == '' ? '' : curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($SSL && $CA) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
      // 驗證CA憑證
      curl_setopt($ch, CURLOPT_CAINFO, $cacert);
      // CA憑證檔案位置
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    } elseif ($SSL && !$CA) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      // 信任任何憑證
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    if ($debug === true && curl_errno($ch)) {
      echo 'GCM error: ' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
  }
}
