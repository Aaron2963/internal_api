# Internal API Documentation

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [1. Client Cache](#1-client-cache)
  - [1.1 Create Cache on Client](#11-create-cache-on-client)
- [2. Site Settings](#2-site-settings)
  - [2.1 Read Site Settings](#21-read-site-settings)
  - [2.2 Write Site Settings](#22-write-site-settings)


## Introduction

This document describes the internal API of the application. The internal API is used in communication of backend. The internal API is not exposed to the public and is only used by the backend.

## 1. Client Cache

### 1.1 Create Cache on Client

```
POST /iapi/cache
```

Remote request server to create cache on server file system.


#### Request: String in HTTP Header, Parameter(s) in HTTP Body

Header

| Name            | Type     | Description                                                |
| :-------------- | :------- | :--------------------------------------------------------- |
| `Authorization` | `string` | **(Required)** Bearer type token，token content is API Key |


```
Authorization: Bearer b01fd1c05d93e77a887fa6c8c91088bb7f053fb220eaf87d51e5efa30fea9d25
```


Body

| Name        | Type     | Description                                                                                                                          |
| :---------- | :------- | :----------------------------------------------------------------------------------------------------------------------------------- |
| `fromDB`    | `string` | **(Required)** source database table and column name                                                                                 |
| `storePath` | `string` | **(Required)** the path of directory storing caches                                                                                  |
| `keys`      | `string` | **(Required)** the primary keys of caching data, splited by comma, the number of keys must be identical with the number of filenames |
| `filenames` | `string` | **(Required)** the filename of caches, splited by comma, the number of filenames must be identical with the number of keys           |


```
fromDB: user_info.intro_json
storePath: ./cache/user-intro
keys: id001,id002,id003
filenames: about-us.json,contact-us.json,privacy-policy.json
```


#### Success Response: String in HTTP Header

```
Status: 200 OK
```
```json
[
    "/var/www/cache/user-intro/about-us.json",
    "/var/www/cache/user-intro/contact-us.json",
    "/var/www/cache/user-intro/privacy-policy.json"
]
```


#### Error Response

```
Status: 400 Bad Request
```
```json
{
    "status": "error",
    "message": "keys and filenames count not match"
}
```



## 2. Site Settings

### 2.1 Read Site Settings

```
GET /api/site-settings
```

遠端向伺服器取得網站設定。  
伺服器收到請求後，從伺服器的檔案系統取得網站設定檔案，解析後回傳給遠端。  

**Direction:** 遠端向伺服器

**Restriction:** 每周限制 1000 次


#### Request: String in HTTP Header, Parameter(s) in Qeury String

Header

| Name            | Type     | Description                                    |
| :-------------- | :------- | :--------------------------------------------- |
| `Authorization` | `string` | **(必填)** Bearer 類型令牌，令牌內容為 API Key |


```
Authorization: Bearer b01fd1c05d93e77a887fa6c8c91088bb7f053fb220eaf87d51e5efa30fea9d25
```


Query String

| Name      | Type              | Description                                                                                                                                                                                                   |
| :-------- | :---------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `keys`    | `array of string` | 網站設定欄位名稱的陣列                                                                                                                                                                                        |
| `keys[i]` | `string`          | 網站設定的欄位名稱，可能的值為: `siteName/logoImageUrl/footerImageUrl/serviceTel1/serviceFax1/serviceAddress/serviceHours/serviceEmail/ownerName/linkLine/linkFacebook/linkYouTube/servicePeriod/serviceTel2` |


```
/api/site-settings?keys[]=siteName&keys[]=logoImageUrl&keys[]=serviceTel1
```


#### Success Response: String in HTTP Header

```
Status: 200 OK
```
```json
{
    "siteName": "雙葉書廊課程網",
    "logoImageUrl": [
        "https://g4.misa.com.tw/yehyeh/data/cht/20220929/20220929tb3aku.jpg"
    ],
    "serviceTel1": "02-2368-4198"
}
```


#### Error Response

```
Status: 401 Unauthorized
```



### 2.2 Write Site Settings

```
PUT /api/site-settings
```

遠端更新伺服器端的網站設定。  
伺服器收到請求後，根據請求內容更新網站設定檔案，並回傳更新後的欄位給遠端。  

**Direction:** 遠端向伺服器

**Restriction:** 每周限制 1000 次


#### Request: String in HTTP Header, Parameter(s) in HTTP Body

Header

| Name            | Type     | Description                                    |
| :-------------- | :------- | :--------------------------------------------- |
| `Authorization` | `string` | **(必填)** Bearer 類型令牌，令牌內容為 API Key |


```
Authorization: Bearer b01fd1c05d93e77a887fa6c8c91088bb7f053fb220eaf87d51e5efa30fea9d25
```


Body

| Name   | Type     | Description                                                                                                                                                                                                           |
| :----- | :------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `json` | `string` | 網站設定的 JSON 格式資料，可設定的鍵為: `siteName/logoImageUrl/footerImageUrl/serviceTel1/serviceFax1/serviceAddress/serviceHours/serviceEmail/ownerName/linkLine/linkFacebook/linkYouTube/servicePeriod/serviceTel2` |


```
json: {"siteName":"雙葉書廊課程網","logoImageUrl":["https://g4.misa.com.tw/yehyeh/data/cht/20220929/20220929tb3aku.jpg"],"serviceTel1":"02-2368-4198"}
```


#### Success Response: String in HTTP Header

```
Status: 204 No Content
```


#### Error Response

```
Status: 400 Bad Request
```
```json
{
    "status": "error",
    "message": "Request body `json` is not a valid JSON string."
}
```
