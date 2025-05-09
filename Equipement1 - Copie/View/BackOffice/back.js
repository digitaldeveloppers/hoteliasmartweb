class ServiceFormValidator {
    constructor() {
        this.formErrors = {};
        this.modal = document.getElementById('edit-service-modal');
        this.form = document.getElementById('edit-form');
        this.fields = ['title', 'quantity', 'price', 'description'];
        this.init();
    }

    init() {
        this.initEditButtons();
        this.initModalControls();
        this.initFormValidation();
    }

    validateTitle(value) {
        const trimmedValue = value.trim();
        if (trimmedValue.length < 3) {
            return "Le titre doit contenir au moins 3 caractères";
        }
        if (!/^[a-zA-ZÀ-ÿ0-9\s\-_]+$/.test(trimmedValue)) {
            return "Le titre ne peut contenir que des lettres, chiffres, espaces, tirets et underscores";
        }
        return "";
    }

    validateQuantity(value) {
        const numValue = parseInt(value);
        if (!value || isNaN(numValue) || numValue <= 0) {
            return "La quantité doit être un nombre entier positif";
        }
        return "";
    }

    validatePrice(value) {
        const numValue = parseFloat(value);
        if (!value || isNaN(numValue) || numValue <= 0) {
            return "Le prix doit être un nombre positif";
        }
        if (!/^\d+(\.\d{0,2})?$/.test(value)) {
            return "Le prix doit avoir au maximum 2 décimales";
        }
        return "";
    }

    validateDescription(value) {
        const trimmedValue = value.trim();
        if (trimmedValue.length < 10) {
            return "La description doit contenir au moins 10 caractères";
        }
        if (trimmedValue.length > 500) {
            return "La description ne doit pas dépasser 500 caractères";
        }
        return "";
    }

    showError(inputId, message) {
        const input = document.getElementById(inputId);
        if (!input) return;

        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.color = 'red';
            errorDiv.style.fontSize = '12px';
            errorDiv.style.marginTop = '5px';
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }

        errorDiv.textContent = message;
        input.style.borderColor = message ? 'red' : '';
        input.style.transition = 'border-color 0.3s ease';
    }

    initFormValidation() {
        this.fields.forEach(field => {
            const input = document.getElementById(`edit-${field}`);
            if (input) {
                input.addEventListener('input', () => {
                    const error = this[`validate${field.charAt(0).toUpperCase() + field.slice(1)}`](input.value);
                    this.formErrors[field] = error;
                    this.showError(`edit-${field}`, error);
                });
            }
        });

        this.form?.addEventListener('submit', (event) => this.handleSubmit(event));
    }

    handleSubmit(event) {
        this.fields.forEach(field => {
            const input = document.getElementById(`edit-${field}`);
            if (input) {
                const error = this[`validate${field.charAt(0).toUpperCase() + field.slice(1)}`](input.value);
                this.formErrors[field] = error;
                this.showError(`edit-${field}`, error);
            }
        });

        if (Object.values(this.formErrors).some(error => error !== "")) {
            event.preventDefault();
        }
    }

    initEditButtons() {
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', () => this.handleEditClick(button));
        });
    }

    handleEditClick(button) {
        const serviceData = {
            id: button.getAttribute('data-id'),
            title: button.getAttribute('data-title'),
            quantity: button.getAttribute('data-quantity'),
            price: button.getAttribute('data-price'),
            description: button.getAttribute('data-description')
        };

        this.populateForm(serviceData);
        this.clearErrors();
        this.showModal();
    }

    populateForm(data) {
        document.getElementById('service_id').value = data.id;
        this.fields.forEach(field => {
            const input = document.getElementById(`edit-${field}`);
            if (input) {
                input.value = data[field] || '';
            }
        });
    }

    clearErrors() {
        this.formErrors = {};
        this.fields.forEach(field => {
            this.showError(`edit-${field}`, '');
        });
    }

    initModalControls() {
        const closeButton = document.querySelector('.close-modal');
        const cancelButton = document.getElementById('cancel-edit');

        closeButton?.addEventListener('click', (e) => this.handleModalClose(e));
        cancelButton?.addEventListener('click', (e) => this.handleModalClose(e));

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.handleModalClose(e);
            }
        });
    }

    handleModalClose(event) {
        event.preventDefault();
        this.hideModal();
        this.clearErrors();
    }

    showModal() {
        if (this.modal) {
            this.modal.style.display = 'block';
        }
    }

    hideModal() {
        if (this.modal) {
            this.modal.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ServiceFormValidator();
});


