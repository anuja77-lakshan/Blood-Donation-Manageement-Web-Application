document.addEventListener('DOMContentLoaded', () => {
  const fileInput = document.getElementById('coverImage');
  const dropzoneTitle = document.querySelector('.dropzone-title');
  const dropzoneHint = document.querySelector('.dropzone-hint');
  const dropzoneIcon = document.querySelector('.dropzone-icon');
  const dropzoneBox = document.querySelector('.file-dropzone');

  if (fileInput) {
    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        // Update icon to checkmark
        if (dropzoneIcon) {
          dropzoneIcon.className = 'fa-solid fa-circle-check dropzone-icon';
          dropzoneIcon.style.color = '#16a34a';
        }

        // Display selected file name
        if (dropzoneTitle) {
          dropzoneTitle.textContent = `Uploaded: ${file.name}`;
          dropzoneTitle.style.color = '#16a34a';
          dropzoneTitle.style.fontWeight = '700';
        }

        // Display file size
        if (dropzoneHint) {
          dropzoneHint.textContent = `File ready for upload (${fileSizeMB} MB) • Click to change`;
          dropzoneHint.style.color = '#374151';
        }

        // Highlight dropzone border
        if (dropzoneBox) {
          dropzoneBox.style.borderColor = '#16a34a';
          dropzoneBox.style.backgroundColor = '#f0fdf4';
        }
      }
    });
  }
});