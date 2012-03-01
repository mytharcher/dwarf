elf(function () {
	elf('a.email-link').forEach(function (item) {
		var email = elf(item).text().replace('[at]', '@').replace('[dot]', '.');
		item.href = 'mailto:' + email;
		item.innerHTML = email;
	});
});
