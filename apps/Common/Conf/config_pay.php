<?php
return array(


    'alipay_config'=>array(
        'partner' =>'2088911513954114',   //这里是你在成功申请支付宝接口后获取到的PID；
        'key'=>'num76mg7w88s1iywg5xpo4wrtv721vt5',//这里是你在成功申请支付宝接口后获取到的Key
        'sign_type'=>strtoupper('MD5'),
        'input_charset'=> strtolower('utf-8'),
        'cacert'=> getcwd().'\\cacert.pem',
        'transport'=> 'http',
    ),
    
    //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；
    
    'alipay'=>array(
        //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
        'seller_email'=>'biz@simpro.cn',
        //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
        'notify_url'=>'http://gczdw.sp11.cn/index.php/Index/Pay/notifyurl',
        //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
        'return_url'=>'http://gczdw.sp11.cn/index.php/Index/Pay/returnurl',
        //支付成功跳转到的页面，
        'successpage'=>'Cms/communist_fee_do_save',
        //支付失败跳转到的页面
        'errorpage'=>'Cms/communist_fee_do_save',
    ),
)
;