$(function() {
	$(".aDodajDoKoszyka").click(function() {
		const $link = $(this);
		const url = $(this).attr('href');
		const dodano = '<i class="fas fa-check-circle text-success"></i>';

		$.post(url, function(resp) {
			if (resp === 'ok') {
				$link.replaceWith(dodano);
			} else {
				alert('Wystąpił błąd');
			}
		});
		return false;
	});
	
	$(".aUsunZKoszyka").click(function() {
		const $link = $(this);
		var url = $(this).attr('href');	
		const dodano = `<i class="fas fa-cart-plus"></i>`;
		
		$.post(url, function(resp) {
			if (resp === 'ok') {
				alert('Usunięto produkt z koszyka')
			}
			else {
				alert('Wystąpił błąd');
			}
		});		
		return false;
	});

	$(".kUsunZKoszyka").click(function() {
		const $link = $(this);
		var url = $(this).attr('href');	
		const dodano = `<i class="fas fa-cart-plus"></i>`;
		
		$.post(url, function(resp) {
			if (resp === 'ok') {
				window.location.reload(true);
			}
			else {
				alert('Wystąpił błąd');
			}
		});		
		return false;
	});
});