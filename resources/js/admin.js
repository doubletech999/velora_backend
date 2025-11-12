document.addEventListener("DOMContentLoaded", function() {
    console.log("admin.js loaded");

    // Example: Handle clicks on a generic button class
    document.addEventListener("click", function(event) {
        if (event.target.matches(".add-button")) {
            alert("Add button clicked!");
            // Add your logic for the Add button here
        }
        if (event.target.matches(".view-button")) {
            console.log("View button clicked for ID: " + event.target.dataset.id + " and type: " + event.target.dataset.type);
            // Add your logic for the View button here
        }
        if (event.target.matches(".edit-button")) {
            console.log("Edit button clicked for ID: " + event.target.dataset.id + " and type: " + event.target.dataset.type);
            // Add your logic for the Edit button here
        }
        if (event.target.matches(".delete-button")) {
            console.log("Delete button clicked for ID: " + event.target.dataset.id + " and type: " + event.target.dataset.type);
            // Add your logic for the Delete button here
        }
    });
});