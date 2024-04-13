<?php
namespace control;

use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\kali;
use kaliphp\util;
use kaliphp\lang;
use kaliphp\config;
use kaliphp\lib\cls_page;
use kaliphp\lib\cls_menu;
use kaliphp\lib\cls_msgbox;
use model\mod_user;
use model\mod_session;
use OpenApi\Logger;


/**
 * @OA\Server(
 *      url="{schema}://127.0.0.1:8080/",
 *      description="测试服",
 *      @OA\ServerVariable(
 *          serverVariable="schema",
 *          enum={"https", "http"},
 *          default="http"
 *      )
 * )
 */

/**
 * @OA\Server(
 *      url="{schema}://api.xxxx.com",
 *      description="OpenApi parameters",
 *      @OA\ServerVariable(
 *          serverVariable="schema",
 *          enum={"https", "http"},
 *          default="http"
 *      )
 * )
 */


/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     description="全局添加API Token鉴权",
 *     name="authorization",
 *     in="header",
 *     securityScheme="token"
 * )
 *
 */

/**
 * @OA\Schema(
 *     schema="response",
 *     response=200,
 *     required={"code", "msg", "data"},
 *     description="成功为0，失败为非0",
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         format="int32"
 *     ),
 *     @OA\Property(
 *         property="msg",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="object"
 *     ),
 * )
 */


// 创建swg文档
class ctl_swg
{
    /**
     * 创建swg文档
     * @return   void
     */
    public function create()
    {
        Logger::$debug_log = false;
        // 上线后要去掉旧项目的
        $path    = [APPPATH . '/control'];
        $openapi = \OpenApi\scan($path);
        $content = $openapi->toJson();
        
        if (defined(SWG_DIR) && is_dir(SWG_DIR)) 
        {
            file_put_contents(SWG_DIR . '/api.json', $content);
        }

        echo $content;
    }

    /**
     * [SWG]
     * @tags  分组名称
     * @title 这是标题，需要则写：获取我的列表
     * @desc  这是长描述，需要则写
     * @path   post /swg/demo
     * @param  integer  $page:1   页数  required=true 
     * @param  integer  $limit:10 每页个数
     * @return json     $data {"a":1, "b":"123", "c":[1, 2, 3]}
     * @return string   $data.a   名字
     * @return string   $data.b   年龄
     * [/SWG]
     */
    public function demo()
    {

    }
}