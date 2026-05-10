// відкриття кошика
const openPopupButton = document.querySelector('.header__cart');
const popupContainer = document.querySelector('.popup');
const closePopupButtons = document.querySelectorAll('.close-popup, .btn-no_product_card-popup');

const openPopup = () => {
  popupContainer.style.display = 'block';
  document.body.style.overflow = 'hidden';
  localStorage.setItem('popupOpen', 'true');
  alert();
};

const closePopup = () => {
  popupContainer.style.display = 'none';
  document.body.style.overflow = 'auto';
  localStorage.setItem('popupOpen', 'false');
};

openPopupButton.addEventListener('click', openPopup);

closePopupButtons.forEach(button => {
  button.addEventListener('click', closePopup);
});

document.addEventListener('DOMContentLoaded', () => {
  const popupOpen = localStorage.getItem('popupOpen');
  if (popupOpen === 'true') {
    openPopup();
  } else {
    closePopup();
  }
});


// видаляємо товар з кошика
const hideBtns = document.querySelectorAll('.basket-popup');
const myDivs = document.querySelectorAll('.product_card-popup');
const orderDiv = document.querySelector('.order');
const noProductDiv = document.querySelector('.no_product_card-popup');
const noProductBtn = document.querySelector('.btn-no_product_card-popup');

const hideDiv = (div) => {
  div.classList.add('hidden');
  localStorage.setItem('productHidden' + div.dataset.index, 'true');

  if (document.querySelectorAll('.product_card-popup:not(.hidden)').length === 0) {
    orderDiv.style.display = 'none';
    noProductDiv.style.display = 'block';
    noProductBtn.style.display = 'block';
  }
};

hideBtns.forEach((hideBtn, i) => {
  hideBtn.addEventListener('click', () => {
    const divIndex = hideBtn.closest('.product_card-popup').dataset.index;
    hideDiv(myDivs[divIndex]);
  });
});

document.addEventListener('DOMContentLoaded', () => {
  myDivs.forEach((div, i) => {
    const productHidden = localStorage.getItem('productHidden' + i);
    if (productHidden === 'true') {
      hideDiv(div);
    } else {
      div.dataset.index = i;
    }
  });

  if (document.querySelectorAll('.product_card-popup:not(.hidden)').length === 0) {
    orderDiv.style.display = 'none';
    noProductDiv.style.display = 'block';
    noProductBtn.style.display = 'block';
  }
});


// фіктивний розмір в кінці попапа для кнопки оформленя замовлення
const content = document.querySelector('.popup');
const fakeElement = document.createElement('div');
fakeElement.style.height = '200px';
content.appendChild(fakeElement);

// для коректного відображення кнопки оформленя замовлення при видалені товарів з корзини
const productsContainer = document.querySelector('.popup');
const buyButton = document.querySelector('.order');
productsContainer.addEventListener('scroll', function() {
const productCount = this.children.length;
const containerHeight = this.getBoundingClientRect().height;
const buttonHeight = buyButton.getBoundingClientRect().height;
const buttonTop = containerHeight - buttonHeight - (productCount * 20);
buyButton.style.top = buttonTop + 'px';
});

// отримуємо масив елементів з класом 'products_card-popup'
const products = document.querySelectorAll('.product_card-popup');

// перевіряємо, чи існує хоча б один елемент
if (products.length === 0) {
  console.log("є");
  // отримуємо елемент з класом 'order' та ховаємо його
  const orderDiv = document.querySelector('.order');
  orderDiv.style.display = 'none';
}
else {
  console.log("немає");
  // отримуємо елемент з класом 'order' та ховаємо його
 
}
