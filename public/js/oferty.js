$(function() {	
	$(".aDodajDoKoszyka").click(function() {
		const $link = $(this);
		var url = $(this).attr('href');
		
		$.post(url, function(resp) {
			if (resp === 'ok') {
				var count = Number($("#liczba_ofert").html());
				
				if ($link.attr('class') === "aDodajDoKoszyka") {
					$link.html('<i class="fas fa-check-circle text-success"></i>')
					url = url.split('/');
					url[url.indexOf("dodaj")] = "usun";
					url = url.join("/");
					$link.attr('href', url);
					$link.attr('title', "Usuń z koszyka");
					$link.attr('class', "aUsunZKoszyka");
					++count;
				}
				else {
					$link.html('<i class="fas fa-cart-plus"></i>')
					url = url.split('/');
					url[url.indexOf("usun")] = "dodaj";
					url = url.join("/");
					$link.attr('href', url)
					$link.attr('title', "Dodaj do koszyka");
					$link.attr('class', "aDodajDoKoszyka");
					--count;
					alert('Usunięto produkt z koszyka')
				}
				$("#liczba_ofert").html(count);
			}else if(resp === 'nope'){
				$link.html('<i class="fas fa-check-circle text-success"></i>')
				url = url.split('/');
				url[url.indexOf("dodaj")] = "usun";
				url = url.join("/");
				$link.attr('href', url);
				$link.attr('title', "Usuń z koszyka");
				$link.attr('class', "aUsunZKoszyka");
				alert('Ten produkt znajduje się w koszyku');
			}
			else {
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