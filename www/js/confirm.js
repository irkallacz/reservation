document.querySelectorAll('.confirm').forEach(function (element) {
	element.addEventListener('click', function (event) {
		if (!confirm(event.target.dataset.message)) event.preventDefault();
	})
});