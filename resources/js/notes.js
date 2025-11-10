document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('add-notes');
    const modal = document.getElementById('notes-modal');
    const backdrop = document.getElementById('notes-modal-backdrop');
    const closeBtn = document.getElementById('notes-modal-close');
    const cancelBtn = document.getElementById('notes-cancel');
    const titleInput = document.getElementById('note-title');
    const contentInput = document.getElementById('note-content');

    function openModal() {
        modal.classList.remove('hidden');
        titleInput.value = '';
        contentInput.value = '';
        setTimeout(() => titleInput.focus(), 50);
    }
    function closeModal() {
        modal.classList.add('hidden');
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
});