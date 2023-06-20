(function ($) {
    $('#btn-search-cpf-numbers').on('click', function (e) {
        e.preventDefault();
        const cpf= $('#search-cpf-val').val();
        $('#cpf-numbers-search-result').html(cpf);
    });
}(jQuery))