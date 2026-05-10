try {
	var swiper1 = new Swiper('.swiper-1', {
		// Optional parameters
		direction: 'horizontal',
		loop: true,
		slidesPerView: 2,
		speed: 800,
		// If we need pagination
		pagination: {
			el: '.swiper-pagination',
		},

		// Navigation arrows
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	});
	var swiper2 = new Swiper('.swiper-2', {
		// Optional parameters
		direction: 'horizontal',
		loop: true,
		speed: 800,
		// If we need pagination
		pagination: {
			el: '.swiper-pagination',
		},
		// Navigation arrows
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		breakpoints: {
			600: {
				slidesPerView: '1',
				slidesOffsetAfter: 0,
			},
			1040: {
				slidesPerView: 2,
			},
		},
	});
} catch (e) {}
var swiper3 = new Swiper('.swiper-3', {
	direction: 'horizontal',
	loop: true,
	speed: 800,
	// If we need pagination
	pagination: {
		el: '.swiper-pagination',
	},
	// Navigation arrows
	navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev',
	},
	breakpoints: {
		349: {
			slidesPerView: 2,
			spaceBetween: 10,
		},
		1040: {
			spaceBetween: 40,
			slidesPerView: 4,
		},
	},
});
