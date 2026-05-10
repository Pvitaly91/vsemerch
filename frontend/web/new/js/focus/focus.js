const buttons = document.querySelectorAll('.review-block__colors, .product-description__size, .review-block__image-alternatives, .products-block__colors, .header__search__input');
let focusedButton = null;

buttons.forEach(button => {
  button.addEventListener('focus', () => {
    focusedButton = button;
  });
});
/*
document.addEventListener('mousedown', event => {
  if (!event.target.closest('.review-block__colors, .product-description__size, .review-block__image-alternatives, .products-block__colors, .header__search__input')) {
    event.preventDefault();
    focusedButton?.focus();
  } else {
    focusedButton = event.target.closest('.review-block__colors, .product-description__size, .review-block__image-alternatives, .products-block__colors, .header__search__input');
  }
});*/

document.addEventListener('keydown', event => {
  if (event.key === 'Tab') {
    event.preventDefault();
    let index = buttons.indexOf(focusedButton);
    if (index < 0) {
      index = 0;
    } else {
      index = (index + 1) % buttons.length;
    }
    focusedButton = buttons[index];
    focusedButton.focus();
  }
});
