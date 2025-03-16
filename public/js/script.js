document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            if (!confirm('Вы уверены, что хотите удалить сотрудника?')) {
                e.preventDefault();
            }
        });
    });
});