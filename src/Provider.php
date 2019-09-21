<?php

namespace Yeosz\Dtool;

/**
 * Class Provider
 *
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string qq
 * @property string mobile
 * @property string phone
 * @property string postcode
 * @property string image_url
 * @property string bitmap_url
 * @property string company_name
 * @property string id_card
 * @property string city
 * @property string address
 * @property string datetime
 * @property string timestamp
 * @property string year
 * @property string date
 * @property string time
 * @property int tinyint
 * @property int smallint
 * @property int mediumint
 * @property int bigint
 * @property int int
 * @property int integer
 * @property string uuid
 * @property string ip
 * @property string ean8
 * @property string ean13
 * @property string payment
 * @property string bank
 * @property string color_name
 * @property string color_hex
 * @property string color_rgb
 * @property string version
 * @property string country
 * @property string university
 */
class Provider
{
    /**
     * 资源对应
     */
    const RESOURCES = [
        'chinese_characters' => 'chinese.characters.csv',
        'company' => 'company.csv',
        'first_name' => 'first.name.csv',
        'last_name' => 'last.name.csv',
        'area' => 'area.json',
        'payment' => 'payment.csv',
        'bank' => 'bank.csv',
        'color' => 'color.json',
        'country' => 'country.csv',
        'university' => 'university.csv',
    ];

    /**
     * 属性
     */
    private $property = [
        'name' => 'getName',
        'first_name' => 'getFirstName',
        'last_name' => 'getLastName',
        'email' => 'getEmail',
        'qq' => 'getQq',
        'mobile' => 'getMobile',
        'phone' => 'getPhone',
        'postcode' => 'getPostCode',
        'image_url' => 'getImageUrl',
        'bitmap_url' => 'getBitmapUrl',
        'company_name' => 'getCompanyName',
        'id_card' => 'getIdCard',
        'address' => 'getAddress',
        'city' => 'getCity',
        'uuid' => 'getUuid',
        'ip' => 'getIp',
        'ean8' => 'getEan8',
        'ean13' => 'getEan13',
        'payment' => 'getPayment',
        'bank' => 'getBank',
        'tinyint' => 'Number::randomTinyint',
        'smallint' => 'Number::randomSmallint',
        'mediumint' => 'Number::randomMediumint',
        'bigint' => 'Number::randomBigint',
        'int' => 'Number::randomInt',
        'integer' => 'Number::randomInt',
        'datetime' => 'Datetime::datetime',
        'timestamp' => 'Datetime::datetime',
        'year' => 'Datetime::year',
        'date' => 'Datetime::date',
        'time' => 'Datetime::time',
        'color_name' => 'getColorName',
        'color_hex' => 'getColorHex',
        'color_rgb' => 'getColorRgb',
        'version' => 'getVersion',
        'country' => 'getCountry',
        'university' => 'getUniversity',
    ];

    /**
     * 资源
     * @var array
     */
    private $resources = [];

    /**
     * 增长类属性
     * @var \stdClass
     */
    private $increments;

    /**
     * @var \stdClass
     */
    private $providers;

    /**
     * @var Number
     */
    public $numberProvider;

    /**
     * @var Datetime
     */
    public $datetimeProvider;

    /**
     * Provider constructor.
     */
    public function __construct()
    {
        $this->increments = new \stdClass();
        $this->providers = new \stdClass();
        $this->numberProvider = new Number();
        $this->datetimeProvider = new Datetime();
    }

    /**
     * 获取资源
     *
     * @param $key
     * @return array
     */
    public function getResource($key)
    {
        if (isset($this->resources[$key])) {
            return $this->resources[$key];
        }

        $path = __DIR__ . '/resources/' . self::RESOURCES[$key];
        if (pathinfo($path, PATHINFO_EXTENSION) == 'json') {
            $result = json_decode(file_get_contents($path), true);
        } else {
            $content = file($path);
            $split = function ($value) {
                return explode(',', trim($value));
            };
            if (count($content) == 1) {
                $result = $split($content[0]);
            } else {
                $result = array_map($split, $content);
            }
        }

        return $this->resources[$key] = $result;
    }

