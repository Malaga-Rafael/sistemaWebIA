function activarPrevisualizacion (inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const container = input.closest('.campo');

        if (!container) return;

        // Eliminar previsualizacion anterior
        const oldPreview = container.querySelector('.preview-img');
        if (oldPreview) oldPreview.remove();

        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = document.createElement('img');
                img.src = event.target.result;
                img.classList.add('preview-img');
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

    });
}