// Function to open a modal
function openModal(modal) {
    modal.style.display = 'block';
}

// Function to close a modal
function closeModal(modal) {
    modal.style.display = 'none';
}
// Event listeners for closing modals
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        closeModal(btn.closest('.modal'));
    });
});
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target);
    }
});



document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-song');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the song ID and type from the data-id and data-type attributes
            const songId = button.getAttribute('data-id');
            const type = button.getAttribute('data-type');

            // Show the delete modal
            const modal = document.getElementById('delete-modal');
            const deleteSongIdInput = document.getElementById('delete-song-id');
            const typetableInput = document.getElementsByClassName('type_c'); // Ensure this element exists

            // Set the song ID and type in the hidden input fields
            deleteSongIdInput.value = songId;
            typetableInput.value = type;

            // Display the modal
            modal.style.display = 'block';
        });
    });

    // Add event listener for the cancel button
    document.getElementById('cancel-delete').addEventListener('click', function() {
        // Close the modal
        document.getElementById('delete-modal').style.display = 'none';
    });

    // Add event listener for the confirm delete button
    document.getElementById('confirm-delete').addEventListener('click', function() {
        // Get the song ID and type values from the modal's hidden input fields
        const songId = document.getElementById('delete-song-id').value;
        const type = document.getElementsByClassName('type_c').value;

        console.log('Deleting song with ID:', songId, 'and type:', type);
        // If you're using a GET request for the deletion
        window.location.href = 'deletepayment.php?song_id=' + songId + '&type=' + type;
    });
});



// Function to open a modal
function openModal(modal) {
    modal.style.display = 'block';
}

// Function to close a modal
function closeModal(modal) {
    modal.style.display = 'none';
}
// Event listeners for closing modals
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        closeModal(btn.closest('.modal'));
    });
});
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target);
    }
});


document.addEventListener("DOMContentLoaded", function () {
    let editButtons = document.querySelectorAll(".edit-paiment-song");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            let paimentId = this.getAttribute("data-id");
            let paimentDate = this.getAttribute("data-date");

            console.log(paimentId, paimentDate);

            document.getElementById("song_paiment_id").value = paimentId;
            
          
            
            let dateOnly = paimentDate.split(" ")[0]; // "2025-04-30"
            document.getElementById("edit-date-title").value = dateOnly;

            document.getElementById("edit-date-title").value = dateOnly;

            document.getElementById("edit-paiment-modal").style.display = "block";
        });
    });

    // Close modal actions
    document.querySelector(".close-modal").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paiment-modal").style.display = "none";
    });

    document.getElementById("cancel-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paiment-modal").style.display = "none";
    });

    // Optional: close modal on outside click
    window.addEventListener("click", function (event) {
        let modal = document.getElementById("edit-paiment-modal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Optional: close modal on Escape key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            document.getElementById("edit-paiment-modal").style.display = "none";
        }
    });
});




document.addEventListener("DOMContentLoaded", function () {
    // Get all edit buttons
    let editButtons = document.querySelectorAll(".edit-paimentc-song");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get payment details from the button's data attributes
            let id_carte = this.getAttribute("data-id");
            let type_carte = this.getAttribute("data-carte"); // Get the payment method type
            let numero_carte = this.getAttribute("data-numero");
            let date_expiration = this.getAttribute("data-expiration");
            let type = this.getAttribute("data-type");

            console.log("Selected Payment Method: ", type_carte);

            console.log(type_carte , type);  // Check that the value is logged correctly

            // Populate the modal fields
            document.getElementById("song_paimentc_id").value = id_carte;
            document.getElementById("edit-carte-title").value = numero_carte;
            document.getElementById('edit-carte-type').value = type_carte;  // Set the value of the payment method dropdown
            document.getElementById('edit-datec-title').value = date_expiration;
            document.getElementById('type_paimentc').value = type;

            // Show the modal
            document.getElementById("edit-paimentc-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector(".close-modal").addEventListener("click", function () {
        event.preventDefault();
        document.getElementById("edit-paimentc-modal").style.display = "none";  // Ensure modal ID matches
    });

    // Cancel button closes the modal
    document.getElementById("cancel-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paimentc-modal").style.display = "none";  // Ensure modal ID matches
    });
});


