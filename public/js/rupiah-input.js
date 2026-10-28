(function () {
    function formatRupiahInput(input) {
        const digits = input.value.replace(/\D/g, '');

        input.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
    }

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('rupiah-input')) {
            formatRupiahInput(event.target);
        }
    });

    document.addEventListener('submit', function (event) {
        event.target.querySelectorAll('.rupiah-input').forEach(function (input) {
            input.value = input.value.replace(/\D/g, '');
        });
    });

    document.querySelectorAll('.rupiah-input').forEach(formatRupiahInput);
})();
