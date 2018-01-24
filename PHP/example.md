# 开发说明文档
============

## 概述
为了方便稳速的小伙伴们了解和使用研发部门所开发的产品，对研发部门发布的系统更新做了梳理。
所有代码和文档均已上传至内部[SVN服务器: https://svn.765.com.cn/svn/SMC/carl](https://svn.765.com.cn/svn/SMC/carl)
产品的安装使用和功能介绍请查看目录下的`README.md`文件

## 操作指引
+ [环境依赖] 软件或脚本依赖的执行环境，通常在文档中提供了安装方法
+ [代码目录] 说明各文件和目录的功能，安装覆盖示例 `cp -rf source-dir/* /path/to/destination/`
+ [数据结构] 导入sql `use [db]; source /path/to/xxx.sql` 或 `mysql -u [user] -D [db] -p < path/to/xxx.sql`

## 系统开发一览
| 系统名称 | svn目录 | 类型 | 最新发布 | 备注 |
| ---- | ---- | :--: | :--: | ---- |
| 稳速BBS         | bbs-discuz        | 二次开发  | 2017.12.07 | 接入Cas单点登录 |
| cacti监控       | cacti             | 二次开发  | 2017.12.18 | 接入Cas单点登录 |
| 资产            | cmdb-rackTables   | 二次开发  | 2017.12.19 | 特殊网段获取网关处理 |
| 工单 v4         | otrs-4            | 二次开发  | 2017.11.17 | 从资产系统创建工单 |
| 工单 v5         | otrs-5            | 二次开发  | 2017.12.23 | 接入Cas单点登录 |
| zabbix监控 v3.2 | zabbix-3.2        | 二次开发  | 2017.12.29 | 接入Cas单点登录 |
| zabbix监控 v3.4 | zabbix-3.4        | 二次开发  | 2017.12.29 | 接入Cas单点登录 |
| cas账号         | cas-user          | 自研      | 2017.12.27 | 账号统一管理 |
| 其他            | 其他脚本和小程序    | 自研      | 2017.12.22 | 监控发送工单脚本 |

## 帮助支持
有任何疑问或bug请随时联系研发部门.
+ [@Carl 王朝辉](mailto:wangzhaohui@765.com.cn)
