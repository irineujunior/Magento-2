<?php
namespace MSP\Shield\Test\Unit\Model;

use MSP\Shield\Api\DetectorInterface;
use MSP\Shield\Api\ScanResultInterface;

class IpsTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testMySQLInjectionAttackPatterns()
    {
        $detector = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('MSP\Shield\Model\Ips');

        $fieldName = 'somefield';
        $tests = explode("\n", <<< __EOF__
sleep(__TIME__)#
1 or sleep(__TIME__)#
" or sleep(__TIME__)#
' or sleep(__TIME__)#
" or sleep(__TIME__)="
' or sleep(__TIME__)='
1) or sleep(__TIME__)#
") or sleep(__TIME__)="
') or sleep(__TIME__)='
1)) or sleep(__TIME__)#
")) or sleep(__TIME__)="
')) or sleep(__TIME__)='
;waitfor delay '0:0:__TIME__'--
);waitfor delay '0:0:__TIME__'--
';waitfor delay '0:0:__TIME__'--
";waitfor delay '0:0:__TIME__'--
');waitfor delay '0:0:__TIME__'--
");waitfor delay '0:0:__TIME__'--
));waitfor delay '0:0:__TIME__'--
'));waitfor delay '0:0:__TIME__'--
"));waitfor delay '0:0:__TIME__'--
benchmark(10000000,MD5(1))#
1 or benchmark(10000000,MD5(1))#
" or benchmark(10000000,MD5(1))#
' or benchmark(10000000,MD5(1))#
1) or benchmark(10000000,MD5(1))#
") or benchmark(10000000,MD5(1))#
') or benchmark(10000000,MD5(1))#
1)) or benchmark(10000000,MD5(1))#
")) or benchmark(10000000,MD5(1))#
')) or benchmark(10000000,MD5(1))#
1 and 1=1
1' and 1=(select count(*) from admin_user); --
1 or 1=1
1' or '1'='1
1'or'1'='1
fake@ema'or'il.nl'='il.nl
1 and CONCAT(username, '') = 'dbo'
'; desc admin_user; --
1' and entity_id = '1
' or username is not NULL or username = '
1 and ascii(lower(substring((select top 1 name from admin_user where entity_id='u'), 1, 1))) > 116
1 union all select 1,2,3,4,5,6,name from admin_user where entity_id=id = 'u' --
1 uni/**/on select all from admin_user where
0 or 1=1
1;(load_file(char(47,101,116,99,47,112,97,115,115,119,100))),1,1,1;
1 or 1=1
1;SELECT%20*
1 waitfor delay '0:0:10'--
'%20or%20''='
'%20or%201=1
')%20or%20('x'='x
'%20or%20'x'='x
%20or%20x=x
23 OR 1=1
%27%20or%201=1
'||'6
admin' or '
' and 1=( if((load_file(char(110,46,101,120,116))<>char(39,39)),1,0));
' and 1 in (select entity_id from admin_user)--
anything' OR 'x'='x
"a"" or 1=1--"
a' or 1=1--
"a"" or 3=3--"
a' or 3=3--
a' or 'a' = 'a
&apos;%20OR%20'
a' waitfor delay '0:0:10'--
'; begin declare @var varchar(8000) set @var=':' select @var=@var+'+login+'/'+password+' ' from users where login > 
'||(elt(-3+5,bin(15),ord(10),hex(char(45))))
' group by userid having 1=1--
' having 1=1--
hi or 1=1 --"
hi' or 1=1 --
"hi"") or (""a""=""a"
hi or a=a
hi' or 'a'='a
hi') or ('a'='a
'hi' or 'x'='x';
' or ''='
1 or 0=0 #
' or 0=0 --
' or 0=0 #
" or 0=0 --
or 0=0 --
' or 1 --'
' or 1/*
; or '1'='1'
' or '1'='1
' or '1'='1'--
' or 1=1
' or 1=1 /*
' or 1=1--
' or 1=1-- 
'/**/or/**/1/**/=/**/1
‘ or 1=1 --
" or 1=1--
or 1=1
or 1=1--
 or 1=1 or ""=
