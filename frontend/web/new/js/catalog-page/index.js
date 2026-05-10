const sortingPopUp = document.getElementById('sorting-popup');
const sortingArrow = document.getElementById('sort-arrow');
const filter = document.getElementById('filter-mobile');
const filterSettings = document.getElementById('filter-settings');
const navBlock = document.querySelector('.navigation-block');
const buttonsOfNav = document.querySelector('.sort-titles-block__button-container');
const sortTitleP = document.querySelector('.sort-titles-block p');
const unCheckBoxAll = () => {
	const filterBlock = document.querySelector('.filter-block-mobile');
	const checkboxesOfFilterBlock = filterBlock.querySelectorAll('input[type="checkbox"]');
	checkboxesOfFilterBlock.forEach((checkbox) => {
		checkbox.checked = false;
	});
};
const openSort = () => {
	sortingPopUp.classList.toggle('hidden');
	sortingArrow.classList.toggle('rotate');
};
const openFilter = () => {
	filter.classList.toggle('hidden');
	navBlock.classList.toggle('hidden');
	buttonsOfNav.classList.toggle('hidden');
	sortTitleP.classList.toggle('hidden');
	filterSettings.classList.toggle('hidden');
};

sortingPopUp.addEventListener('mouseleave', () => {
	sortingPopUp.classList.toggle('hidden');
	sortingArrow.classList.toggle('rotate');
});