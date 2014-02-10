#Introduction

Piray Application Platform 

Usage: 
* http://localhost/
* http://localhost/storage/

---------

#Datebase Schema

###user
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint 			 | 流水號 |
| name			| varchar(255)	 | 使用者名稱 |
| password		| varchar(255)	 | 密碼 |
| level			| varchar(255)	 | 權限 |
| description	| text			 | 敘述 |

-----------
###device


| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint | 流水號 |
| device_id		|varchar(17)|MAC Address ID|
| name			|varchar(255)|機器名稱|
| type			|varchar(20)| piplayer/picam|
| comment		|text|記錄說明|

---------------
###device_config
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint | 流水號 |
| key			| varchar(255) | 欄位 |
| value			| varchar(255) | 值 |

-------------
###course
| Field        | type           | Comment  |
| ------------- |:-------------:| -----:|
| id      			| uint 		    | 流水號  |
| file_id			| varchar(255)  | 實體檔案編碼名稱 |
| teacher_id		| uint      	| 老師table關連id |
| room				| varchar(255)  | 錄影教室 |
| name  			| varchar(255)  | 課程名稱 |
| file_resolution   | varchar(20)   | 影像解析度|
| file_size			| uint		    | 影像大小 |
| record_start_time	| uint		    | 開始時間 |
| record_end_time	| uint 	        | 結束時間 |
| file_duration	    | uint		    | 影片長度 |
| preserved			| bool		    | 典藏（不會被自動移除）|
| is_valid			| bool		    | 驗證是否檔案有效 |


~~~
給人看的檔名為：課程名稱_老師名稱_錄影年月日_時分秒
example: 程式設計_吳刻宗_2014-02-09_14:53:00
~~~
------------
###booking
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint 			 | 流水號 |
| course_id		| uint 			 | 課程編號	 |
| student_id 	| uint           | 補課學生id 	|
| booking_time	| uint			 | 預約補課時間 |
| status		| enum      	 | 補課狀態 	|
| check_time	| uint			 | 學生報到時間 |
| comment		| text 			 | 其他資訊 	|

~~~
- booking_time 透過時間去對應補課時段
- check_time 當學生來報到時(status=checked)寫入當下時間 
- staus狀態
	* booking - 預約狀態
	* checked - 學生報到
	* cancel  - 取消補課預約
~~~

------------
###booking_setting
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint          | 流水號 |
| key			| varchar(255)  | 欄位 |
| value			| varchar(255)  | 值 |

~~~
time_slice : {json}
~~~

------------
###teacher
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint			 | 流水號 |
| name			| varchar(255) 	 | 老師名稱 |
| is_valid		| bool 			 | 是否已經解雇或沒用(預設為true) |
| comment		| text 			 | 其他資訊 |

------------
###student
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint 			 | 流水號 |
| name			| varchar(255) 	 | 學生名稱 |
| phone			| varchar(20) 	 | 電話 |
| comment		| text 			 | 其他資訊 |

------------
###system_config
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint | 流水號 |
| key           | varchar(255) | 欄位 |
| value			| varchar(255) | 值 |

~~~
- server_ip	- 主機的IP位置，預設192.168.0.100
- video_path - 影片存放位置 
- allowed_transfer - 是否允許遠端傳輸
- random_password - 遠端傳輸臨時密碼
- shutdown_time - 關機時間
~~~

------------
###log
| Field         | type           | Comment  |
| ------------- |:-------------:| -----:|
| id 		  	| uint 			 | 流水號 |
| class			| varchar(255)	 | 模組分類 |
| user_id		| uint			 | 使用者id |
| message		| text			 | 操作記錄 |
| time			| uint			 | 時間 |


