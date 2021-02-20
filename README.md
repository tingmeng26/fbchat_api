## 基本資料

通路總數 3,630
```
  國小 2,663
  國中 961
  其他(實體+線上) 6
```
序號總數 2,230,981
```
  採每通路人數+100產生序號 前三碼為該通路檢核碼
  國小 1,436,912
  國中 704,069
  其他 90,000
```
## 環境
<div title="PHP version"><img src="https://img.shields.io/badge/php-%3E=_7.2.14-green.svg"></div>
<div title="Lumen version"><img src="https://img.shields.io/badge/lumen framework-%3E=_5.5-green.svg"></div>
<div title="Mysql version"><img src="https://img.shields.io/badge/Mysql-%3E=_5.7-green.svg"></div>
<div title="Composer version"><img src="https://img.shields.io/badge/Composer-%3E=_1.7.2-green.svg"></div>

## 安裝
```
cd api
複製 _example.env 改名為.env 並設定相關參數
composer install
對應 env 裡 DATABASE建立資料表 本例中命名為beethoven
執行 php artisan migrate
```

## Swagger 文件
```
/api/public/documentation
```

# API 路徑
```
/api/public/api
```
