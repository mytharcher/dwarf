elf(function () {
	elf('#DocContent code').forEach(function (item) {
		hljs.highlightBlock(item);
	});
	/*
	elf().on('DocToc', 'mouseover', function (ev) {
		elf().addClass('DocToc', 'expand');
	});
	
	elf().on('DocToc', 'mouseout', function (ev) {
		elf().removeClass('DocToc', 'expand');
	});
	*/
	function scrolling(ev) {
		var docElem = elf().Chrome || elf().Safari ? document.body : document.documentElement;
		var toc = elf('#DocToc');
		var content = elf('#DocContent');
		bodyScrollTop = docElem.scrollTop;
		headerTop = elf('#MainBody').getPosition(docElem).y;
		if (bodyScrollTop > headerTop) {
			var contentBottom = content.getPosition(docElem).y + content[0].offsetHeight;
			var tocHeight = toc[0].offsetHeight;
			if (contentBottom < bodyScrollTop + tocHeight + 20) {
				toc.css({position: 'absolute', top: (contentBottom - tocHeight - headerTop) + 'px'});
			} else {
				toc.css({position: 'fixed', top: '10px'});
			}
		} else {
			toc.css({position: 'absolute', top: 'auto'});
		}
	}
	elf(window).on('scroll', scrolling);
	scrolling();
	
	elf().on('MainBody', 'dblclick', function (ev) {
		elf().toggleClass(this, 'doc-collapse-menu');
		ev.preventDefault();
		return false;
	});
});