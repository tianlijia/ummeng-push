# ummeng-push
##安装方法
composer require tianlijia/ummeng-push

##使用方法

###引用
use UMeng\PushFactory\PushFactory;

###安卓
PushFactory::android(app_key,app_secret);
###IOS
PushFactory::ios(app_key,app_secret);