document.addEventListener("DOMContentLoaded", function () {
    // Get all mobile edit buttons
    let editMobileButtons = document.querySelectorAll(".edit-paimentmobile-song");

    editMobileButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get payment details from the button's data attributes
            let id_mobile = this.getAttribute("data-id");
            let provider = this.getAttribute("data-provider");
            let numero_mobile = this.getAttribute("data-numero");
            let date_exp = this.getAttribute("date_exp");
            let type = this.getAttribute("data-type");

            console.log("Selected Mobile Provider: ", provider);
            console.log ("Selected Mobile Type: ", type);  // Check that the value is logged correctly
            console.log(id_mobile, provider, numero_mobile, date_exp);  // Check that the value is logged correctly

            // Populate the modal fields
            document.getElementById("song_paimentmobile_id").value = id_mobile;
            document.getElementById("edit-mobile-title").value = numero_mobile;
            document.getElementById("edit-mobile-type").value = provider;
            document.getElementById("edit-mobile").value = date_exp;
            document.getElementById("type_mobile").value = type;

            // Show the modal
            document.getElementById("edit-paimentmobile-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector("#edit-paimentmobile-modal .close-modal").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paimentmobile-modal").style.display = "none";
    });

    // Cancel button closes the modal
    document.getElementById("cancel-mobile-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paimentmobile-modal").style.display = "none";
    });
});





const tabs = document.querySelectorAll(".tab-link");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            // Supprimer 'active' de tous
            tabs.forEach(t => t.classList.remove("active"));
            contents.forEach(c => c.classList.remove("active"));

            // Ajouter 'active' sur le tab cliqué
            tab.classList.add("active");
            document.getElementById(tab.getAttribute("data-tab")).classList.add("active");
        });
    });


      
    document.addEventListener('DOMContentLoaded', function() {
        const tabs   = document.querySelectorAll('.tab-link');
        const panels = document.querySelectorAll('.tab-content');
    
        tabs.forEach(tab => {
          tab.addEventListener('click', () => {
            // 1. Deactivate all tabs & hide all panels
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
    
            // 2. Activate clicked tab
            tab.classList.add('active');
    
            // 3. Show matching panel
            const panelId = tab.dataset.tab;            // e.g. "paimentc"
            document.getElementById(panelId)
                    .classList.add('active');
          });
        });
      });



      document.addEventListener('DOMContentLoaded', () => {
        const tabs       = document.querySelectorAll('.tab-link');
        const panels     = document.querySelectorAll('.tab-content');
        const searchBox  = document.querySelector('.search-input');
      
        // Tab switching
        tabs.forEach(tab => {
          tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
      
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
      
            // Clear search when switching tabs
            searchBox.value = '';
            filterAndHighlight('');
          });
        });
      
        // Search filtering + highlight
        searchBox.addEventListener('input', e => {
          filterAndHighlight(e.target.value.trim().toLowerCase());
        });
      
        function filterAndHighlight(query) {
          const visible = document.querySelector('.tab-content.active');
          if (!visible) return;
          const rows = visible.querySelectorAll('table.song-table tbody tr');
      
          rows.forEach(row => {
            // Reset
            row.style.display = '';
            row.querySelectorAll('td').forEach(cell => {
              cell.textContent = cell.textContent;
            });
      
            if (!query) return;
      
            // Check & highlight
            let match = false;
            row.querySelectorAll('td').forEach(cell => {
              const text = cell.textContent;
              if (text.toLowerCase().includes(query)) {
                match = true;
                const regex = new RegExp(`(${query})`, 'gi');
                cell.innerHTML = text.replace(regex, '<span class="highlight">$1</span>');
              }
            });
      
            if (!match) row.style.display = 'none';
          });
        }
      
        // Initialize
        document.querySelector('.tab-link.active').click();
      });
    
 
const sortSelect = document.getElementById("sort");
const table = document.querySelector("table");
const originalRows = Array.from(table.querySelectorAll("tr")).slice(1); // Save original rows

sortSelect.addEventListener("change", function () {
    const sortBy = this.value;

    const rows = Array.from(table.querySelectorAll("tr")).slice(1); // Skip header row
    const dataRows = rows.filter(row => row.querySelector("td")); // Only rows with data

    if (sortBy === "choisir") {

        // Clear current rows
        dataRows.forEach(row => row.remove());

        // Re-append original rows
        originalRows.forEach(row => table.appendChild(row.cloneNode(true)));

        return;
    }

    table.style.display = "table"; // Show table

    const colIndex = sortBy === "quantity" ? 2 : 3;

    dataRows.sort((a, b) => {
        const aVal = parseFloat(a.children[colIndex].textContent);
        const bVal = parseFloat(b.children[colIndex].textContent);
        return aVal - bVal;
    });

    dataRows.forEach(row => table.appendChild(row));
});


