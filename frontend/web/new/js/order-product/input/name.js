const nameInput = document.getElementById("name-input");
const nameError = document.getElementById("name-error");
const submitBtn = document.getElementById("to-order-products-card-order_btn-id");

function validateЕmptyLine() {
  if (nameInput.value === "") {
    nameInput.style.borderColor = "#DB4444";
    nameError.innerHTML = "Введіть ім'я";
    return false;
  }
  return true;
}

function validateNameInput() {
  if (!/^[a-zA-Zа-яА-ЯёЁІі]*$/.test(nameInput.value)) {
    nameInput.style.borderColor = "#DB4444";
    if (nameInput.value === "") {
      nameError.innerHTML = "Введіть ім'я";
    } else {
      nameError.innerHTML = "Некоректне ім'я";
    }
    return false;
  } else {
    nameInput.style.borderColor = "#32B26A"; // Зелена обводка
    nameError.innerHTML = "";
    return true;
  }
}

// Перевірка при введенні символу
nameInput.addEventListener("input", function () {
  validateNameInput();
});

// Очищення помилки при фокусі на полі вводу
nameInput.addEventListener("focus", function () {
  if (nameInput.value !== "") {
    validateNameInput();
  }
  nameInput.style.borderColor = "initial";
  nameError.innerHTML = "";
});

// Видалення зеленої обводки при виході з поля вводу
nameInput.addEventListener("blur", function () {
  validateNameInput();
  if (nameInput.value === "") {
    nameInput.style.borderColor = "initial";
  }
});

// Перевірка при натисканні на кнопку
submitBtn.addEventListener("click", function (event) {
  event.preventDefault();
  if (validateNameInput() && validateЕmptyLine()) {
    // Виконуємо дії при правильно введених даних
  } else {
    validateЕmptyLine();
  }
});

/*Видаляємо карточки товарів*/

// Отримуємо всі карточки товарів
const productCards = document.querySelectorAll('.product-card_order');
const removedCards = JSON.parse(localStorage.getItem('removedCards')) || [];

// Видаляємо картки товарів, які були видалені у попередні відвідування сторінки
for (let i = 0; i < productCards.length; i++) {
  const productId = productCards[i].dataset.productId;
  if (removedCards.includes(productId)) {
    productCards[i].remove();
  }
}

// Обробник кліка на кнопці закриття картки товару
function handleCloseClick() {
  const parentCard = this.closest('.product-card_order');
  const productId = parentCard.dataset.productId;

  // Додаємо ідентифікатор віддаленої картки товару до масиву
  removedCards.push(productId);
  localStorage.setItem('removedCards', JSON.stringify(removedCards));

  // Видаляємо карточку
  parentCard.remove();

  // Показуємо блок no_product_card-order-product, якщо всі карточки були видалені
  if (document.querySelectorAll('.product-card_order').length === 0) {
    document.querySelector('.no_product_card-order-product').style.display = 'block';
  }
}

// Додаємо обробник на кожну кнопку закриття картки товару
for (let i = 0; i < productCards.length; i++) {
  const closeBtn = productCards[i].querySelector('.close-product-card_order');
  closeBtn.addEventListener('click', handleCloseClick);
}

// Показуємо блок no_product_card-order-product, якщо всі карточки були видалені
if (document.querySelectorAll('.product-card_order').length === 0) {
  document.querySelector('.no_product_card-order-product').style.display = 'block';
}

var element = document.getElementById('phone');
var maskOptions = {
  mask: '+380(00)00-00-000',
  lazy: false
} 
var mask = new IMask(element, maskOptions);

var phoneInput = document.getElementById('phone');
phoneInput.addEventListener('input', function () {
  var phoneNumber = phoneInput.value.replace(/[^\d]/g, '');
  if (phoneNumber.length === 12) {
    phoneInput.style.border = '1px solid #32B26A';
  } else {
    phoneInput.style.border = '';
  }
});

var phoneInput = document.getElementById('phone');
var phoneError = document.getElementById('phone-error');
phoneInput.addEventListener('input', function () {
  var phoneNumber = phoneInput.value.replace(/[^\d]/g, '');
  if (phoneNumber.length === 12) {
    phoneInput.style.border = '1px solid #32B26A';
    phoneError.innerText = '';
  } else {
    phoneInput.style.border = '';
    phoneError.innerText = '';
  }
});

var orderBtnPhone = document.getElementById('to-order-products-card-order_btn-id');
orderBtnPhone.addEventListener('click', function () {
  var phoneNumber = phoneInput.value.replace(/[^\d]/g, '');
  if (phoneNumber === '') {
    phoneError.innerText = 'Введіть телефон';
    phoneInput.style.border = '1px solid red';
    phoneInput.focus();
  } else if (!phoneNumber.match(/^\d{12}$/)) {
    phoneError.innerText = 'Некоректний телефон';
    phoneInput.style.border = '1px solid red';
    phoneInput.focus();
  } else {
    phoneError.innerText = '';
    // Введено коректний телефон
    // Виконуємо інші дії
  }
});
