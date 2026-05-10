try {
	const showMoreSouvenir = document.getElementById('show-more-souvenir');
	const showMorePoligraph = document.getElementById('show-more-poligraph');
	const hiddenItemsPoligraph = document.querySelectorAll('.product-block__item-hidden');
	const hiddenItemsSouvenir = document.querySelectorAll('.product-block__item-hidden-souvenir');

	showMoreSouvenir.addEventListener('click', () => {
          
		for (let i = 0; i < hiddenItemsSouvenir.length; i++) {
		
				hiddenItemsSouvenir[i].classList.toggle('hidden');
				showMoreSouvenir.textContent = 'Приховати';
				if (hiddenItemsSouvenir[i].classList.contains('hidden')) {
					showMoreSouvenir.textContent = 'Більше';
				}
			
		}
	});
	showMorePoligraph.addEventListener('click', () => {
                const result = showMorePoligraph.textContent === 'Приховати' ? 'Більше' : 'Приховати';
          
		for (let i = 0; i < hiddenItemsPoligraph.length; i++) {
			
				
				hiddenItemsPoligraph[i].classList.toggle('hidden');
				showMorePoligraph.textContent = 'Приховати';
				showMorePoligraph.textContent = result;
			
		}
	});
} catch (e) {}
try {
	const hiddenColors = document.querySelectorAll('.review-block__colors-item-hidden');
	const showMoreColors = document.getElementById('show-more-colors');
	showMoreColors.addEventListener('click', () => {
		for (let i = 0; i < hiddenColors.length; i++) {
			
				hiddenColors[i].classList.toggle('hidden');
				showMoreColors.textContent = 'Приховати';
				if (hiddenColors[i].classList.contains('hidden')) {
					showMoreColors.textContent = 'Більше';
				}
			
		}
	});
	const hiddenColors2 = document.querySelectorAll('.review-block__colors-item-hidden-2');
	const showMoreColors2 = document.getElementById('show-more-colors-2');
	showMoreColors2.addEventListener('click', () => {
		for (let i = 0; i < hiddenColors2.length; i++) {
			
				hiddenColors2[i].classList.toggle('hidden');
				showMoreColors2.textContent = 'Приховати';
				if (hiddenColors2[i].classList.contains('hidden')) {
					showMoreColors2.textContent = 'Більше';
				}
			
		}
	});
} catch (e) {}