' or 1=1 or ''='
' or 1 in (select @@version)--
or%201=1
or%201=1 --
' or 2 > 1
' or 2 between 1 and 3
' or 3=3
‘ or 3=3 --
' or '7659'='7659
' or 'a'='a
') or ('a'='a
" or "a"="a
 or isNULL(1/0) /*
" or isNULL(1/0) /*
' or 'something' like 'some%'
' or 'something' = 'some'+'thing'
' or 'text' = n'text'
' or 'text' > 't'
' or entity_id like '%
' or 'unusual' = 'unusual'
' or entity_id like char(37);
' or 'whatever' in ('whatever')
' select * from admin_user--
' select entity_id from admin_user where id = (select id from admin_user where entity_id = 'some')--
' union select 1,load_file('/etc/passwd'),1,1,1;
) union select * from information_schema.tables;
' union select * from users where login = char(114,111,111,116);
x' AND 1=(SELECT COUNT(*) FROM admin_user); --
x' AND entity_id IS NULL; --
x' AND admin_user.entity_id IS NULL; --
x' AND entity_id IS NULL; --
x' or 1=1 or 'x'='y
x' OR entity_id LIKE '%Bob%
ý or 1=1 --
__EOF__
);

        foreach ($tests as $test) {
            /** @var ScanResultInterface $scanResult */
            $scanResult = $detector->scanRequest(['POST' => [$fieldName => $test]]);
            $this->assertGreaterThanOrEqual(DetectorInterface::SCORE_CRITICAL_MATCH, $scanResult->getScore(), "Failed to detect attack: " . $test);
        }
    }

    public function testXssInjections()
    {
        $detector = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('MSP\Shield\Model\Ips');

        $fieldName = 'somefield';
        $tests = explode("\n", <<< __EOF__
<font style='color:expression(alert('XSS'))'>
' onmouseover=alert(/Black.Spook/)
";eval(unescape(location))//#  %0Aalert(0)
"><BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>
"><iframe%20src="http://google.com"%%203E
"><img src=x onerror=prompt(1);>
"><img src=x onerror=window.open('https://www.google.com/');>
'%22--%3E%3C/style%3E%3C/script%3E%3Cscript%3Eshadowlabs(0x000045)%3C/script%3E
%27%22--%3E%3C%2Fstyle%3E%3C%2Fscript%3E%3Cscript%3ERWAR%280x00010E%29%3C%2Fscript%3E
%3Cscript%3Exhr=new%20ActiveXObject%28%22Msxml2.XMLHTTP%22%29;xhr.open%28%22GET%22,%22/xssme2%22,true%29;xhr.onreadystatechange=function%28%29{if%28xhr.readyState==4%26%26xhr.status==200%29{alert%28xhr.responseText.match%28/%27%28[^%27]%2b%29/%29[1]%29}};xhr.send%28%29;%3C/script%3E
&#x61;l&#x65;rt&#40;1)
&<script&S1&TS&1>alert&A7&(1)&R&UA;&&<&A9&11/script&X&>
&lt;IMG """><SCRIPT>alert("XSS")</SCRIPT>">
&lt;img src&equals;x:x onerror&equals;alert&lpar;1&rpar;&gt;
&lt;IMG SRC=&amp;#106;&amp;#97;&amp;#118;&amp;#97;&amp;#115;&amp;#99;&amp;#114;&amp;#105;&amp;#112;&amp;#116;&amp;#58;&amp;#97;&amp;#108;&amp;#101;&amp;#114;&amp;#116;&amp;#40;
&lt;SCRIPT SRC=//xss.rocks/.j>
'); alert('XSS
\";alert('XSS');//
<%<!--'%><script>alert(1);</script -->
<%73%63%72%69%70%74> %64 = %64%6f%63%75%6d%65%6e%74%2e%63%72%65%61%74%65%45%6c%65%6d%65%6e%74(%22%64%69%76%22); %64%2e%61%70%70%65%6e%64%43%68%69%6c%64(%64%6f%63%75%6d%65%6e%74%2e%68%65%61%64%2e%63%6c%6f%6e%65%4e%6f%64%65(%74%72%75%65)); %61%6c%65%72%74(%64%2e%69%6e%6e%65%72%48%54%4d%4c%2e%6d%61%74%63%68(%22%63%6f%6f%6b%69%65 = '(%2e%2a%3f)'%22)[%31]); </%73%63%72%69%70%74>
<--`<img/src=` onerror=alert(1)> --!>
<~/XSS/*-*/STYLE=xss:e/**/xpression(alert('XSS'))>
<<scr\0ipt/src=http://xss.com/xss.js></script
<<SCRIPT>alert("XSS");//<</SCRIPT>
<a  href="data:text/html;blabla,&#60&#115&#99&#114&#105&#112&#116&#32&#115&#114&#99&#61&#34&#104&#116&#116&#112&#58&#47&#47&#115&#116&#101&#114&#110&#101&#102&#97&#109&#105&#108&#121&#46&#110&#101&#116&#47&#102&#111&#111&#46&#106&#115&#34&#62&#60&#47&#115&#99&#114&#105&#112&#116&#62&#8203">Click  Me</a>
<a aa aaa aaaa aaaaa aaaaaa aaaaaaa aaaaaaaa  aaaaaaaaa aaaaaaaaaa  href=j&#97v&#97script&#x3A;&#97lert(1)>ClickMe
<a href="data:text/html;base64_,<svg/onload=\u0061&#x6C;&#101%72t(1)>">X</a
<a href="javascript&colon;\u0061&#x6C;&#101%72t&lpar;1&rpar;"><button>
<a href="jAvAsCrIpT&colon;alert&lpar;1&rpar;">X</a>
<a href=javascript&colon;alert&lpar;document&period;cookie&rpar;>Click Here</a>
<a onmouseover="alert(document.cookie)">xxs link</a>
<a onmouseover=alert(document.cookie)>xxs link</a>
<a target="x" href="xssme?xss=%3Cscript%3EaddEventListener%28%22DOMFrameContentLoaded%22,%20function%28e%29%20{e.stopPropagation%28%29;},%20true%29;%3C/script%3E%3Ciframe%20src=%22data:text/html,%253cscript%253eObject.defineProperty%28top,%20%27MyEvent%27,%20{value:%20Object,%20configurable:%20true}%29;function%20y%28%29%20{alert%28top.Safe.get%28%29%29;};event%20=%20new%20Object%28%29;event.type%20=%20%27click%27;event.isTrusted%20=%20true;y%28event%29;%253c/script%253e%22%3E%3C/iframe%3E
<a target="x" href="xssme?xss=<script>find('cookie'); var doc = getSelection().getRangeAt(0).startContainer.ownerDocument; console.log(doc); var xpe = new XPathEvaluator(); var nsResolver = xpe.createNSResolver(doc); var result = xpe.evaluate('//script/text()', doc, nsResolver, 0, null); alert(result.iterateNext().data.match(/cookie = '(.*?)'/)[1])</script>
<a target="x" href="xssme?xss=<script>function x(window) { eval(location.hash.substr(1)) }</script><iframe src=%22javascript:parent.x(window);%22></iframe>#var xhr = new window.XMLHttpRequest();xhr.open('GET', '.', true);xhr.onload = function() { alert(xhr.responseText.match(/cookie = '(.*?)'/)[1]) };xhr.send();
<a target="x" href="xssme?xss=<script>var cl=Components;var fcc=String.fromCharCode;doc=cl.lookupMethod(top, fcc(100,111,99,117,109,101,110,116) )( );cl.lookupMethod(doc,fcc(119,114,105,116,101))(doc.location.hash)</script>#<iframe src=data:text/html;base64,PHNjcmlwdD5ldmFsKGF0b2IobmFtZSkpPC9zY3JpcHQ%2b name=ZG9jPUNvbXBvbmVudHMubG9va3VwTWV0aG9kKHRvcC50b3AsJ2RvY3VtZW50JykoKTt2YXIgZmlyZU9uVGhpcyA9ICBkb2MuZ2V0RWxlbWVudEJ5SWQoJ3NhZmUxMjMnKTt2YXIgZXZPYmogPSBkb2N1bWVudC5jcmVhdGVFdmVudCgnTW91c2VFdmVudHMnKTtldk9iai5pbml0TW91c2VFdmVudCggJ2NsaWNrJywgdHJ1ZSwgdHJ1ZSwgd2luZG93LCAxLCAxMiwgMzQ1LCA3LCAyMjAsIGZhbHNlLCBmYWxzZSwgdHJ1ZSwgZmFsc2UsIDAsIG51bGwgKTtldk9iai5fX2RlZmluZUdldHRlcl9fKCdpc1RydXN0ZWQnLGZ1bmN0aW9uKCl7cmV0dXJuIHRydWV9KTtmdW5jdGlvbiB4eChjKXtyZXR1cm4gdG9wLlNhZmUuZ2V0KCl9O2FsZXJ0KHh4KGV2T2JqKSk></iframe>
<BASE HREF="javascript:alert('XSS');//">
<BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>
<body onLoad="alert('XSS');"
<body onunload="javascript:alert('XSS');">
<body/onload=&lt;!--&gt;&#10alert(1)>
<div  style="position:absolute;top:0;left:0;width:100%;height:100%"  onmouseover="prompt(1)" onclick="alert(1)">x</button>?
<div onmouseover='alert&lpar;1&rpar;'>DIV</div>
<div/onmouseover='alert(1)'> style="x:">
<embed code="http://businessinfo.co.uk/labs/xss/xss.swf" allowscriptaccess=always>?
<embed src="http://corkami.googlecode.com/svn/!svn/bc/480/trunk/misc/pdf/helloworld_js_X.pdf"> ?
<form><button formaction=javascript&colon;alert(1)>CLICKME
<FRAMESET><FRAME SRC=\"javascript:alert('XSS');\"></FRAMESET>
<iframe  src="data:text/html,%3C%73%63%72%69%70%74%3E%61%6C%65%72%74%28%31%29%3C%2F%73%63%72%69%70%74%3E"></iframe>
<iframe  src=j&NewLine;&Tab;a&NewLine;&Tab;&Tab;v&NewLine;&Tab;&Tab;&Tab;a&NewLine;&Tab;&Tab;&Tab;&Tab;s&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;c&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;r&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;i&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;p&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;t&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&colon;a&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;l&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;e&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;r&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;t&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;%28&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;1&NewLine;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;&Tab;%29></iframe> ?
<iframe  src=j&Tab;a&Tab;v&Tab;a&Tab;s&Tab;c&Tab;r&Tab;i&Tab;p&Tab;t&Tab;:a&Tab;l&Tab;e&Tab;r&Tab;t&Tab;%28&Tab;1&Tab;%29></iframe>
<iframe id=%22ifra%22 src=%22/%22></iframe> <script>ifr = document.getElementById('ifra'); ifr.contentDocument.write(%22<scr%22 %2b %22ipt>top.foo = Object.defineProperty</scr%22 %2b %22ipt>%22); foo(window, 'Safe', {value:{}}); foo(Safe, 'get', {value:function() {    return document.cookie }}); alert(Safe.get());</script>
<iframe onload=%22write('<script>'%2Blocation.hash.substr(1)%2B'</script>')%22></iframe>#var xhr = new XMLHttpRequest();xhr.open('GET', 'http://xssme.html5sec.org/xssme2', true);xhr.onload = function() { alert(xhr.responseText.match(/cookie = '(.*?)'/)[1]) };xhr.send();
<iframe src=%22404%22 onload=%22content.frames[0].document.write(%26quot;<script>r=new XMLHttpRequest();r.open('GET','http://xssme.html5sec.org/xssme2',false);r.send(null);if(r.status==200){alert(r.responseText.substr(150,41));}<\/script>%26quot;)%22></iframe>
<iframe src=%22404%22 onload=%22frames[0].document.write(%26quot;<script>r=new XMLHttpRequest();r.open('GET','http://xssme.html5sec.org/xssme2',false);r.send(null);if(r.status==200){alert(r.responseText.substr(150,41));}<\/script>%26quot;)%22></iframe>
<iframe src=%22404%22 onload=%22self.frames[0].document.write(%26quot;<script>r=new XMLHttpRequest();r.open('GET','http://xssme.html5sec.org/xssme2',false);r.send(null);if(r.status==200){alert(r.responseText.substr(150,41));}<\/script>%26quot;)%22></iframe>
<iframe src=%22404%22 onload=%22top.frames[0].document.write(%26quot;<script>r=new XMLHttpRequest();r.open('GET','http://xssme.html5sec.org/xssme2',false);r.send(null);if(r.status==200){alert(r.responseText.substr(150,41));}<\/script>%26quot;)%22></iframe>
<iframe src=/ onload=eval(unescape(this.name.replace(/\/g,null))) name=fff%253Dnew%2520this.contentWindow.window.XMLHttpRequest%2528%2529%253Bfff.open%2528%2522GET%2522%252C%2522xssme2%2522%2529%253Bfff.onreadystatechange%253Dfunction%2528%2529%257Bif%2520%2528fff.readyState%253D%253D4%2520%2526%2526%2520fff.status%253D%253D200%2529%257Balert%2528fff.responseText%2529%253B%257D%257D%253Bfff.send%2528%2529%253B></iframe>
<iframe src=`http://xssme.html5sec.org/?xss=<iframe onload=%22xhr=new XMLHttpRequest();xhr.open('GET','http://html5sec.org/xssme2',true);xhr.onreadystatechange=function(){if(xhr.readyState==4%26%26xhr.status==200){alert(xhr.responseText.match(/'([^']%2b)/)[1])}};xhr.send();%22>`>
<iframe src=http://xss.rocks/scriptlet.html <
<iframe style="position:absolute;top:0;left:0;width:100%;height:100%" onmouseover="prompt(1)">
<iframe/onreadystatechange=\u0061\u006C\u0065\u0072\u0074('\u0061') worksinIE>
<iframe/onreadystatechange=alert(1)
<iframe/src \/\/onload = prompt(1)
<IMG DYNSRC=\"javascript:alert('XSS')\">
<IMG onmouseover="alert('xxs')">
<img src ?itworksonchrome?\/onerror = alert(1)???
<IMG SRC= onmouseover="alert('xxs')">
<IMG SRC=" &#14;  javascript:alert('XSS');">
<img src="/" =_=" title="onerror='prompt(1)'">
<IMG SRC="jav&#x09;ascript:alert('XSS');">
<IMG SRC="jav&amp;#x09;ascript:alert('XSS');">
<IMG SRC="jav&amp;#x0A;ascript:alert('XSS');">
<IMG SRC="jav&amp;#x0D;ascript:alert('XSS');">
<IMG SRC="javascript:alert('XSS')"
<img src="javascript:alert('XSS')">
<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>
<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>
<img src=`xx:xx`onerror=alert(1)>
<img src=http://www.google.fr/images/srpr/logo3w.png onload=alert(this.ownerDocument.cookie) width=0 height= 0 /> #
<IMG SRC=java%00script:alert(\"XSS\")>
<img src=x onerror="&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041">
<INPUT TYPE="IMAGE" SRC="javascript:alert('XSS');">
<input type="text" value=``<div/onmouseover='alert(1)'>X</div>
<input value=<><iframe/src=javascript:confirm(1)
<math><a xlink:href="//jsfiddle.net/t846h/">click
<meta http-equiv="refresh" content="0;javascript&colon;alert(1)"/>?
<object data="http://corkami.googlecode.com/svn/!svn/bc/480/trunk/misc/pdf/helloworld_js_X.pdf">?
<object data=%22data:text/html;base64,PHNjcmlwdD4gdmFyIHhociA9IG5ldyBYTUxIdHRwUmVxdWVzdCgpOyB4aHIub3BlbignR0VUJywgJ2h0dHA6Ly94c3NtZS5odG1sNXNlYy5vcmcveHNzbWUyJywgdHJ1ZSk7IHhoci5vbmxvYWQgPSBmdW5jdGlvbigpIHsgYWxlcnQoeGhyLnJlc3BvbnNlVGV4dC5tYXRjaCgvY29va2llID0gJyguKj8pJy8pWzFdKSB9OyB4aHIuc2VuZCgpOyA8L3NjcmlwdD4=%22>
<object data=data:text/html;base64,PHN2Zy9vbmxvYWQ9YWxlcnQoMik+></object>?
<object data=javascript&colon;\u0061&#x6C;&#101%72t(1)>
<object type="text/x-scriptlet" data="http://jsfiddle.net/XLE63/ "></object>
<script for=document event=onreadystatechange>getElementById('safe123').click()</script>
<script itworksinallbrowsers>/*<script* */alert(1)</script ?
<script src="data:text/javascript,alert(1)"></script>
<SCRIPT SRC="http:&#47;&#47;xss.rocks/xss.jpg"></SCRIPT>
<SCRIPT SRC=http://xss.rocks/xss.js?< B >
<script x> alert(1) </script 1=2
<script/src="data&colon;text%2Fj\u0061v\u0061script,\u0061lert('\u0061')"></script a=\u0061 & /=%2F
<SCRIPT/SRC="http:&#47;&#47;xss.rocks/xss.js"></SCRIPT>
<script/src=&#100&#97&#116&#97:text/&#x6a&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x000070&#x074,&#x0061;&#x06c;&#x0065;&#x00000072;&#x00074;(1)></script> ?
<script/src=data&colon;text/j\u0061v\u0061&#115&#99&#114&#105&#112&#116,\u0061%6C%65%72%74(/XSS/)></script ????????????
<SCRIPT\s" != "<SCRIPT/XSS\s';alert(String.fromCharCode(88,83,83))//';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//--></SCRIPT>">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>
<SCRIPT+FOR=document+EVENT=onreadystatechange>MouseEvent=function+MouseEvent(){};test=new+MouseEvent();test.isTrusted=true;test.type=%22click%22;getElementById(%22safe123%22).click=function()+{alert(Safe.get());};getElementById(%22safe123%22).click(test);</SCRIPT>#
<script>     function b() { return Safe.get(); } alert(b({type:String.fromCharCode(99,108,105,99,107),isTrusted:true})); </script>
<script>  (function (o) {   function exploit(x) {    if (x !== null)     alert('User cookie is ' %2B x);    else     console.log('fail');   }      o.onclick = function (e) {    e.__defineGetter__('isTrusted', function () { return true; });    exploit(Safe.get());   };      var e = document.createEvent('MouseEvent');   e.initEvent('click', true, true);   o.dispatchEvent(e);  })(document.getElementById('safe123')); </script>
<script>  function foo(elem, doc, text) {   elem.onclick = function (e) {    e.__defineGetter__(text[0], function () { return true })    alert(Safe.get());   };      var event = doc.createEvent(text[1]);   event.initEvent(text[2], true, true);   elem.dispatchEvent(event);  } </script> <img src=http://www.google.fr/images/srpr/logo3w.png onload=foo(this,this.ownerDocument,this.name.split(/,/)) name=isTrusted,MouseEvent,click width=0 height=0 /> #
<script> document.getElementById(%22safe123%22).click=function()+{alert(Safe.get());} document.getElementById(%22safe123%22).click({'type':'click','isTrusted':true}); </script>
<script> document.getElementById(%22safe123%22).setCapture(); document.getElementById(%22safe123%22).click(); </script>
<script> location.href = 'data:text/html;base64,PHNjcmlwdD54PW5ldyBYTUxIdHRwUmVxdWVzdCgpO3gub3BlbigiR0VUIiwiaHR0cDovL3hzc21lLmh0bWw1c2VjLm9yZy94c3NtZTIvIix0cnVlKTt4Lm9ubG9hZD1mdW5jdGlvbigpIHsgYWxlcnQoeC5yZXNwb25zZVRleHQubWF0Y2goL2RvY3VtZW50LmNvb2tpZSA9ICcoLio/KScvKVsxXSl9O3guc2VuZChudWxsKTs8L3NjcmlwdD4='; </script>
<script> var xdr = new ActiveXObject(%22Microsoft.XMLHTTP%22);  xdr.open(%22get%22, %22/xssme2%3Fa=1%22, true); xdr.onreadystatechange = function() { try{   var c;   if (c=xdr.responseText.match(/document.cookie = '(.*%3F)'/) )    alert(c[1]); }catch(e){} };  xdr.send(); </script>
<script> var+MouseEvent=function+MouseEvent(){}; MouseEvent=MouseEvent var+test=new+MouseEvent(); test.isTrusted=true; test.type='click';  document.getElementById(%22safe123%22).click=function()+{alert(Safe.get());} document.getElementById(%22safe123%22).click(test); </script>
<script> var+x+=+showModelessDialog+(this); alert(x.document.cookie); </script>
<script> var+xmlHttp+=+null; try+{ xmlHttp+=+new+XMLHttpRequest(); }+catch(e)+{} if+(xmlHttp)+{ xmlHttp.open('GET',+'/xssme2',+true); xmlHttp.onreadystatechange+=+function+()+{ if+(xmlHttp.readyState+==+4)+{ xmlHttp.responseText.match(/document.cookie%5Cs%2B=%5Cs%2B'(.*)'/gi); alert(RegExp.%241); } } xmlHttp.send(null); }; </script>
<script> var+xmlHttp+=+null; try+{ xmlHttp+=+new+XMLHttpRequest(); }+catch(e)+{} if+(xmlHttp)+{ xmlHttp.open('GET',+'/xssme2',+true); xmlHttp.onreadystatechange+=+function+()+{ if+(xmlHttp.readyState+==+4)+{ xmlHttp.responseText.match(/document.cookie%5Cs%2B=%5Cs%2B'(.*)'/gi); alert(RegExp.%241); } } xmlHttp.send(null); }; </script>#
<script>(function() {var event = document.createEvent(%22MouseEvents%22);event.initMouseEvent(%22click%22, true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);var fakeData = [event, {isTrusted: true}, event];arguments.__defineGetter__('0', function() { return fakeData.pop(); });alert(Safe.get.apply(null, arguments));})();</script>
<script>~'\u0061' ;  \u0074\u0068\u0072\u006F\u0077 ~ \u0074\u0068\u0069\u0073.  \u0061\u006C\u0065\u0072\u0074(~'\u0061')</script U+
<script>+-+-1-+-+alert(1)</script>
<script>alert(document.documentElement.innerHTML.match(/'([^']%2b)/)[1])</script>
<script>alert(document.getElementsByTagName('html')[0].innerHTML.match(/'([^']%2b)/)[1])</script>
<script>alert(document.head.childNodes[3].text)</script>
<script>alert(document.head.innerHTML.substr(146,20));</script>
<script>alert('XSS');</script>
<script>function x(window) { eval(location.hash.substr(1)) }; open(%22javascript:opener.x(window)%22)</script>#var xhr = new window.XMLHttpRequest();xhr.open('GET', 'http://xssme.html5sec.org/xssme2', true);xhr.onload = function() { alert(xhr.responseText.match(/cookie = '(.*?)'/)[1]) };xhr.send();
<script>function x(window) { eval(location.hash.substr(1)) }</script><iframe id=iframe src=%22javascript:parent.x(window)%22><iframe>#var xhr = new window.XMLHttpRequest();xhr.open('GET', 'http://xssme.html5sec.org/xssme2', true);xhr.onload = function() { alert(xhr.responseText.match(/cookie = '(.*?)'/)[1]) };xhr.send();
<script>Object.defineProperties(window, {Safe: {value: {get: function() {return document.cookie}}}});alert(Safe.get())</script>
<script>Object.defineProperty(window, 'Safe', {value:{}});Object.defineProperty(Safe, 'get', {value:function() {return document.cookie}});alert(Safe.get())</script>
<script>var request = new XMLHttpRequest();request.open('GET', 'http://html5sec.org/xssme2', false);request.send(null);if (request.status == 200){alert(request.responseText.substr(150,41));}</script>
<script>var script = document.getElementsByTagName('script')[0]; var clone = script.childNodes[0].cloneNode(true); var ta = document.createElement('textarea'); ta.appendChild(clone); alert(ta.value.match(/cookie = '(.*?)'/)[1])</script>
<script>var x = document.createElement('iframe');document.body.appendChild(x);var xhr = x.contentWindow.XMLHttpRequest();xhr.open('GET', 'http://xssme.html5sec.org/xssme2', true);xhr.onload = function() { alert(xhr.responseText.match(/cookie = '(.*?)'/)[1]) };xhr.send();</script>
<script>var x = safe123.onclick;safe123.onclick = function(event) {var f = false;var o = { isTrusted: true };var a = [event, o, event];var get;event.__defineGetter__('type', function() {get = arguments.callee.caller.arguments.callee;return 'click';});var _alert = alert;alert = function() { alert = _alert };x.apply(null, a);(function() {arguments.__defineGetter__('0', function() { return a.pop(); });alert(get());})();};safe123.click();</script>#
'<script>window.onload=function(){document.forms[0].message.value='1';}</script>
<script>x=document.createElement(%22iframe%22);x.src=%22http://xssme.html5sec.org/404%22;x.onload=function(){window.frames[0].document.write(%22<script>Object.defineProperty(parent,'Safe',{value:{}});Object.defineProperty(parent.Safe,'get',{value:function(){return top.document.cookie}});alert(parent.Safe.get())<\/script>%22)};document.body.appendChild(x);</script>
<script>x=document.createElement(%22iframe%22);x.src=%22http://xssme.html5sec.org/404%22;x.onload=function(){window.frames[0].document.write(%22<script>r=new XMLHttpRequest();r.open('GET','http://xssme.html5sec.org/xssme2',false);r.send(null);if(r.status==200){alert(r.responseText.substr(150,41));}<\/script>%22)};document.body.appendChild(x);</script>
<script>xhr=new ActiveXObject(%22Msxml2.XMLHTTP%22);xhr.open(%22GET%22,%22/xssme2%22,true);xhr.onreadystatechange=function(){if(xhr.readyState==4%26%26xhr.status==200){alert(xhr.responseText.match(/'([^']%2b)/)[1])}};xhr.send();</script>
<svg contentScriptType=text/vbs><script>MsgBox+1
<svg/onload=alert(1)
<svg><script ?>alert(1)
<svg><script onlypossibleinopera:-)> alert(1)
<svg><script>//&NewLine;confirm(1);</script </svg>
<textarea id=ta onfocus=%22write('<script>alert(1)</script>')%22 autofocus></textarea>
<textarea id=ta onfocus=console.dir(event.currentTarget.ownerDocument.location.href=%26quot;javascript:\%26quot;%26lt;script%26gt;var%2520xhr%2520%253D%2520new%2520XMLHttpRequest()%253Bxhr.open('GET'%252C%2520'http%253A%252F%252Fhtml5sec.org%252Fxssme2'%252C%2520true)%253Bxhr.onload%2520%253D%2520function()%2520%257B%2520alert(xhr.responseText.match(%252Fcookie%2520%253D%2520'(.*%253F)'%252F)%255B1%255D)%2520%257D%253Bxhr.send()%253B%26lt;\/script%26gt;\%26quot;%26quot;) autofocus></textarea>
<textarea id=ta></textarea><script>ta.appendChild(safe123.parentNode.previousSibling.previousSibling.childNodes[3].firstChild.cloneNode(true));alert(ta.value.match(/cookie = '(.*?)'/)[1])</script>
<var onmouseover="prompt(1)">On Mouse Over</var>?
<video+onerror='javascript:MouseEvent=function+MouseEvent(){};test=new+MouseEvent();test.isTrusted=true;test.type=%22click%22;document.getElementById(%22safe123%22).click=function()+{alert(Safe.get());};document.getElementById(%22safe123%22).click(test);'><source>%23
alert&lpar;1&rpar;
alert(1)
alert\\`1\\`
alert`1`
<script>alert(Components.lookupMethod(Components.lookupMethod(Components.lookupMethod(Components.lookupMethod(this,'window')(),'document')(), 'getElementsByTagName')('html')[0],'innerHTML')().match(/d.*'/));</script>
http://www.<script>alert(1)</script .com
javascript:alert%28/xss/%29
javascript:alert(1)
PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==
x”</title><img src%3dx onerror%3dalert(1)>
[[#%3Cscript%3Ealert(1)%3C/script%3E|
a{b:`function(){alert(1)}()`;}
"><style type=text/less>a{b:`function(){alert(1)}()`;}</style>
[][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]][([][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]+(!![]+[][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]])[+!+[]+[+[]]]+([][[]]+[])[+!+[]]+(![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[+!+[]]+([][[]]+[])[+[]]+([][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]])[+!+[]+[+[]]]+(!![]+[])[+!+[]]]((![]+[])[+!+[]]+(![]+[])[!+[]+!+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]+(!![]+[])[+[]]+(![]+[][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]])[!+[]+!+[]+[+[]]]+[+!+[]]+(!![]+[][(![]+[])[+[]]+([![]]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]])[!+[]+!+[]+[+[]]])()
eval(function(p,a,c,k,e,d){e=function(c){return c};if(!''.replace(/^/,String)){while(c--){d[c]=k[c]||c}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('0(1)',2,2,'alert|'.split('|'),0,{}))
__EOF__
);

        foreach ($tests as $test) {
            /** @var ScanResultInterface $scanResult */
            $scanResult = $detector->scanRequest(['POST' => [$fieldName => $test]]);
            $this->assertGreaterThanOrEqual(DetectorInterface::SCORE_CRITICAL_MATCH, $scanResult->getScore(), "Failed to detect attack: " . $test);
        }
    }

    public function testUncertainContents()
    {
        $detector = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('MSP\Shield\Model\Ips');

        $fieldName = 'somefield';
        $tests = [
            "3 or something",
            "4 or more",
        ];

        foreach ($tests as $test) {
            /** @var ScanResultInterface $scanResult */
            $scanResult = $detector->scanRequest(['POST' => [$fieldName => $test]]);
            $this->assertLessThan(50, $scanResult->getScore(),
                "False positive on: <" . $test . '>');

            $this->assertGreaterThan(10, $scanResult->getScore(),
                "Possible threat not detected: <" . $test . '>');
        }
    }

    public function testNonDangerousContents()
    {
        $detector = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('MSP\Shield\Model\Ips');

        $fieldName = 'somefield';
        $tests = [
            'I would like to test; Any way?',
            "I'dd like to test this phrase or another one",
            'A composed-word should not trigger',
            'I would like to test; Any way?',
            "I'dd like to test this phrase or another one I'll find",
            '123',
            "Talking about dots.this should. be a dotted. phrase.",
            'A composed-word should not trigger',
            'This is a normal phrase(should not trigger); But I need to check it and test!',
            'someone could -- write this',
            'The way you select your words may or may not activate a trigger',
            'You should select your words from you vocabulary',
            'Let me try writing a complex phrase talking about a table you should select and like to make a quote',
            '{1, 2, 3, 4, 5, 6, 7}',
        ];

        foreach ($tests as $test) {
            /** @var ScanResultInterface $scanResult */
            $scanResult = $detector->scanRequest(['POST' => [$fieldName => $test]]);
            $this->assertLessThan(20, $scanResult->getScore(),
                "False positive on: <" . $test . '>');
        }
    }
}
