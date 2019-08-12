<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016100200645172",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAtqQB25mG0pn1n8IqAe13+0Q5B5mv+HyNkKQGiT4YPXWvjRRwl1cKcsV42d0LrcWt/N/jzuHxgm/aEvAhumhxWCEJcdkHfFZuvGhy2zvK595nwtD7efb9IA8DfEYXry9S3Ke8xK9UNeGHgC8gVxLB6t6l4SRKoQvtNaGKmHJimhYcdkoKo69WD12HYXghzUM6/XK0A380ipG+1Evl5Eo5pZoi+KDXE5mcJlQJWtSTkYbj7ImCBRU4XoDhjRL87DybRPRmhjoBMtsWvfg0kvvLJ7A2ZHYYhZuzJ6435FsEA+LzQPfzIVKjTeq4DBA3EsQW19aiN9hy+ahlYf9x2oENBwIDAQABAoIBADMebmKvctvpOaVQa/+EHqvWKXwmGOmcuua78hRhq9kv4kXDbcv+Ea+T88JOqyElDhCT/af+92DBu7DHQzlwWCEJhiI2U3EKpA3Z0iPodV82kaYmZex6I9jgOuKCn8hpn3ChBiWqyRAXopxPFGcqmdoKHTWJudWfh/IV17vZcB2eDyw4PYp+euhuBgYJHVNCTutX5moBt1nB5bp5EquKgGM/5E30OABGxYBPap7qbMnMGmGuQaGGalyrZ/7tdIZITgtelwIjmMzQRhokvvxw796BgVx60N6pmU82l+aQeG5KXQ01DT/CrQKTVBnnhpeIfbnSVlG08bVm11d5nJHo9/kCgYEA7HiYOpu211dcUXWKwYOxKUPNclSFYuGjslIPFuW/XVQN4/mi8M5jAO4uCG0eigF549SN1byWCFgxFnqcej93mGMJyTz3FKktmuHvK3cGhl5TROR9Qmregr3QYOVixXjf4eyez9k5V0LLEzA3Sa7VUmSMEchT4iRZNT22Dvl/oRUCgYEAxblYTObTALkGRTsV2COAws6qjKTbEX1B2Xq7WR/+RB1nPYFAnHLeEiAsNxMD/L9rfu5tJan7FDyMjLJlRKSUYg4NIw+V7D0uqhAbuKJ71K7+kqc6gnxXujTcqnkm2CnA6QB4MrhLXIW1+LgDSqjKs2Q0Im5+z1o/yK3wJjCTpKsCgYApf3xPhLYQkk8sKHGCRkcX08NBFh4WXTyp9YaaYRU1HqQVZuC2W7G6HxJK/kNGur2WQt3lLWQy8K8kn73IIk0tm+vCugUuRgYv0Qh02z105Sa9x09IEZMc5IymBHtR5kV701eHaDqM2rbiliqNrrXW8Lg4Aqzd2b0h/8NsW1KPhQKBgQCQ59r80RXGq9MancjOlIZjPs7jMFaUpLXDyxzOnpHcHahx/O37Onvh0M5WtorviuIfLmGzfrXSCOYAnyV0fyF4E1AxZ9S6Q8SVQiTu1bXnEzDjDcflpXctslweW5fKSB9nI77zfPlOH3hThZhz+OxFOKS0r0IkeMHSMxeTBXrO7QKBgQDSCYHJ0JrojFpNdIRm3+quZXfysHTfg2BBKQw2fCUGVd6hTELfXh8Rde4i+paa53XghdOXwIsVlm5+6Ati0FVPdoRpsJCI4K8zfZBSdhjr216EF87m3L+DTMhfkk83sSqcKtgrgGOTbxMbc1LWWDZWu5QC6ge2QFEyy8tnyxTOUw==",
		
		//异步通知地址
		'notify_url' => "http://www.pyg.com/home/order/notify",
		
		//同步跳转
		'return_url' => "http://www.pyg.com/home/order/callback",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6XsZFGWEoAoIBMXHy4ZRF5Y8Y9ViVKFWmrzN/F/cpe23ZQCbxZb03ahIOZ1IYgI7Qupep0N69OYM3n2wyix5XoNXdLeQzU49FN2+0HlkZGanpNlwa+Ms7mTv+1TaiC3xdFD1raQLOYaWG5baXJHGkft3TjTzm+Ypdjt1w7e2ughbvWXeKO9wxvQMpKjTCUoB0ZPDLF6FsXzSyAJL1fN/k+zFlR6Ylf9hKOMOvLqMx2/W5rQJkSecTRGKl6M1Kzy0EDjifVqYIxSc+yKMahFr0t5Ds0sa+zR1k3MlE2c3Xuyt/jvFKrSA5KebiolkHgfRmrklDHrau+59h0LZFFaNIwIDAQAB",
);