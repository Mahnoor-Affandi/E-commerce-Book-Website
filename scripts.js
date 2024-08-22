// scripts.js

document.addEventListener('DOMContentLoaded', () => {
    const leftArrow = document.querySelector('.left-arrow');
    const rightArrow = document.querySelector('.right-arrow');
    const booksContainer = document.querySelector('.books-container');
    
    let scrollPosition = 0;
    const bookWidth = booksContainer.querySelector('.book').clientWidth;
    const visibleBooks = 7;
    const totalBooks = booksContainer.children.length;
    const maxScroll = (totalBooks - visibleBooks) * bookWidth;

    leftArrow.addEventListener('click', () => {
        if (scrollPosition > 0) {
            scrollPosition -= bookWidth;
            booksContainer.style.transform = `translateX(-${scrollPosition}px)`;
        }
    });

    rightArrow.addEventListener('click', () => {
        if (scrollPosition < maxScroll) {
            scrollPosition += bookWidth;
            booksContainer.style.transform = `translateX(-${scrollPosition}px)`;
        }
    });
});
