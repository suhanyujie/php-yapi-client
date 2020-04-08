<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-07
 * Time: 17:05
 */

namespace App\Libs;

use App\Traits\Http as HttpTrait;

class Request
{
    use HttpTrait;

    /**
     * @desc 发送 post 请求
     * @param array $params
     * @return array
     */
    public function post($params = [])
    {
        $options = [
            'url'    => '',
            'header' => [],
            'body'   => '',
            'debug'  => '',
        ];
        $options = array_merge($options, $params);
        $result = $this->httpRequest([
            'url'    => $options['url'],
            'method' => 'post',//
            'header' => $options['header'],
            'body'   => $options['body'],
        ]);

        return $result;
    }

    /**
     * @desc 发送 post 请求
     * @param array $params
     * @return array
     */
    public function get($params = [])
    {
        $options = [
            'url'    => '',
            'header' => [],
            'data'   => '',
            'debug'  => '',
        ];
        $options = array_merge($options, $params);
        if (!empty($options['data'])) {
            $pathPart = http_build_query($options['data']);
            $options['url'] = rtrim($options['url'], '/').'?'.$pathPart;
        }
        $result = $this->httpRequest([
            'url'    => $options['url'],
            'method' => 'GET',
            'header' => $options['header'],
        ]);

        return $result;
    }
}
