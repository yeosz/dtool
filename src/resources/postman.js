
function randomValue(arr)
{
    return arr[Math.floor(Math.random()*arr.length)];
}

function getString(len)
{
    var chars =  ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
    var str = '';
    for (var i=0; i<len; i++)
    {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}

function getMobile()
{
    var prefix = ['13','15','17','18'];
    var num = Math.random() * 1000000000;
    num = num < 100000000 ? '0' + String(Math.ceil(num)) : String(Math.ceil(num));
    return prefix[Math.floor(Math.random() * prefix.length)] + num;
}

function getEmail()
{
    var suffix = ['@qq.com','@126.com','@163.com','@sina.com','@yahoo.com','@gmail.com','@hotmail.com'];
    var chars = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9'];
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
    var prefix = ['李','王','张','刘','陈','杨','黄','赵','吴','周','徐','孙','马','朱','胡','郭','何','高','林','罗','郑','梁','谢','宋','唐','许','韩','冯','邓','曹','彭','曾','萧','田','董','潘','袁','于','蒋','蔡','余','杜','叶','程','苏','魏','吕','丁','任','沈','姚','卢','姜','崔','钟','谭','陆','汪','范','金','石','廖','贾','夏','韦','傅','方','白','邹','孟','熊','秦','邱','江','尹','薛','阎','段','雷','侯','龙','史','陶','黎','贺','顾','毛','郝','龚','邵','万','钱','严','覃','河','戴','莫','孔','向','汤'];
    var str = prefix[Math.floor(Math.random()*prefix.length)];
    for (var i=0; i<2; i++)
    {
        var num = 19968 + Math.floor(Math.random() * 6080);
        str += String.fromCharCode(num);
    }
    return str;
}