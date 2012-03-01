<!DOCTYPE html><!-- placeholder(vars) -->
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="author" content="mytharcher" />
<meta name="keywords" content="elf,js,elfjs,JavaScript,library" />
<title>elf+js: ${page.title}</title>
<link rel="stylesheet" type="text/css" href="/assets/css/site.css" />
<!-- placeholder(headers) -->

<!--[if IE]>
<script>
document.createElement('header');
document.createElement('footer');
document.createElement('aside');
document.createElement('section');
document.createElement('figure');
</script>
<![endif]-->
</head>

<body>

<div id="Holder">
	<div id="MainPage">
		<div id="Header">
			<div class="layout-section">
				<div class="logo"><a href="http://elfjs.com/"><img src="${images.logo.src}" alt="elf+js" title="体验愉悦的JavaScript开发" /></a></div>
				<div class="additional"><!-- placeholder(titleBlock) --></div>
			</div>
			<div class="layout-aside">
				<ul>
					<li><a href="/">首页</a></li>
					<li><a href="/downloads/">下载</a></li>
					<li><a href="/docs/">文档</a></li>
					<li><a href="/blog/">博客</a></li>
				</ul>
			</div>
		</div>
		
		<div id="MainBody" class="${page.mainClass}"><!-- placeholder(mainBody) --></div>
		
		<div id="Footer">
			<p>该项目所有代码使用<a href="http://github/elfjs/" target="_blank">github</a>托管，并以<a href="/license.txt" target="_blank">MIT协议</a>授权许可。[<a href="/docs/develop/contributor.html">贡献者</a>]</p>
			<p>&copy; Since 2011 <a href="http://elfjs.com/">elfjs.com</a> Email:<a href="#" class="email-link">elfjslib<span class="symbol">[at]</span>gmail<span class="symbol">[dot]</span>com</a></p>
		</div>
	</div>
</div>

<!--<script src="${version.file.url}elf-${version}-min.js"></script>-->
<script src="/assets/js/elf.js"></script>
<script type="text/javascript" src="/assets/js/site.js"></script>
<!-- placeholder(scripts) -->
<div style="display:none">
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fc721ec118ef865ee41646415bc0e9734' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-27954937-1']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</div>
</body>
</html>