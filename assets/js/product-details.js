// Функция для обновления данных при выборе размера
function updatePrice(price, availability, stock, element) {
  document.getElementById('price').innerText = price + ' грн'
  document.getElementById('availability').innerText = availability == 1 ? 'В наличии' : 'Нет в наличии'
  document.getElementById('stock').innerText = stock

  let buttons = document.querySelectorAll('.size-btn')
  buttons.forEach((btn) => btn.classList.remove('active'))
  element.classList.add('active')
}

// Функция для переключения групп размеров
function toggleSizeGroup(sizeType) {
  // Скрываем все группы кнопок
  document.querySelectorAll('.size-group').forEach((group) => {
    group.style.display = 'none'
  })

  // Показываем только соответствующую группу
  document.querySelectorAll('.size-group-' + sizeType).forEach((group) => {
    group.style.display = 'flex'
  })

  // Снимаем активность со всех кнопок
  let sizeButtons = document.querySelectorAll('.size-btn')
  sizeButtons.forEach((btn) => btn.classList.remove('active'))

  // Устанавливаем активную кнопку по умолчанию для 50*70, если это не 70*70
  if (sizeType === '50x70') {
    document.querySelector('.size-group-50x70 .size-btn').classList.add('active')
    document.querySelector('.size-btn-group-50x70').classList.add('active')
    document.querySelector('.size-btn-group-70x70').classList.remove('active')
  } else if (sizeType === '70x70') {
    document.querySelector('.size-group-70x70 .size-btn').classList.add('active')
    document.querySelector('.size-btn-group-70x70').classList.add('active')
    document.querySelector('.size-btn-group-50x70').classList.remove('active')
  }
}

// Устанавливаем активность кнопки 50*70 по умолчанию при загрузке страницы
window.onload = function () {
  toggleSizeGroup('50x70') // Устанавливаем 50*70 как активную
}
