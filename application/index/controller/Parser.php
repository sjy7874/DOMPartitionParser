<?php
/**
 * Created by PhpStorm.
 * User: Albert Jang
 * Date: 2017/4/27
 * Time: 13:36
 */
namespace app\index\controller;

use app\index\Parser\ParserLog;
use app\index\Parser\ResumeParser;
use app\index\Irregular\ParseCommon;
use app\index\Irregular\ParserIrregularLog;
use think\Controller;

class Parser extends Controller {
    //简历解析
    public function resume() {
        header('Access-Control-Allow-Origin:*');
        $request = request();
        if($request->isPost()) {
            $originContent = $request->post('content');
            if(!$originContent)
                return json(array('status' => -2));
            //$type = $request->post('type');
            $Parser = new ResumeParser();
            $content = $Parser->convert2UTF8($originContent);
            $data = $Parser->parse($content, $templateId);
            if($data){
                $info = array(
                    'template' => $templateId,
                    'data' => $data,
                    'status' => 1,
                );
            }else{
                ParserLog::toSupport($originContent);
                //通用解析
                $Parser = new ParseCommon();
                $data = $Parser->parse($content);
                if($data){
                    $info = array(
                        'data' => $data,
                        'status' => 2,
                    );
                }else{
                    //存储没有解析出来的简历文档（由于简历名字提取原因暂时不用）
                    ParserIrregularLog::toSupportIrregular($originContent);
                    $info = array(
                        'status' => 0,
                    );
                }
            }

        }else{
            $info = array(
                'status' => -1,
            );
        }
        return json($info);
    }
}
