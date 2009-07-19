--TEST--
Test parse_url() function: Parse a load of URLs without specifying PHP_URL_SCHEME as the URL component 
--FILE--
<?php
/* Prototype  : proto mixed parse_url(string url, [int url_component])
 * Description: Parse a URL and return its components 
 * Source code: ext/standard/url.c
 * Alias to functions: 
 */

/*
 * Parse a load of URLs without specifying PHP_URL_SCHEME as the URL component
 */
include_once(dirname(__FILE__) . '/urls.inc');

foreach ($urls as $url) {
	echo "--> $url   : ";
	var_dump(parse_url($url, PHP_URL_SCHEME));

}

echo "Done";
?>
--EXPECTF--
--> 64.246.30.37   : NULL
--> http://64.246.30.37   : unicode(4) "http"
--> http://64.246.30.37/   : unicode(4) "http"
--> 64.246.30.37/   : NULL
--> 64.246.30.37:80/   : NULL
--> php.net   : NULL
--> php.net/   : NULL
--> http://php.net   : unicode(4) "http"
--> http://php.net/   : unicode(4) "http"
--> www.php.net   : NULL
--> www.php.net/   : NULL
--> http://www.php.net   : unicode(4) "http"
--> http://www.php.net/   : unicode(4) "http"
--> www.php.net:80   : NULL
--> http://www.php.net:80   : unicode(4) "http"
--> http://www.php.net:80/   : unicode(4) "http"
--> http://www.php.net/index.php   : unicode(4) "http"
--> www.php.net/?   : NULL
--> www.php.net:80/?   : NULL
--> http://www.php.net/?   : unicode(4) "http"
--> http://www.php.net:80/?   : unicode(4) "http"
--> http://www.php.net:80/index.php   : unicode(4) "http"
--> http://www.php.net:80/foo/bar/index.php   : unicode(4) "http"
--> http://www.php.net:80/this/is/a/very/deep/directory/structure/and/file.php   : unicode(4) "http"
--> http://www.php.net:80/this/is/a/very/deep/directory/structure/and/file.php?lots=1&of=2&parameters=3&too=4&here=5   : unicode(4) "http"
--> http://www.php.net:80/this/is/a/very/deep/directory/structure/and/   : unicode(4) "http"
--> http://www.php.net:80/this/is/a/very/deep/directory/structure/and/file.php   : unicode(4) "http"
--> http://www.php.net:80/this/../a/../deep/directory   : unicode(4) "http"
--> http://www.php.net:80/this/../a/../deep/directory/   : unicode(4) "http"
--> http://www.php.net:80/this/is/a/very/deep/directory/../file.php   : unicode(4) "http"
--> http://www.php.net:80/index.php   : unicode(4) "http"
--> http://www.php.net:80/index.php?   : unicode(4) "http"
--> http://www.php.net:80/#foo   : unicode(4) "http"
--> http://www.php.net:80/?#   : unicode(4) "http"
--> http://www.php.net:80/?test=1   : unicode(4) "http"
--> http://www.php.net/?test=1&   : unicode(4) "http"
--> http://www.php.net:80/?&   : unicode(4) "http"
--> http://www.php.net:80/index.php?test=1&   : unicode(4) "http"
--> http://www.php.net/index.php?&   : unicode(4) "http"
--> http://www.php.net:80/index.php?foo&   : unicode(4) "http"
--> http://www.php.net/index.php?&foo   : unicode(4) "http"
--> http://www.php.net:80/index.php?test=1&test2=char&test3=mixesCI   : unicode(4) "http"
--> www.php.net:80/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : NULL
--> http://secret@www.php.net:80/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : unicode(4) "http"
--> http://secret:@www.php.net/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : unicode(4) "http"
--> http://:hideout@www.php.net:80/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : unicode(4) "http"
--> http://secret:hideout@www.php.net/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : unicode(4) "http"
--> http://secret@hideout@www.php.net:80/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : unicode(4) "http"
--> http://secret:hid:out@www.php.net:80/index.php?test=1&test2=char&test3=mixesCI#some_page_ref123   : unicode(4) "http"
--> nntp://news.php.net   : unicode(4) "nntp"
--> ftp://ftp.gnu.org/gnu/glic/glibc.tar.gz   : unicode(3) "ftp"
--> zlib:http://foo@bar   : unicode(4) "zlib"
--> zlib:filename.txt   : unicode(4) "zlib"
--> zlib:/path/to/my/file/file.txt   : unicode(4) "zlib"
--> foo://foo@bar   : unicode(3) "foo"
--> mailto:me@mydomain.com   : unicode(6) "mailto"
--> /foo.php?a=b&c=d   : NULL
--> foo.php?a=b&c=d   : NULL
--> http://user:passwd@www.example.com:8080?bar=1&boom=0   : unicode(4) "http"
--> file:///path/to/file   : unicode(4) "file"
--> file://path/to/file   : unicode(4) "file"
--> file:/path/to/file   : unicode(4) "file"
--> http://1.2.3.4:/abc.asp?a=1&b=2   : unicode(4) "http"
--> http://foo.com#bar   : unicode(4) "http"
--> scheme:   : unicode(6) "scheme"
--> foo+bar://baz@bang/bla   : unicode(7) "foo+bar"
--> gg:9130731   : unicode(2) "gg"
--> http://user:@pass@host/path?argument?value#etc   : unicode(4) "http"
--> http://10.10.10.10/:80   : unicode(4) "http"
--> http://x:?   : unicode(4) "http"
--> x:blah.com   : unicode(1) "x"
--> x:/blah.com   : unicode(1) "x"
--> x://::abc/?   : unicode(1) "x"
--> http://::?   : unicode(4) "http"
--> x://::6.5   : unicode(1) "x"
--> http://?:/   : unicode(4) "http"
--> http://@?:/   : unicode(4) "http"
--> file:///:   : unicode(4) "file"
--> file:///a:/   : unicode(4) "file"
--> file:///ab:/   : unicode(4) "file"
--> file:///a:/   : unicode(4) "file"
--> file:///@:/   : unicode(4) "file"
--> file:///:80/   : unicode(4) "file"
--> []   : NULL
--> http://[x:80]/   : unicode(4) "http"
-->    : NULL
--> /   : NULL
--> http:///blah.com   : 
Warning: parse_url(http:///blah.com): Unable to parse URL in %s on line 15
bool(false)
--> http://:80   : 
Warning: parse_url(http://:80): Unable to parse URL in %s on line 15
bool(false)
--> http://user@:80   : 
Warning: parse_url(http://user@:80): Unable to parse URL in %s on line 15
bool(false)
--> http://user:pass@:80   : 
Warning: parse_url(http://user:pass@:80): Unable to parse URL in %s on line 15
bool(false)
--> http://:   : 
Warning: parse_url(http://:): Unable to parse URL in %s on line 15
bool(false)
--> http://@/   : 
Warning: parse_url(http://@/): Unable to parse URL in %s on line 15
bool(false)
--> http://@:/   : 
Warning: parse_url(http://@:/): Unable to parse URL in %s on line 15
bool(false)
--> http://:/   : 
Warning: parse_url(http://:/): Unable to parse URL in %s on line 15
bool(false)
--> http://?   : 
Warning: parse_url(http://?): Unable to parse URL in %s on line 15
bool(false)
--> http://?:   : 
Warning: parse_url(http://?:): Unable to parse URL in %s on line 15
bool(false)
--> http://:?   : 
Warning: parse_url(http://:?): Unable to parse URL in %s on line 15
bool(false)
--> http://blah.com:123456   : 
Warning: parse_url(http://blah.com:123456): Unable to parse URL in %s on line 15
bool(false)
--> http://blah.com:abcdef   : 
Warning: parse_url(http://blah.com:abcdef): Unable to parse URL in %s on line 15
bool(false)
Done
