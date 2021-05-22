const mefCarousels = document.querySelectorAll('.mef-carousel')

for (i = 0; i < mefCarousels.length; i++) {
  const mefCarousel = mefCarousels[i]
  const navigation = document.createElement('div')
  const carouselItems = mefCarousel.childElementCount
  navigation.className = 'mef-carousel-selector'
  const parent = mefCarousel.parentElement
  let timeout

  const setActive = index => {
    for (let k = 0; k < mefCarousel.childNodes.length; k++) {
      const element = mefCarousel.childNodes[k]
      const navitem = navigation.childNodes[k]
      if (k === index) {
        element.classList.add('active')
        navitem.classList.add('active')
      } else {
        element.classList.remove('active')
        navitem.classList.remove('active')
      }
    }
    clearTimeout(timeout)
    timeout = setTimeout(() => setActive((index + 1) % carouselItems), 5000)
  }

  for (let j = 0; j < mefCarousel.childNodes.length; j++) {
    const element = mefCarousel.childNodes[j]
    console.log(element)

    const navitem = document.createElement('span')
    navitem.className = 'mef-carousel-selector-item'
    navigation.appendChild(navitem)
    navitem.addEventListener('click', () => {
      setActive(j)
    })
  }

  parent.appendChild(navigation)
  setActive(0)
}