    /**
     * 随机字符串
     *
     * @param int $length 长度
     * @param int $type 0大小写,1小写,2大写
     * @return string
     */
    public function getString($length = 8, $type = 0)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $chars .= strtoupper($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars{rand(0, 51)};
        }
        return $type == 0 ? $string : ($type == 1 ? strtolower($string) : strtoupper($string));
    }

    /**
     * 随机中文字符串
     *
     * @param int $length 长度
     * @return string
     */
    public function getMbString($length = 8)
    {
        $chars = $this->getResource('chinese_characters');
        $count = count($chars) - 1;
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, $count)];
        }
        return $string;
    }

    /**
     * 随机邮箱
     *
     * @return string
     */
    public function getEmail()
    {
        $suffix = ['@qq.com', '@126.com', '@163.com', '@sina.com', '@yahoo.com', '@gmail.com', '@hotmail.com'];
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = rand(6, 10);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars{rand(0, 35)};
        }
        return $string . $suffix[rand(0, 6)];
    }

    /**
     * 获取qq
     *
     * @return string
     */
    public function getQq()
    {
        $qq = strval(mt_rand(1, 9));
        $length = rand(4, 8);
        for ($i = 0; $i < $length; $i++) {
            $qq .= rand(0, 9);
        }
        return $qq;
    }

    /**
     * 随机手机号码
     *
     * @return string
     */
    public function getMobile()
    {
        $suffix = ['13', '15', '17', '18'];
        return $suffix[array_rand($suffix)] . rand(100000000, 999999999);
    }

    /**
     * 随机电话号码
     *
     * @return string
     */
    public function getPhone()
    {
        return '0' . rand(20, 999) . '-' . rand(10000000, 99999999);
    }

    /**
     * 随机身份证号码
     *
     * @return string
     */
    public function getIdCard()
    {
        // 区域code
        $area = $this->getResource('area');
        $code = [];
        foreach ($area as $region) {
            if (substr($region['id'], -2) != '00') {
                $code[] = $region['id'];
            }
        }

        // 生日
        $time = time() - 86400 * rand(1, 20800);
        $date = date('Ymd', $time);

        $idCard = $this->randomValue($code) . $date . rand(1, 9) . rand(1, 9) . rand(1, 9);

        // 检验位
        $idCard = str_split($idCard);
        $weight = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $sum = [];
        foreach ($idCard as $key => $value) {
            $sum[] = $value * $weight[$key];
        }
        $lastIndex = array_sum($sum) % 11;
        $last = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];

        return implode('', $idCard) . $last[$lastIndex];
    }

    /**
     * 随机邮政编码
     *
     * @return string
     */
    public static function getPostcode()
    {
        return rand(1, 9) . rand(1000, 9999) . '0';
    }

    /**
     * 数据随机值
     *
     * @param $arr
     * @return mixed
     */
    public static function randomValue($arr)
    {
        return $arr[array_rand($arr)];
    }

    /**
     * 获取随机图片
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getImageUrl($width = 200, $height = 200)
    {
        return "http://lorempixel.com/{$width}/{$height}/";
    }

    /**
     * 获取占位图
     *
     * @param int $width
     * @param int $height
     * @param string $txt
     * @return string
     */
    public function getBitmapUrl($width = 200, $height = 200, $txt = 'image')
    {
        $txtSize = $txt ? floor($width / strlen($txt)) : 25;
        return "https://placeholdit.imgix.net/~text?txtsize={$txtSize}&txt={$txt}&w={$width}&h={$height}";
    }

    /**
     * 获取公司名称
     *
     * @return string
     */
    public function getCompanyName()
    {
        $resource = $this->getResource('company');
        return $this->randomValue($resource[0]) . $this->randomValue($resource[1]);
    }

    /**
     * 获取姓
     *
     * @return string
     */
    public function getLastName()
    {
        $resource = $this->getResource('last_name');
        return $this->randomValue($resource);
    }

    /**
     * 获取名
     *
     * @param int $gender 0随机,1男,2女
     * @return string
     */
    public function getFirstName($gender = 0)
    {
        $resource = $this->getResource('first_name');
        if ($gender == 1) {
            $resource = array_merge($resource[0], $resource[1]);
        } elseif ($gender == 2) {
            $resource = array_merge($resource[2], $resource[3]);
        } else {
            $resource = array_merge($resource[0], $resource[1], $resource[2], $resource[3]);
        }
        return $this->randomValue($resource);
    }

    /**
     * 获取姓名
     *
     * @param int $gender 0随机,1男,2女
     * @return string
     */
    public function getName($gender = 0)
    {
        return $this->getLastName() . $this->getFirstName($gender);
    }

    /**
     * 地址
     *
     * @return string
     */
    public function getAddress()
    {
        // 区域code
        $areas = $this->getResource('area');
        $regions = [];
        $ids = [];
        foreach ($areas as $area) {
            $regions[$area['id']] = $area['name'];
            if (substr($area['id'], -2) != '00') {
                $ids[$area['id']] = $area['id'];
            }
        }
        $id = $this->randomValue($ids);
        $code = [
            substr($id, 0, 2) . '0000',
            substr($id, 0, 4) . '00',
            $id,
        ];

        $getName = function ($id) use ($regions) {
            return in_array($regions[$id], ['县', '市辖区']) ? '' : $regions[$id];
        };
        $address = $getName($code[0]) . $getName($code[1]) . $getName($code[2]);
        return $address;
    }

    /**
     * 地址
     *
     * @return string
     */
    public function getCity()
    {
        // 区域code
        $areas = $this->getResource('area');
        $regions = [];
        $ids = [];
        foreach ($areas as $area) {
            $regions[$area['id']] = $area['name'];
            if (substr($area['id'], -2) == '00' && substr($area['id'], -4) != '0000') {
                $ids[$area['id']] = $area['id'];
            } elseif (in_array($area['id'], [110000, 120000, 310000, 500000,])) {
                $ids[$area['id']] = $area['id'];
            }
        }
        $ids = array_diff($ids, [110100, 120100, 139000, 419000, 429000, 469000, 500100, 500200, 659000]);
        $id = $this->randomValue($ids);
        return $regions[$id];
    }

    /**
     * 增加供给器
     *
     * @param $key
     * @param $callback
     */
    public function addProvider($key, $callback)
    {
        if (isset($this->property[$key])) {
            trigger_error('属性' . $key . '已经存在', E_USER_ERROR);
        }
        $this->providers->$key = $callback;
    }

    /**
     * 初始化自增的供给器
     *
     * @param $key
     * @param int $start
     */
    public function addIncrement($key, $start = 0)
    {
        if (isset($this->property[$key])) {
            trigger_error('属性' . $key . '已经存在', E_USER_ERROR);
        }
        $this->increments->$key = $start;
    }

    /**
     * uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return md5(uniqid() . '-' . getmypid() . '-' . rand(111111111, 999999999));
    }

    /**
     * ip
     *
     * @return string
     */
    public function getIp()
    {
        $ipLong = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
        );
        $key = mt_rand(0, 9);
        $ip = long2ip(mt_rand($ipLong[$key][0], $ipLong[$key][1]));
        return $ip;
    }

    /**
     * 获取定长数字
     *
     * @param int $length
     * @return string
     */
    public function getNumber($length = 5)
    {
        $number = mt_rand(0, 9);
        for ($i = 1; $i < $length; $i++) {
            $number .= mt_rand(0, 9);
        }
        return $number;
    }

    /**
     * 获取ean8
     *
     * @return string
     */
    public function getEan8()
    {
        $countryCode = [690, 691, 692, 693, 694, 695];
        $barcode = $this->randomValue($countryCode) . $this->getNumber(4);
        $barcode .= $this->getLastEan($barcode);
        return $barcode;
    }

    /**
     * 获取ean13
     *
     * @return string
     */
    public function getEan13()
    {
        $countryCode = [690, 691, 692, 693, 694, 695];
        $barcode = $this->randomValue($countryCode) . $this->getNumber(9);
        $barcode .= $this->getLastEan($barcode);
        return $barcode;
    }

    /**
     * 获取ean校验位
     *
     * @param $ean
     * @return string
     */
    public function getLastEan($ean)
    {
        $length = strlen($ean);
        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $value = intval($ean{$i});
            if ($i % 2 == 0) {
                $sum += $sum;
            } else {
                $sum += $value * 3;
            }
        }
        return strval((10 - $sum % 10) % 10);
    }

    /**
     * 支付方式
     *
     * @return string
     */
    public function getPayment()
    {
        $resource = $this->getResource('payment');
        return $this->randomValue($resource);
    }

    /**
     * 银行
     *
     * @return string
     */
    public function getBank()
    {
        $resource = $this->getResource('bank');
        return $this->randomValue($resource);
    }

    /**
     * 下划线转驼峰
     *
     * @param string $string
     * @param bool $first
     * @return mixed
     */
    public static function toHump($string, $first = false)
    {
        $string = preg_replace_callback(
            '/([-_]+([a-z]{1}))/i',
            function ($matches) {
                return strtoupper($matches[2]);
            },
            $string
        );
        return $first ? ucfirst($string) : $string;
    }

    /**
     * 驼峰转下划线
     *
     * @param string $string
     * @return mixed
     */
    public static function toUnderline($string)
    {
        $string = preg_replace_callback(
            '/([A-Z]{1})/',
            function ($matches) {
                return '_' . strtolower($matches[0]);
            },
            $string);
        return $string;
    }

    /**
     * 魔术方法
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->property[$name])) {
            $method = $this->property[$name];
            if (stripos($method, '::')) {
                return call_user_func(__NAMESPACE__ . '\\' . $method);
            } else {
                return $this->$method();
            }
        } else if (isset($this->increments->$name)) {
            return $this->increments->$name++;
        } else if (isset($this->providers->$name)) {
            return call_user_func_array($this->providers->$name, []);
        }
        return null;
    }

    /**
     * 颜色名称
     *
     * @return string
     */
    public function getColorName()
    {
        $resource = $this->getResource('color');
        $color = $this->randomValue($resource);
        return $color[0];
    }

    /**
     * hex颜色
     *
     * @return string
     */
    public function getColorHex()
    {
        $resource = $this->getResource('color');
        $color = $this->randomValue($resource);
        return $color[1];
    }

    /**
     * rgb颜色
     *
     * @return string
     */
    public function getColorRgb()
    {
        $resource = $this->getResource('color');
        $color = $this->randomValue($resource);
        return $color[2];
    }

    /**
     * 版本号
     *
     * @return string
     */
    public function getVersion()
    {
        return mt_rand(0, 9) . '.' . mt_rand(0, 20) . '.' . mt_rand(0, 20);
    }

    /**
     * 国家
     *
     * @return string
     */
    public function getCountry()
    {
        $resource = $this->getResource('country');
        return $this->randomValue($resource);
    }

    /**
     * 大学
     *
     * @return string
     */
    public function getUniversity()
    {
        $resource = $this->getResource('university');
        return $this->randomValue($resource);
    }

    /**
     * 区间值
     *
     * @param int $start 开始值
     * @param int $end 结束值
     * @param int $divisor 除数
     * @return float|int
     */
    public function between($start = 0, $end = 2147483647, $divisor = 0)
    {
        $number = mt_rand($start, $end);
        if ($divisor) {
            $number = $number / $divisor;
        }
        return $number;
    }
}