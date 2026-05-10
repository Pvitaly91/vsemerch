const decrementButton = document.querySelectorAll('.del');
const incrementButton = document.querySelectorAll('.add');
const countSpan = document.querySelectorAll('.count_popup');
const priceSpan = document.querySelectorAll('.price-popup');
const totalPriceSpan = document.querySelectorAll('.total-price-popup');
const totalPriceWrapper = document.querySelector('.btn_price strong');

let totalPrice = 0;

// пройдемося по кожній кнопці "add" та "del"
for (let i = 0; i < incrementButton.length; i++) {
  const incrementBtn = incrementButton[i];
  const decrementBtn = decrementButton[i];

  // додамо обробник події для кнопки "add"
  incrementBtn.addEventListener("click", () => {
    let count = parseInt(countSpan[i].textContent);
    let price = parseFloat(priceSpan[i].textContent);
    let totalPriceItem = parseFloat(totalPriceSpan[i].textContent);

    count++;
    totalPriceItem += price;
    totalPrice += price;

    countSpan[i].textContent = count;
    totalPriceSpan[i].textContent = totalPriceItem.toFixed(2);
    totalPriceWrapper.textContent = totalPrice.toFixed(2);

    localStorage.setItem(`count${i}`, count);
    localStorage.setItem(`totalPrice${i}`, totalPriceItem);
    localStorage.setItem('totalPrice', totalPrice);
  });

  // додамо обробник події для кнопки "del"
  decrementBtn.addEventListener("click", () => {
    let count = parseInt(countSpan[i].textContent);
    let price = parseFloat(priceSpan[i].textContent);
    let totalPriceItem = parseFloat(totalPriceSpan[i].textContent);

    if (count > 1) {
      count--;
      totalPriceItem -= price;
      totalPrice -= price;

      countSpan[i].textContent = count;
      totalPriceSpan[i].textContent = totalPriceItem.toFixed(2);
      totalPriceWrapper.textContent = totalPrice.toFixed(2);

      localStorage.setItem(`count${i}`, count);
      localStorage.setItem(`totalPrice${i}`, totalPriceItem);
      localStorage.setItem('totalPrice', totalPrice);
    }
  });

  // встановимо значення кількості та загальної ціни з локального сховища при завантаженні сторінки
  let count = parseInt(localStorage.getItem(`count${i}`)) || 1;
  let totalPriceItem = parseFloat(localStorage.getItem(`totalPrice${i}`)) || parseFloat(totalPriceSpan[i].textContent) || 0; 

  countSpan[i].textContent = count;
  totalPriceSpan[i].textContent = totalPriceItem.toFixed(2);
  let price = parseFloat(priceSpan[i].textContent);
  totalPrice += totalPriceItem;
  totalPriceWrapper.textContent = totalPrice.toFixed(2);

  // update the localStorage for total price
  localStorage.setItem('totalPrice', totalPrice);
}

// update the total price from the localStorage on page load
let totalPriceFromStorage = parseFloat(localStorage.getItem('totalPrice')) || 0;
totalPriceWrapper.textContent = totalPriceFromStorage.toFixed(2);


