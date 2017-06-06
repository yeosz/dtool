
function randomValue(arr)
{
    return arr[Math.floor(Math.random()*arr.length)];
}

function getString(len)
{
    var chars = 'abcdefghijklmnopqrstuvwxyz'.split('');
    var str = '';
    for (var i=0; i<len; i++)
    {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}

function getMobile()
{
    var prefix = ['13', '15', '17', '18'];
    var num = Math.random() * 1000000000;
    num = num < 100000000 ? '0' + String(Math.ceil(num)) : String(Math.ceil(num));
    return prefix[Math.floor(Math.random() * prefix.length)] + num;
}

function getEmail()
{
    var suffix = ['@qq.com', '@126.com', '@163.com', '@sina.com', '@yahoo.com', '@gmail.com', '@hotmail.com'];
    var chars = 'abcdefghijklmnopqrstuvwxyz0123456789'.split('');
    var str = '';
    for (var i=0; i<8; i++)
    {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str + suffix[Math.floor(Math.random()*suffix.length)];
}

function getLocalString(len)
{
    var str = '';
    for (var i=0; i<len; i++)
    {
        var num = 19968 + Math.floor(Math.random() * 6080);
        str += String.fromCharCode(num);
    }    
    return str;
}

function getName()
{
    var prefix = '李王张刘陈杨黄赵吴周徐孙马朱胡郭何高林罗郑梁谢宋唐许韩冯邓曹彭曾萧田董潘袁于蒋蔡余杜叶程苏魏吕丁任沈姚卢姜崔钟谭陆汪范金石廖贾夏韦傅方白邹孟熊秦邱江尹薛阎段雷侯龙史陶黎贺顾毛郝龚邵万钱严覃河戴莫孔向汤'.split('');
    var str = prefix[Math.floor(Math.random()*prefix.length)];
    for (var i=0; i<2; i++)
    {
        var num = 19968 + Math.floor(Math.random() * 6080);
        str += String.fromCharCode(num);
    }    
    return str;
}