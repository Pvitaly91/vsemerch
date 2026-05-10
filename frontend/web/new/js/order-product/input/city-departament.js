// Відкриття випадающого списку міст
const inputNameDeliveryMethod = document.getElementById("input-name_delivery-method-city");
const listCityProductOrder = document.querySelector('.list-city-product_order');

inputNameDeliveryMethod.addEventListener('input', function() {
  if (this.value) {
    listCityProductOrder.classList.add('active');
  } else {
    listCityProductOrder.classList.remove('active');
  }
});

// Відкриття випадающого списку відділень НП
const inputNameDeliveryMethodSecond = document.getElementById("input-name_delivery-method-department");
const listDepartamentProductOrder = document.querySelector('.list-department-of-NP-product_order');

inputNameDeliveryMethodSecond.addEventListener('input', function() {
  if (this.value) {
    listDepartamentProductOrder.classList.add('active');
  } else {
    listDepartamentProductOrder.classList.remove('active');
  }
});


// Помилка пустого інпуту міста
const cityInput = document.getElementById("input-name_delivery-method-city");
const cityError = document.getElementById("city-error");
const orderBtn = document.getElementById("to-order-products-card-order_btn-id");

function validateCityInput() {
  if (cityInput.value.trim() === "") {
    cityInput.style.borderColor = "#DB4444";
    cityError.innerHTML = "Виберіть місто";
    return false;
  } else {
    cityInput.style.borderColor = "#32B26A";
    cityError.innerHTML = "";
    return true;
  }
}

orderBtn.addEventListener("click", function(event) {
  event.preventDefault();
  if (validateCityInput()) {
    
  }
});

cityInput.addEventListener("focus", function() {
    cityInput.style.borderColor = "";
    cityError.innerHTML = "";
  });
  
  // Помилка пустого інпуту відділень НП
  const departmentInput = document.getElementById("input-name_delivery-method-department");
  const departmentError = document.getElementById("department-error");
  const orderBtnDepartament = document.getElementById("to-order-products-card-order_btn-id");
  
  function validateDepartmentInput() {
    if (departmentInput.value.trim() === "") {
      departmentInput.style.borderColor = "#DB4444";
      departmentError.innerHTML = "Виберіть відділення Нової Пошти";
      return false;
    } else {
      departmentInput.style.borderColor = "#32B26A";
      departmentError.innerHTML = "";
      return true;
    }
  }
  
  orderBtnDepartament.addEventListener("click", function(event) {
    event.preventDefault();
    if (validateDepartmentInput()) {
      
    }
  });
  
  departmentInput.addEventListener("focus", function() {
      departmentInput.style.borderColor = "";
      departmentError.innerHTML = "";
  });
  