const emailInput = document.getElementById("email-input");
const emailError = document.getElementById("email-error");
const submitBtnEmail = document.getElementById("to-order-products-card-order_btn-id");

function validateEmptyLine() {
  if (emailInput.value === "") {
    emailInput.style.borderColor = "#DB4444";
    emailError.innerHTML = "Введіть email";
    return false;
  }
  return true;
}

function validateEmailInput() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (emailInput.value === "") {
      emailInput.style.borderColor = "initial";
      emailError.innerHTML = "";
      return false;
    } else if (!emailRegex.test(emailInput.value)) {
      emailInput.style.borderColor = "#DB4444";
      emailError.innerHTML = "Наприклад: oleksandr@gmail.com";
      return false;
    } else {
      emailInput.style.borderColor = "#32B26A";
      emailError.innerHTML = "";
      return true;
    }
  }
  
  

emailInput.addEventListener("input", function () {
  validateEmailInput();
});

emailInput.addEventListener("focus", function () {
    if (emailInput.value !== "") {
      validateEmailInput();
    }
    emailInput.style.borderColor = "initial";
    emailError.innerHTML = "";
  });
  

emailInput.addEventListener("blur", function () {
  if (emailInput.value === "") {
    emailInput.style.borderColor = "initial";
  } else {
    validateEmailInput();
  }
});

submitBtnEmail.addEventListener("click", function (event) {
  event.preventDefault();
  if (validateEmailInput() && validateEmptyLine()) {
    // Виконуємо дії при правильно введених даних
  } else {
    validateEmptyLine();
  }
});
