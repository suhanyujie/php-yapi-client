### 简要描述
- 用户注册接口

### relate_flag
- group=1688
- project=xxx
- cateid=xxxx

### 请求URL
- `http://api.example.com/api/user/register`
  
### 请求方式
- POST 
- `Content-type: application/json`

### 参数 

参数名|是否必填|类型|说明
|:----    |:---|:----- |:-----   |
|`username` |是  |string |用户名   |
|`password` |是  |string | 密码    |

#### 请求参数示例

```json
{

}
```
    
### 接口返回
#### 返回示例

```json
{
    "err_no": 0,
    "err_msg": "",
    "results": {
        "list": [
            {
                
            }
        ],
        "pagination": {
            "page": 1,
            "size": 10,
            "total": 20
        }
    }
}
```

#### 返回参数说明 

|参数名|类型|说明|
|:-----  |:-----|:-----                           |
|`err_no` |int   | 接口返回状态值 `0`表示正常 其他表示异常，异常时，`err_msg` 字段会有详细异常信息  |
|`err_msg` |string   | 异常时，`err_msg` 的详细异常信息  |
|`results` |object   | 接口返回的数据 |

### 备注
- 无
