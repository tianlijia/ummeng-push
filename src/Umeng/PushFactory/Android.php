<?php
/**
 * Created by xjliu.
 * User: xjliu@snqu.com
 * Date: 2017/8/22 12:10
 */

namespace UMeng\PushFactory;

use Illuminate\Support\Facades\Log;
use UMeng\Android\AndroidBroadcast;
use UMeng\Android\AndroidCustomizedCast;
use UMeng\Android\AndroidFileCast;
use UMeng\Android\AndroidGroupCast;
use UMeng\Android\AndroidUniCast;

class Android implements PushInterface
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

    public function sendBroadCast($ticker, $title, $text, $afterOpen, array $customs = [], $isFormal = true)
    {
        try {
            $broCast = new AndroidBroadcast();
            $broCast->setAppMasterSecret($this->appMasterSecret);
            $broCast->setPredefinedKeyValue("appkey", $this->appKey);
            $broCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $broCast->setPredefinedKeyValue("ticker", $ticker);
            $broCast->setPredefinedKeyValue("title", $title);
            $broCast->setPredefinedKeyValue("text", $text);
            $broCast->setPredefinedKeyValue("after_open", $afterOpen);
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $broCast->setPredefinedKeyValue("production_mode", $isFormal);
            // [optional]Set extra fields
            foreach ($customs as $key => $value) {
                $broCast->setExtraField($key, $value);
            }
            Log::info("Sending broadcast notification, please wait...\r\n");
            $broCast->send();
            Log::info("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            Log::info("Caught exception: " . $e->getMessage());
        }
    }

    /**
     * 安卓端推送
     * @param $assOrDoctorId
     * @param $content
     * @param $type
     * @param $ylist
     * @throws \Exception
     */
    public function sendUniCast($assOrDoctorId, $content, $type, $ylist)
    {
        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appKey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", "");
            $unicast->setPredefinedKeyValue("type", 'customizedcast');
            $unicast->setPredefinedKeyValue("alias_type", 'normal');
            $unicast->setPredefinedKeyValue("alias", $assOrDoctorId);

            $unicast->setPredefinedKeyValue("ticker", '1');
            $unicast->setPredefinedKeyValue("title", $content);
            $unicast->setPredefinedKeyValue("text", "");
            $unicast->setPredefinedKeyValue("display_type", "notification");
            $unicast->setPredefinedKeyValue("after_open", "go_custom");
            $unicast->setPredefinedKeyValue("custom", "");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");

            $unicast->setPredefinedKeyValue("mipush", true);
            $unicast->setPredefinedKeyValue("mi_activity", 'com.dayi.patient.MfrMessageActivity');

            // Set expire time like redis cache. In case send times repeat.
            $unicast->setPredefinedKeyValue("policy", [
                "expire_time" => date("Y-m-d H:i:s", (24 * 60 * 60 + time()))
            ]);
            // from xiaomi, oppo, huawei, meizu channel etc.
            $unicast->setPredefinedKeyValue("channel_properties", [
                "channel_activity" => "com.dayi.patient.MfrMessageActivity",
                "mi_channel_id" => "XLYS_DOCTOR_TXVIDEO",
                "vivo_classification" => "1",
                "oppo_channel_id" => "XLYS_DOCTOR_TXVIDEO",
                "main_activity" => "com.dayi.patient.ui.home.HomeActivity",
            ]);

            // Set extra fields
            $unicast->setExtraField("param", $ylist);
            $unicast->setExtraField("type", $type);

            $unicast->send();

        } catch (Exception $e) {
            Log::info("Caught exception: " . $e->getMessage());
        }
    }

    public function sendFileCast($ticker, $title, $text, $afterOpen)
    {
        try {
            $fileCast = new AndroidFileCast();
            $fileCast->setAppMasterSecret($this->appMasterSecret);
            $fileCast->setPredefinedKeyValue("appkey", $this->appKey);
            $fileCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $fileCast->setPredefinedKeyValue("ticker", $ticker);
            $fileCast->setPredefinedKeyValue("title", $title);
            $fileCast->setPredefinedKeyValue("text", $text);
            $fileCast->setPredefinedKeyValue("after_open", $afterOpen);  //go to app
            Log::info("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $fileCast->uploadContents("aa" . "\n" . "bb");
            Log::info("Sending filecast notification, please wait...\r\n");
            $fileCast->send();
            Log::info("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            Log::info("Caught exception: " . $e->getMessage());
        }
    }

    public function sendGroupCast($filter, $ticker, $title, $text, $afterOpen, $isFormal = true)
    {
        try {
            $groupCast = new AndroidGroupCast();
            $groupCast->setAppMasterSecret($this->appMasterSecret);
            $groupCast->setPredefinedKeyValue("appkey", $this->appKey);
            $groupCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupCast->setPredefinedKeyValue("filter", $filter);
            $groupCast->setPredefinedKeyValue("ticker", $ticker);
            $groupCast->setPredefinedKeyValue("title", $title);
            $groupCast->setPredefinedKeyValue("text", $text);
            $groupCast->setPredefinedKeyValue("after_open", $afterOpen);
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $groupCast->setPredefinedKeyValue("production_mode", $isFormal);
            Log::info("Sending groupcast notification, please wait...\r\n");
            $groupCast->send();
            Log::info("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            Log::info("Caught exception: " . $e->getMessage());
        }
    }

    public function sendCustomizedCast($alias, $aliasType, $ticker, $title, $text, $afterOpen)
    {
        try {
            $customizedCast = new AndroidCustomizedCast();
            $customizedCast->setAppMasterSecret($this->appMasterSecret);
            $customizedCast->setPredefinedKeyValue("appkey", $this->appKey);
            $customizedCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedCast->setPredefinedKeyValue("alias", $alias);
            // Set your alias_type here
            $customizedCast->setPredefinedKeyValue("alias_type", $aliasType);
            $customizedCast->setPredefinedKeyValue("ticker", $ticker);
            $customizedCast->setPredefinedKeyValue("title", $title);
            $customizedCast->setPredefinedKeyValue("text", $text);
            $customizedCast->setPredefinedKeyValue("after_open", $afterOpen);
            Log::info("Sending customizedcast notification, please wait...\r\n");
            $customizedCast->send();
            Log::info("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            Log::info("Caught exception: " . $e->getMessage());
        }
    }

    public function sendCustomizedCastFileId($uploadContent, $aliasType, $ticker, $title, $test, $afterOpen)
    {
        try {
            $customizedCast = new AndroidCustomizedCast();
            $customizedCast->setAppMasterSecret($this->appMasterSecret);
            $customizedCast->setPredefinedKeyValue("appkey", $this->appKey);
            $customizedCast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedCast->uploadContents($uploadContent);
            // Set your alias_type here
            $customizedCast->setPredefinedKeyValue("alias_type", $aliasType);
            $customizedCast->setPredefinedKeyValue("ticker", $ticker);
            $customizedCast->setPredefinedKeyValue("title", $title);
            $customizedCast->setPredefinedKeyValue("text", $test);
            $customizedCast->setPredefinedKeyValue("after_open", $afterOpen);
            Log::info("Sending customizedcast notification, please wait...\r\n");
            $customizedCast->send();
            Log::info("Sent SUCCESS\r\n");
        } catch (\Exception $e) {
            Log::info("Caught exception: " . $e->getMessage());
        }
    }
}
