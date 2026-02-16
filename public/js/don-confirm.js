document.addEventListener('DOMContentLoaded', function() {
    function attachConfirm(form) {
        form.addEventListener('submit', function(e) {
            var ok = confirm('Voulez-vous vraiment distribuer ce don ?');
            if (!ok) e.preventDefault();
        });
    }

    var forms = document.querySelectorAll('form.form-distribuer');
    forms.forEach(function(f) { attachConfirm(f); });
});
