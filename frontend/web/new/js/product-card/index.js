// const showMore = () => {
// 	const moreColors = document.querySelector('.show-more-colors');
// 	const showMoreBtn = document.getElementById('show-more-btn');
// 	moreColors.classList.toggle('show-more-colors-active');
// 	const innerBtnText = showMoreBtn.textContent === 'Більше' ? 'Приховати' : 'Більше';
// 	showMoreBtn.textContent = innerBtnText;
// };
const changeDescription = () => {
	const button1 = document.getElementById('description-btn');
	const button2 = document.getElementById('characteristics-btn');

	const characteristics = document.getElementById('description__characteristics');
	const description = document.getElementById('description__exposition');
	button1.classList.toggle('active-btn');
	characteristics.classList.toggle('hidden');
	button2.classList.toggle('active-btn');
	description.classList.toggle('hidden');
};
const changeDescription2 = () => {
	const button1 = document.getElementById('description-btn-2');
	const button2 = document.getElementById('characteristics-btn-2');
	const characteristics = document.getElementById('description__characteristics-2');
	const description = document.getElementById('description__exposition-2');
	button1.classList.toggle('active-btn');
	characteristics.classList.toggle('hidden');
	button2.classList.toggle('active-btn');
	description.classList.toggle('hidden');
};
