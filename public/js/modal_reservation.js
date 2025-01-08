document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reservation-modal');
    const openModal = document.querySelector('[data-modal-toggle="reservation-modal"]');
    const closeModal = document.querySelector('[data-modal-toggle="close-reservation-modal"]');

    if( modal && openModal && closeModal) {
    openModal.addEventListener('click', function (e) {
    e.preventDefault();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    document.body.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    });

    closeModal.addEventListener('click', function (e) {
    e.preventDefault();
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
    document.body.style.backgroundColor = '';
    });
}
})