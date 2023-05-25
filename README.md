# Internal API Documentation

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [1. Client Cache](#1-client-cache)
  - [1.1 Create Cache on Client](#11-create-cache-on-client)
- [2. File Transfer](#2-file-transfer)
  - [2.1 Download File](#21-download-file)
  - [2.2 Upload File](#22-upload-file)


## Introduction

This document describes the internal API of the application. The internal API is used in communication of backend. The internal API is not exposed to the public and is only used by the backend.

## 1. Client Cache

### 1.1 Create Cache on Client

```
POST /iapi/cache
```

Remote server request server to create cache on server file system.


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



## 2. File Transfer

### 2.1 Download File

```
GET /iapi/file
```

Remote server download file from server.


#### Request: String in HTTP Header, Parameter(s) in Query String

Header

| Name            | Type     | Description                                                |
| :-------------- | :------- | :--------------------------------------------------------- |
| `Authorization` | `string` | **(Required)** Bearer type token，token content is API Key |


```
Authorization: Bearer b01fd1c05d93e77a887fa6c8c91088bb7f053fb220eaf87d51e5efa30fea9d25
```


Query String

| Name       | Type     | Description                                          |
| :--------- | :------- | :--------------------------------------------------- |
| `filename` | `string` | **(Required)** path and filename of downloading file |


```
https://example.com/iapi/file?filename=public/images/1.jpg
```


#### Success Response: String in HTTP Header

```
Status: 200 OK
Content-Type: image/jpg
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

### 2.2 Upload File

```
POST /iapi/file
```

Remote server upload file to server.


#### Request: String in HTTP Header, Parameter(s) in HTTP Body

Header

| Name            | Type     | Description                                                |
| :-------------- | :------- | :--------------------------------------------------------- |
| `Authorization` | `string` | **(Required)** Bearer type token，token content is API Key |


```
Authorization: Bearer b01fd1c05d93e77a887fa6c8c91088bb7f053fb220eaf87d51e5efa30fea9d25
```


Body

| Name       | Type     | Description                                        |
| :--------- | :------- | :------------------------------------------------- |
| `content`  | `binary` | **(Required)** uploading file                      |
| `filename` | `string` | **(Required)** path and filename of uploading file |


```
content: (binary)
filename: ./public/images/1.jpg
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
    "message": "keys and filenames count not match"
}
```

