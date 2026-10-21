document.addEventListener('DOMContentLoaded', () => {
  const printButton = document.getElementById('print-resume');
  if (!printButton) {
    return;
  }

  printButton.addEventListener('click', () => window.print());
  if (window.location.hash === '#print') {
    printButton.focus();
  }
});
