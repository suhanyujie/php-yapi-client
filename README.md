# php-yapi-client
* 一个 [yapi](https://hellosean1025.github.io/yapi/index.html) 客户端，为了方便将本地编写的 markdown 文档解析上传到公司/组织的 yapi 文档管理中心

## instruction
### 痛点
* yapi 后台编辑文档繁琐、效率低下，如果直接在本地编辑 markdown 文档，然后通过命令行更新到 yapi 后台，此时效率大大提升。

## usage
### 下载 
- `git clone https://github.com/suhanyujie/php-yapi-client.git`
- `cd php-yapi-client`
- `composer install`
- 打包成 phar `./build.php`
- MacOS：`cp yc.phar /usr/local/bin/yc`

### 运行
- 1.配置文件
    - `cp env.example .env`
    - 根据需要，在 `.env` 文件中修改 yapi 服务的地址以及 token
- 2.按照要求写文档
    - 要使用该 yapi 客户端，需要必须按照合法的要求写 markdown，格式参考[此模板](docs/md_doc_template.md)。
- `yc /path/businessName/loginDoc.md`

## todo
- [ ] 更新文档到组织的 yapi 服务中

## reference
* https://hellosean1025.github.io/yapi/documents/index.html
* https://github.com/laravel/tinker
* markdown 解析 https://github.com/SegmentFault/HyperDown
