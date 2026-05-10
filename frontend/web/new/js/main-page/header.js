const popUp = document.getElementById('header_popup');
const arrow = document.getElementById('header__catalog-arrow');
const menu = document.getElementById('mobile__menu');
const liItems = document.querySelectorAll('.header__popup-li');

const thirdPopups = document.querySelectorAll('.header__popup-third');
thirdPopups.forEach((popup) => {
	if (!popup.classList.contains('hidden')) {
		popup.classList.add('hidden');
	}
});
/*
const subpopItems = document.querySelectorAll('.header__popup-subpop li');
subpopItems.forEach((item) => {
    item.addEventListener('click', (event) => {
        const overflowElement = event.target.closest('.header__popup-subpop');
        const popup = event.target.querySelector('.header__popup-third');
        overflowElement.style.overflowY = 'unset';
        if (popup) {
                popup.addEventListener('mouseleave', () => {
                        popup.classList.add('hidden');
                        overflowElement.style.overflowY = 'scroll';
                });

                thirdPopups.forEach((popup) => popup.classList.add('hidden'));
                popup.classList.remove('hidden');
        }
    });
});*/

document.addEventListener('click', (event) => {
	// Check if click was outside of subPopUp element
	if (!event.target.closest('.header__popup-subpop')) {
		// Remove active classes and hide all subPopUp elements
		liItems.forEach((liItem) => {
			liItem.classList.remove('header__popup-li-active');
			const subPopUp = liItem.querySelector('.header__popup-subpop');
			if (subPopUp) {
				subPopUp.classList.add('hidden');
			}
		});
	}
});

for (let i = 0; i < liItems.length; i++) {
	const liItem = liItems[i];
	const subPopUp = liItem.querySelector('.header__popup-subpop');
	if (subPopUp) {
		liItem.addEventListener('click', (event) => {
			// Stop event propagation to prevent it from triggering the document click handler
			event.stopPropagation();
			// Remove active classes and hide all subPopUp elements
			liItems.forEach((liItem) => {
				liItem.classList.remove('header__popup-li-active');
				const subPopUp = liItem.querySelector('.header__popup-subpop');
				if (subPopUp) {
					subPopUp.classList.add('hidden');
				}
			});
			// Add active class and show clicked subPopUp element
			subPopUp.classList.remove('hidden');
			liItem.classList.add('header__popup-li-active');
		});
	}
}

const openPopUp = () => {
	popUp.classList.toggle('hidden');
	arrow.classList.toggle('rotate');
};
popUp.addEventListener('mouseleave', () => {
	popUp.classList.toggle('hidden');
	arrow.classList.toggle('rotate');
});
const openMenu = () => {
	menu.classList.toggle('hidden');
};
