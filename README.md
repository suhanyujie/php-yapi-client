# php-yapi-client
* 一个 [yapi](https://hellosean1025.github.io/yapi/index.html) 客户端，为了方便将本地编写的 markdown 文档解析上传到公司/组织的 yapi 文档管理中心
* 换句话说，将本地 markdown 文档保存到 yapi 服务上。

## instruction

### requirement
- PHP 7.2+
- _目前只支持 macOS、Linux 系统_

### 痛点
* 公司的文档服务器可能不是很靠谱，有可能丢失数据。你可以建立一个 git 仓库存放你的 markdown 文档。而 markdown 文档又可以直接通过 `yc` 命令提交到你公司的文档服务中。相当于多一个备份。
* yapi 后台编辑文档繁琐、效率不够高，如果直接在本地编辑 markdown 文档，然后通过命令行更新到 yapi 后台，此时效率大大提升。

## usage
### 下载 
- `git clone https://github.com/suhanyujie/php-yapi-client.git`
- `cd php-yapi-client`
- `composer install`

### 配置
- `cp env.example .env`
- 根据需要，在 `.env` 文件中修改 yapi 服务的地址以及文档 project 对应的 token

### 打包
- 打包成 phar `./build.php`
- `cp yc.phar /usr/local/bin/yc`

### 运行
- 1.按照要求写文档
    - 要使用该 yapi 客户端，需要必须按照合法的要求写 markdown，格式参考[此模板](docs/md_doc_template.md)。
- 2.使用 `yc` 命令提交文档。例如：`yc /path/businessName/loginDoc.md`。
    * 当然如果需要提交当前路径下的 `loginDoc.md` 文件，可以执行 `yc loginDoc.md`

## todo
- [x] 更新文档到组织的 yapi 服务中

## reference
* https://hellosean1025.github.io/yapi/documents/index.html
* https://github.com/laravel/tinker
* markdown 解析 https://github.com/SegmentFault/HyperDown
* https://github.com/composer/composer
* 命令行应用框架 https://github.com/inhere/php-console
