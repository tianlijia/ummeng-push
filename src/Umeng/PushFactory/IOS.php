<?php
/**
 * Created by xjliu.
 * User: xjliu@snqu.com
 * Date: 2017/8/22 12:10
 */

namespace UMeng\PushFactory;


use UMeng\Ios\IOSBroadCast;
use UMeng\Ios\IOSCustomizedCast;
use UMeng\Ios\IOSFileCast;
use UMeng\Ios\IOSGroupCast;
use UMeng\Ios\IOSUniCast;
use Illuminate\Support\Facades\Log;

class IOS implements PushInterface
{
    protected $appKey = null;
    protected $appMasterSecret = null;
    protected $timestamp = null;

    function __construct($key, $secret)
    {
        $this->appKey = $key;
        $this->appMasterSecret = $secret;
        $this->timestamp = strval(time());
    }

    /**
     * ios 广播消息
     * @param string $alert IOS必须字段
     * @param array $customs 自定义字段 , d b 不能用于字段名
     * @param bool $isFormal
     */
    function sendBroadCast($alert, $customs = [], $isFormal = true)
    {
        try {
            $broCast = new IOSBroadCast();
            $broCast->setAppMasterSecret($this->appMasterSecret);
            $broCast->setPredefinedKeyValue("appkey", $this->appKey);
            $broCast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $broCast->setPredefinedKeyValue("alert", $alert);
            $broCast->setPredefinedKeyValue("badge", 0);
            $broCast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $broCast->setPredefinedKeyValue("production_mode", $isFormal);
            // Set customized fields
            foreach ( $customs as $key => $value ){
                $broCast->setCustomizedField($key, $value);
            }
            print("Sending broadcast notification, please wait...\r\n");
            $broCast->send();
            print("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    /**
     *
     * @param string $deviceTokens 设备唯一标识 ios为64位
     * @param string $alert
     * @param array $customs 自定义字段
     * @param boolean $isFormal
     */
    function sendUniCast($docid,$content,$type,$ylist)
    {
        try {
            $unicast = new IOSUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey",           $this->appKey);
            $unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens",    "");
            $unicast->setPredefinedKeyValue("type", 'customizedcast');
            $unicast->setPredefinedKeyValue("alias_type", 'normal');
            $unicast->setPredefinedKeyValue("alias", $docid);

            $unicast->setPredefinedKeyValue("alert", $content);
            $unicast->setPredefinedKeyValue("badge", 0);
            $unicast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $unicast->setPredefinedKeyValue("production_mode", config('env.IOS_PRODUCTION_MODE', 'false'));
            // Set customized fields
            $unicast->setCustomizedField("param", $ylist);
            $unicast->setCustomizedField("type", $type);

            //print("Sending unicast notification, please wait...\r\n");
            Log::debug('Sending unicast notification, please wait...');
            $unicast->send();
            //print("Sent SUCCESS\r\n");
            \Log::debug('Sent SUCCESS');
        } catch (Exception $e) {
            //print("Caught exception: " . $e->getMessage());
            \Log::fatal("Caught exception: " . $e->getMessage());
        }
    }

    /**
     * @param string $alert
     * @param boolean $isFormal
     * @param string $uploadContent
     */
    function sendFileCast($alert, $uploadContent, $isFormal = true)
    {
        try {
            $fileCast = new IOSFileCast();
            $fileCast->setAppMasterSecret($this->appMasterSecret);
            $fileCast->setPredefinedKeyValue("appkey", $this->appKey);
            $fileCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $fileCast->setPredefinedKeyValue("alert", $alert);
            $fileCast->setPredefinedKeyValue("badge", 0);
            $fileCast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $fileCast->setPredefinedKeyValue("production_mode", $isFormal);
            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $fileCast->uploadContents($uploadContent);
            print("Sending filecast notification, please wait...\r\n");
            $fileCast->send();
            print("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendGroupCast(array $filter, $alert, $isFormal = true)
    {
        try {
            $groupCast = new IOSGroupCast();
            $groupCast->setAppMasterSecret($this->appMasterSecret);
            $groupCast->setPredefinedKeyValue("appkey", $this->appKey);
            $groupCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupCast->setPredefinedKeyValue("filter", $filter);
            $groupCast->setPredefinedKeyValue("alert", $alert);
            $groupCast->setPredefinedKeyValue("badge", 0);
            $groupCast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $groupCast->setPredefinedKeyValue("production_mode", $isFormal);
            print("Sending groupcast notification, please wait...\r\n");
            $groupCast->send();
            print("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendCustomizedCast($alias, $aliasType, $alert, $isFormal = true)
    {
        try {
            $customizedCast = new IOSCustomizedCast();
            $customizedCast->setAppMasterSecret($this->appMasterSecret);
            $customizedCast->setPredefinedKeyValue("appkey", $this->appKey);
            $customizedCast->setPredefinedKeyValue("timestamp", $this->timestamp);

            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedCast->setPredefinedKeyValue("alias", $alias);
            // Set your alias_type here
            $customizedCast->setPredefinedKeyValue("alias_type", $aliasType);
            $customizedCast->setPredefinedKeyValue("alert", $alert);
            $customizedCast->setPredefinedKeyValue("badge", 0);
            $customizedCast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedCast->setPredefinedKeyValue("production_mode", $isFormal);
            print("Sending customizedcast notification, please wait...\r\n");
            $customizedCast->send();
            print("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
}