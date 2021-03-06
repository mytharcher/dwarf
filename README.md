﻿Dwarf Site Builder
==================

该项目通过特定的模板规则和Markdown插件生成纯静态的文档类内容站点。

Usage
-----

下载项目：

	$ git clone https://github.com/mytharcher/dwarf.git

### 命令行方式 ###

在命令行运行PHP脚本：
	
	$ cd path/to/dwarf
	$ mkdir output && chmod 777 output
	$ php build.php -p example -o output

### Web方式 ###

0.	下载仓库代码到本地Apache服务器下（最好建一个虚拟站点）。

0.  在浏览器中访问以下地址触发站点生成操作：
	
		http://yourhost/build.php?p=<project path>&o=<output path>

	以上`path`均为相对路径。需要确保`output path`目录存在且服务器有写权限。不需要加前置和结尾的`/`。

Template and Syntax
-------------------

其实dwarf也是一个简单模板引擎，并包括一些简单的解释语法。

### 文件夹结构 ###

对于一个要生成的站点目录，应该有如下的结构：

    .
    |-- includes/         # 用于片段包含的模板文件
    |-- targets/          # 需要生成的目标模板文件
    |-- wrap/             # 模板包装器，类似于继承使用的母版
    `-- config.inc        # 配置文件

### 配置文件 ###

`config.inc`是一个标准的PHP文件，dwarf在处理每一个目标文件之前会先载入这里的配置参数。目前设计了两项配置内容：`$cfg['vars']`和`$cfg['group']`。

`vars`中配置全局使用的变量，如站点标题，地址等。

`group`中以母版路径为键名配置使用该母版的目标模板的路径数组，以方便的指明哪些目标模板需要使用母版。其中需要注意：

* 所有配置的路径都是相对于项目目录的绝对路径，需要添加前置的`/`，如`/wrap/content.tpl`；
* 目标模板的路径可以使用wildcard，如`/targets/docs/*.html`；
* `wrap`目录中的母版可以多层继承，只需在要继承的配置组中加入继承的母版路径；

### 文件解析规则 ###

脚本中有三类文件的生成规则，分别对应目录，普通文件和文本文件。

* 扫描到`targets`目录中任何目录时，都会按照原路径创建同样的目录；
* 扫描到非`.html`或`.text`结尾的任何文件，都会直接按同样路径copy这个文件；
* 上一项中两种文本文件都会进行模板替换和母版继承包装，再生成到对应路径。其中`.text`文件会被认为是Markdown格式进行解析生成。

### 模板语法 ###

Dwarf支持几种简单的模板语法：变量定义，模板包含和母版包含。所有的模板语法都是`<!-- syntax(detail) -->`的格式。

#### 变量 ####

要在模板中定义变量可以使用这个语法：

    <!-- vars(
    varName=value
    varName2=value2
    ) -->

其中括号内每行代表一个变量，以等号`=`分隔变量名和值。这些变量可以在当前模板和要继承嵌套的母版中使用。使用的方式类似其他模板语言：`${varName}`。

#### 模板包含 ####

使用`include`语法来包含一些模板片段到当前模板中：

    <!-- include(/includes/file.html) -->

这里的路径都是基于项目路径的绝对路径。

#### 模板继承 ####

对于目标模板是否使用和使用哪个母版进行继承已经在配置文件中的`group`项中配置过了。使用母版有两种语法，其中在目标模板中定义一个要替换母版内容的块：

    <!-- block(name) -->

然后在母版文件中定义要替换的内容块：

    <!-- placeholder(name) -->

这样在替换时引入目标模板的内容块到母版中了。

#### Markdown ####

Dwarf使用了<https://github.com/michelf/php-markdown>项目的Markdown解释器，并针对文档大纲（Table of Content）的生成做了对应的修改。在`.text`文件中具体使用时，只需加入`[TOC]`即可在模板中生成大纲内容。

更多的站点模板语法和结构参见`example`目录。

History
-------

矮人在有和精灵一起的剧情里，总是以一种干吃苦活的次要身份出现。

在[elf+js]项目里，用于构建官方网站的这个程序就是这样。在了解github上可以使用更强大的[jekyll](http://github.com/mojombo/jekyll)之前，由于受不了一个一个页面构建的痛苦，然后就开发了这个构建脚本。等站点开发完，才发现原来github早就集成了jekyll，版本控制不说，加上全程托管和域名指向，什么都可以搞定了，根本不用我在这费事。

不过说回来，为什么要放弃自己写过的东西呢？虽然看起来每个轮子都差不多，但总和别人的有那么一点不一样。即使写的再烂，也是自己的，何况还有点用呢。

所以就放在这了，刚好作为[elf+js][]网站的第一版的纪念。**Dwarf**这个名字倒是临时想的，正好就当个elf的龙套了。也许哪天我又有新的想法，会再来升级的。

[elf+js]: http://elfjs.com/
