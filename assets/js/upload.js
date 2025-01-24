// Fonction pour ajouter un nouveau champ de type fichier
function addFileInput() {
    var fileInputContainer = document.getElementById('file-input-container');
    var newFileInput = document.createElement('input');
    newFileInput.type = 'file';
    newFileInput.name = 'files[]';
    fileInputContainer.appendChild(newFileInput);
}