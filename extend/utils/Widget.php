<?php
/**
 * Desc: 上传文件记录 - 模型
 * User: 黎笔家
 * Date-Time: 2024/3/11/15:41
 */

namespace utils;

use basic\BaseLogic;
use exception\ThrottleException;
use think\facade\Filesystem;

class Widget
{
    /**
     * 验证上传文件类型
     * @param $type
     * @return array
     * @throws ThrottleException
     * User lee
     * Date 2023/8/14
     */
    private static function isFileType($type)
    {
        $tp = array();

        switch ($type) {
            case 'image':
                $tp = array(
                    "gif", "pjpeg", "jpeg", 'png', 'jpg', 'bmp', 'tiff',
                    "image/gif", "image/pjpeg", "image/jpeg", 'image/png', 'image/jpg', 'image/bmp', 'image/tiff',
                );    //检查上传文件是否在允许上传的类型
                $tp_msg = lang('image'); // 图片
                $tp_path = 'image';
                break;
            case 'file':
                // $tp = array("image/gif", "image/pjpeg", "image/jpeg", 'image/png');    //检查上传文件是否在允许上传的类型
                $tp_msg = lang('file'); // 文件
                $tp_path = 'file';
                break;
            case 'media':
                $tp = array(
                    'swf', 'flv', 'mp3', 'mp4', 'wav',
                    'wma', 'wmv', 'mid', 'avi', 'mpg',
                    'asf', 'rm', 'rmvb',
                    'video/swf', 'video/flv', 'video/mp3', 'video/mp4', 'video/wav',
                    'video/wma', 'video/wmv', 'video/mid', 'video/avi', 'video/mpg',
                    'video/asf', 'video/rm', 'video/rmvb'
                ); //检查上传文件是否在允许上传的类型
                $tp_msg = lang('media'); // 媒体
                $tp_path = 'media';
                break;
            default:
                // 上传失败，上传类型不在允许范围
                throw new ThrottleException(lang('illegalUploadType'));
        }

        return [$tp, $tp_msg, $tp_path];
    }

    /**
     * 上传附件
     * @param app('request') 获取上传的文件信息
     * @param $type 类型 image=>图片  file=>文件  media=>媒体
     * @param string $file_name 上传文件 name 名
     * @param int $max_size 最大上传  注：需要设置服务器支持上传大小
     * @return \think\response\Json
     * @throws ThrottleException
     * User lee
     * Date 2022/12/28
     */
    public static function upload($specify_the_first_dir = '', $max_size = 1024 * 20)
    {
        try {
            set_time_limit(0);// 持续运行到程序结束

            $type = app('request')->param('type/s');
            $file_name = app('request')->param('file_name/s');

            // 请检查文件是否上传，如确认无误，请联系管理员排查服务器上传配置
            if (!$type && !$file_name) throw new ThrottleException(lang('pleaseCheckIfTheFileHasBeenUploaded'));
            // 上传失败，缺少上传类型
            if (!$type) throw new ThrottleException(lang('missingUploadType'));
            // 上传失败，缺少上传文件name值名
            if (!$file_name) throw new ThrottleException(lang('missingUploadFileNameValueName'));

            // 验证上传文件类型
            [$tp, $tp_msg, $tp_path] = self::isFileType($type);

            $file = app('request')->file($file_name);
            $err = error_get_last();
            if ($err) throw new ThrottleException($err['message']);

            // 类型错误
            if (!empty($tp) && !in_array($file->getOriginalMime(), $tp)) throw new ThrottleException($tp_msg . lang('errorInType'));

            // 如果文件夹不存在，创建文件夹
            if (!file_exists(Filesystem::getDiskConfig('public', 'root')))
                mkdir(Filesystem::getDiskConfig('public', 'root'), 755); // 给予 755 权限

            $data['name'] = $file->getOriginalName();
            if (strlen($data['name']) >= 100) throw new ThrottleException('文件上传失败，文件名称长度最长支持100');

            $data['extension'] = $file->getOriginalExtension();
            $data['size'] = floatval(round($file->getSize() / 1024, 2));
            // 文件大小不能超过
//        if ($data['size'] > $max_size) throw new ThrottleException(lang('fileSizeCannotExceed').' '.$max_size.' kb');
            if ($data['size'] > $max_size) throw new ThrottleException(lang('fileSizeCannotExceed') . ' ' . floatval(round($max_size / 1024, 0)) . ' M');

            $save_name = Filesystem::disk('public')->putFile(($specify_the_first_dir ? $specify_the_first_dir . '/' : '') . $tp_path, $file);
            $data['file_path'] = Filesystem::getDiskConfig('public', 'url') . $save_name;
            $data['create_time'] = date('Y-m-d H:i:s');

            // todo::写入数据库


        } catch (\Throwable $e) {
            if ($e->getMessage() == 'upload File size exceeds the maximum value') throw new ThrottleException(lang('fileSizeCannotExceed') . ' ' . floatval(round($max_size / 1024, 0)) . ' M');

            throw new ThrottleException(lang('operationFail') . ': ' . $e->getLine() . ' ' . $e->getMessage());
        }

        return $data;
    }
}